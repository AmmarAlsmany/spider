<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\contracts;
use App\Models\payments;
use App\Models\tikets;
use App\Traits\NotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    use NotificationDispatcher;

    public function dashboard()
    {
        $data = [
            'totalContracts' => contracts::count(),
            'activeContracts' => contracts::where('contract_status', 'active')->count(),
            'totalRevenue' => payments::sum('payment_amount'),
            'pendingPayments' => payments::where('payment_status', 'pending')->count(),
            'overduePayments' => payments::where('payment_status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
            'openTickets' => tikets::where('status', 'open')->count(),
            'recentPayments' => payments::with(['contract', 'customer', 'postponementRequests'])
                ->latest()
                ->take(10)
                ->get()
        ];

        return view('managers.finance.dashboard', $data);
    }

    public function invoices()
    {
        $invoices = payments::with(['contract', 'customer'])
            ->latest()
            ->paginate(10);
        return view('managers.finance.invoices.index', compact('invoices'));
    }

    public function showInvoice($id)
    {
        $invoice = payments::with(['contract', 'customer'])
            ->findOrFail($id);
        return view('managers.finance.invoices.show', compact('invoice'));
    }

    public function payments()
    {
        $payments = payments::with(['contract', 'customer'])
            ->latest()
            ->paginate(10);
        return view('managers.finance.payments.index', compact('payments'));
    }

    public function showPayment($id)
    {
        $payment = payments::with(['contract', 'customer'])
            ->findOrFail($id);
        return view('managers.finance.payments.show', compact('payment'));
    }

    public function generateFinancialReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $contractNumber = $request->input('contract_number');

        // Base query for payments with contract filter
        $paymentsQuery = payments::query()
            ->when($contractNumber, function($query) use ($contractNumber) {
                $query->whereHas('contract', function($q) use ($contractNumber) {
                    $q->where('contract_number', 'like', '%' . $contractNumber . '%');
                });
            });

        $report = [
            'total_revenue' => (clone $paymentsQuery)
                ->whereBetween('due_date', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->sum('payment_amount'),
            'pending_payments' => (clone $paymentsQuery)
                ->whereIn('payment_status', ['pending', 'unpaid'])
                ->whereBetween('due_date', [$startDate, $endDate])
                ->sum('payment_amount'),
            'overdue_amount' => (clone $paymentsQuery)
                ->where('payment_status', 'overdue')
                ->whereBetween('due_date', [$startDate, $endDate])
                ->sum('payment_amount'),
            'monthly_revenue' => (clone $paymentsQuery)
                ->whereMonth('due_date', Carbon::now()->month)
                ->where('payment_status', 'paid')
                ->sum('payment_amount'),
            'payment_history' => $paymentsQuery
                ->with(['contract', 'customer'])
                ->whereBetween('due_date', [$startDate, $endDate])
                ->orderBy('due_date', 'desc')
                ->paginate(15)
        ];

        return view('managers.finance.reports.financial', compact('report'));
    }

    private function calculateCollectionRate($startDate, $endDate)
    {
        $totalDue = payments::whereBetween('due_date', [$startDate, $endDate])->sum('payment_amount');
        $totalCollected = payments::whereBetween('due_date', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('payment_amount');

        return $totalDue > 0 ? ($totalCollected / $totalDue) * 100 : 0;
    }

    private function getMonthlyBreakdown($startDate, $endDate)
    {
        $months = [];
        $currentDate = Carbon::parse($startDate);
        
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            
            $months[] = [
                'month' => $currentDate->format('M Y'),
                'revenue' => payments::whereBetween('due_date', [$monthStart, $monthEnd])
                    ->where('payment_status', 'paid')
                    ->sum('payment_amount'),
                'pending' => payments::whereBetween('due_date', [$monthStart, $monthEnd])
                    ->whereIn('payment_status', ['pending', 'unpaid'])
                    ->sum('payment_amount'),
                'overdue' => payments::whereBetween('due_date', [$monthStart, $monthEnd])
                    ->where('payment_status', 'overdue')
                    ->sum('payment_amount')
            ];
            
            $currentDate->addMonth();
        }
        
        return $months;
    }

    private function getPaymentStatusDistribution($startDate, $endDate)
    {
        $statuses = ['paid', 'pending', 'unpaid', 'overdue'];
        $distribution = [];
        
        foreach ($statuses as $status) {
            $distribution[$status] = payments::whereBetween('due_date', [$startDate, $endDate])
                ->where('payment_status', $status)
                ->count();
        }
        
        return $distribution;
    }

    public function pendingPayments()
    {
        $pendingPayments = payments::with(['contract', 'customer'])
            ->whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        return view('managers.finance.payments.pending', compact('pendingPayments'));
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $payment = payments::findOrFail($id);
        $payment->payment_status = $request->status;
        $payment->save();

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    // Payment Processing Methods
    public function recordPayment(Request $request, $id)
    {
        $payment = payments::findOrFail($id);
        
        $request->validate([
            'payment_amount' => 'required|numeric',
            'due_date' => 'required|date',
            'payment_method' => 'required|string',
            'payment_reference' => 'required|string',
            'payment_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        $payment->payment_status = 'paid';
        $payment->due_date = $request->due_date;
        $payment->payment_method = $request->payment_method;
        $payment->payment_reference = $request->payment_reference;
        $payment->payment_notes = $request->payment_notes;
        
        // Handle receipt upload
        if ($request->hasFile('payment_receipt')) {
            $file = $request->file('payment_receipt');
            $fileName = time() . '_' . $payment->invoice_number . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/receipts'), $fileName);
            $payment->receipt_path = 'uploads/receipts/' . $fileName;
        }
        
        $payment->save();
        
        // Log the payment activity
        Log::info('Payment recorded', ['payment_id' => $payment->id, 'amount' => $payment->payment_amount, 'method' => $request->payment_method]);
        
        // Notify client, sales, and finance about the payment being recorded
        $notificationData = [
            'title' => 'Payment Recorded',
            'message' => 'Payment of ' . $payment->payment_amount . ' SAR has been recorded as paid',
            'type' => 'success',
            'url' => "#",
            'priority' => 'normal',
        ];
        $this->notifyRoles(['client', 'sales', 'finance'], $notificationData, $payment->customer_id);
            
        return redirect()->route('finance.payments')->with('success', 'Payment recorded successfully.');
    }
    
    public function paymentForm($id)
    {
        $payment = payments::with(['contract', 'customer'])->findOrFail($id);
        return view('managers.finance.payments.record', compact('payment'));
    }
    
    // Advanced Analytics Methods
    public function advancedAnalytics(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        $analytics = [
            'collection_rate' => $this->calculateCollectionRate($startDate, $endDate),
            'monthly_breakdown' => $this->getMonthlyBreakdown($startDate, $endDate),
            'payment_status_distribution' => $this->getPaymentStatusDistribution($startDate, $endDate),
            'aging_analysis' => $this->getAgingAnalysis(),
            'cash_flow_projection' => $this->getCashFlowProjection(),
        ];
        
        return view('managers.finance.reports.analytics', compact('analytics', 'startDate', 'endDate'));
    }
    
    private function getAgingAnalysis()
    {
        $now = Carbon::now();
        
        $aging = [
            'current' => payments::whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
                ->where('due_date', '>=', $now)
                ->sum('payment_amount'),
            '1_30' => payments::whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
                ->where('due_date', '<', $now)
                ->where('due_date', '>=', $now->copy()->subDays(30))
                ->sum('payment_amount'),
            '31_60' => payments::whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
                ->where('due_date', '<', $now->copy()->subDays(30))
                ->where('due_date', '>=', $now->copy()->subDays(60))
                ->sum('payment_amount'),
            '61_90' => payments::whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
                ->where('due_date', '<', $now->copy()->subDays(60))
                ->where('due_date', '>=', $now->copy()->subDays(90))
                ->sum('payment_amount'),
            'over_90' => payments::whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
                ->where('due_date', '<', $now->copy()->subDays(90))
                ->sum('payment_amount'),
        ];
        
        return $aging;
    }
    
    private function getCashFlowProjection()
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addMonths(3);
        
        $months = [];
        $currentDate = Carbon::parse($startDate);
        
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            
            $months[] = [
                'month' => $currentDate->format('M Y'),
                'expected_inflow' => payments::whereBetween('due_date', [$monthStart, $monthEnd])
                    ->whereIn('payment_status', ['pending', 'unpaid', 'overdue'])
                    ->sum('payment_amount'),
            ];
            
            $currentDate->addMonth();
        }
        
        return $months;
    }
    
    // Export Methods
    public function exportPaymentsForm()
    {
        $contracts = contracts::with('customer')->get();
        return view('managers.finance.exports.export_form', compact('contracts'));
    }
    
    public function exportPayments(Request $request)
    {
        $query = payments::with(['contract', 'customer']);
        
        // Apply filters if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->has('status')) {
            if ($request->status) {
                $query->where('payment_status', $request->status);
            }
        }
        
        if ($request->has('contract_id')) {
            if ($request->contract_id) {
                $query->where('contract_id', $request->contract_id);
            }
        }
        
        $payments = $query->get();
        
        // Calculate summary statistics
        $summary = [
            'total_amount' => $payments->sum('payment_amount'),
            'paid_amount' => $payments->where('payment_status', 'paid')->sum('payment_amount'),
            'pending_amount' => $payments->whereIn('payment_status', ['pending', 'unpaid'])->sum('payment_amount'),
            'overdue_amount' => $payments->where('payment_status', 'overdue')->sum('payment_amount'),
            'count' => $payments->count(),
            'paid_count' => $payments->where('payment_status', 'paid')->count(),
            'pending_count' => $payments->whereIn('payment_status', ['pending', 'unpaid'])->count(),
            'overdue_count' => $payments->where('payment_status', 'overdue')->count(),
        ];
        
        if ($request->format === 'csv') {
            // Generate CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="payments_export_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($payments) {
                $file = fopen('php://output', 'w');
                
                // Add headers
                fputcsv($file, [
                    'Invoice Number', 
                    'Contract Number', 
                    'Customer', 
                    'Amount', 
                    'Due Date', 
                    'Status', 
                    'Payment Method', 
                    'Reference', 
                    'Reconciled'
                ]);
                
                // Add data
                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->invoice_number,
                        $payment->contract->contract_number,
                        $payment->customer->name,
                        $payment->payment_amount,
                        $payment->due_date,
                        $payment->payment_status,
                        $payment->payment_method ?? 'N/A',
                        $payment->payment_reference ?? 'N/A',
                        $payment->reconciled ? 'Yes' : 'No'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } else {
            // Generate PDF using mPDF
            $mpdf = new \Mpdf\Mpdf([
                'orientation' => 'L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);
            
            $html = view('managers.finance.exports.payments_pdf', [
                'payments' => $payments,
                'summary' => $summary,
                'filters' => [
                    'start_date' => $request->start_date ?? 'All',
                    'end_date' => $request->end_date ?? 'All',
                    'status' => $request->status ?? 'All',
                    'contract_id' => $request->contract_id ? ($payments->first()->contract->contract_number ?? 'N/A') : 'All'
                ],
                'generated_by' => Auth::user()->name,
                'generated_at' => now()->format('Y-m-d H:i:s')
            ])->render();
            
            $mpdf->WriteHTML($html);
            return $mpdf->Output('payments_export_' . date('Y-m-d') . '.pdf', 'D');
        }
    }
    
    // Notification Methods
    public function sendPaymentReminders()
    {
        $upcomingPayments = payments::with(['contract', 'customer'])
            ->where('payment_status', 'pending')
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->get();
        
        foreach ($upcomingPayments as $payment) {
            Mail::to($payment->customer->email)->send(new PaymentReminder($payment));
            
            // Log the reminder
            Log::info('Payment reminder sent', ['payment_id' => $payment->id, 'due_date' => $payment->due_date]);
        }
        
        return redirect()->back()->with('success', 'Payment reminders sent successfully.');
    }
    
    // Payment Reconciliation
    public function reconciliationIndex()
    {
        // Get unreconciled paid payments
        $unreconciled = payments::where('payment_status', 'paid')
            ->where('reconciled', false)
            ->with(['contract', 'customer'])
            ->orderBy('paid_at', 'desc')
            ->get();
            
        // Get recently reconciled payments
        $reconciled = payments::where('reconciled', true)
            ->with(['contract', 'customer'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('managers.finance.reconciliation.index', compact('unreconciled', 'reconciled'));
    }
    
    public function reconcilePayments(Request $request)
    {
        $paymentIds = $request->input('payment_ids', []);
        
        if (empty($paymentIds)) {
            return redirect()->back()->with('error', 'No payments selected for reconciliation');
        }
        
        try {
            // Update the reconciled status for selected payments
            $count = payments::whereIn('id', $paymentIds)
                ->update(['reconciled' => true]);
                
            // Log the reconciliation action
            Log::info('Payments reconciled', [
                'user_id' => Auth::id(),
                'payment_count' => $count,
                'payment_ids' => $paymentIds
            ]);
            
            return redirect()->back()->with('success', $count . ' payments have been marked as reconciled');
        } catch (\Exception $e) {
            Log::error('Payment reconciliation failed', [
                'error' => $e->getMessage(),
                'payment_ids' => $paymentIds
            ]);
            
            return redirect()->back()->with('error', 'Failed to reconcile payments: ' . $e->getMessage());
        }
    }
}
