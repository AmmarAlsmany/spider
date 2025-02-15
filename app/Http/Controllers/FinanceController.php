<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\contracts;
use App\Models\payments;
use App\Models\tikets;
use Carbon\Carbon;

class FinanceController extends Controller
{
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
}
