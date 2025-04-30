<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\client;
use App\Models\contracts;
use App\Models\payments;
use App\Models\Tiket;
use App\Services\NotificationService;
use App\Traits\NotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use NotificationDispatcher;

    public function index()
    {
        // Tickets Statistics
        $totalTickets = Tiket::count();
        $openTickets = Tiket::where('status', 'open')->count();
        $recentTickets = Tiket::latest()->take(5)->get();

        // Contracts Statistics
        $totalContracts = contracts::count();
        $activeContracts = contracts::where('contract_status', 'approved')->count();
        $recentContracts = contracts::latest()->take(5)->get();

        // Financial Statistics
        $monthlyRevenue = payments::whereMonth('created_at', Carbon::now()->month)
            ->sum('payment_amount');
        $yearlyRevenue = payments::whereYear('created_at', Carbon::now()->year)
            ->sum('payment_amount');

        return view('admin.dashboard', compact(
            'totalTickets',
            'openTickets',
            'recentTickets',
            'totalContracts',
            'activeContracts',
            'recentContracts',
            'monthlyRevenue',
            'yearlyRevenue'
        ));
    }

    public function contractsIndex(Request $request)
    {
        // Get filters from request
        $status = $request->get('contract_status');
        $type = $request->get('Property_type');
        $search = $request->get('search');

        // Base query
        $query = contracts::with('customer')
            ->when($status, function ($query, $status) {
                if ($status === 'completed') {
                    return $query->where('contract_status', 'completed')
                        ->where('contract_end_date', '<=', Carbon::now()->addMonths(1))
                        ->where('contract_end_date', '>', Carbon::now());
                }
                return $query->where('contract_status', $status);
            })
            ->when($type, function ($query, $type) {
                return $query->where('Property_type', $type);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            });

        // Get contracts with pagination
        $contracts = $query->latest()->paginate(10);

        // Get statistics
        $totalContracts = contracts::count();
        $activeContracts = contracts::where('contract_status', 'approved')->count();
        $expiredContracts = contracts::where('contract_status', 'completed')->count();
        $expiringSoonContracts = contracts::where('contract_status', 'approved')
            ->where('contract_end_date', '<=', Carbon::now()->addMonths(1))
            ->where('contract_end_date', '>', Carbon::now())
            ->count();

        return view('admin.contracts.index', compact(
            'contracts',
            'totalContracts',
            'activeContracts',
            'expiredContracts',
            'expiringSoonContracts'
        ));
    }

    public function reports()
    {
        return view('admin.contracts.reports');
    }

    public function getReportsData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $monthlyStats = collect(range(1, 12))->map(function ($month) use ($year) {
            $date = Carbon::create($year, $month, 1);
            
            return [
                'month' => $date->format('M'),
                'new_contracts' => contracts::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count(),
                'expired_contracts' => contracts::whereMonth('contract_end_date', $month)
                    ->whereYear('contract_end_date', $year)
                    ->where('contract_status', 'completed')
                    ->count(),
                'revenue' => payments::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('payment_amount')
            ];
        });

        return response()->json($monthlyStats);
    }

    public function show(contracts $contract)
    {
        // Load necessary relationships
        $contract->load([
            'customer',
            'payments',
            'type',
        ]);

        // Get payment statistics
        $totalPayments = $contract->payments->sum('payment_amount');
        $payments = $contract->payments->where('payment_status', 'paid')->sum('payment_amount');
        $remainingAmount = $contract->contract_price - $payments;
        $paymentProgress = $contract->contract_price > 0 
            ? round(($totalPayments / $contract->contract_price) * 100) 
            : 0;

        // Calculate contract duration
        $startDate = Carbon::parse($contract->contract_start_date);
        $endDate = Carbon::parse($contract->contract_end_date);
        $duration = $startDate->diffInMonths($endDate);
        
        // Calculate remaining time
        $now = Carbon::now();
        $remainingTime = $now->diffInDays($endDate, false);
        $timeProgress = $startDate->diffInDays($endDate) > 0 
            ? round(($startDate->diffInDays($now) / $startDate->diffInDays($endDate)) * 100) 
            : 0;

        return view('admin.contracts.show', compact(
            'contract',
            'totalPayments',
            'payments',
            'remainingAmount',
            'paymentProgress',
            'duration',
            'remainingTime',
            'timeProgress'
        ));
    }

    public function ticketsIndex()
    {
        $tickets = Tiket::with(['customer.contracts'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function ticketsReports()
    {
        return view('admin.tickets.reports');
    }

    public function getTicketsReportsData()
    {
        $openTickets = Tiket::where('status', 'open')->count();
        $closedTickets = Tiket::where('status', 'closed')->count();
        
        $monthlyTickets = Tiket::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $customerTickets = Tiket::with('customer')
            ->selectRaw('customer_id, COUNT(*) as count')
            ->groupBy('customer_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => [
                'open' => $openTickets,
                'closed' => $closedTickets
            ],
            'monthly' => $monthlyTickets,
            'topCustomers' => $customerTickets
        ]);
    }

    public function ticketShow(Tiket $ticket)
    {
        $ticket->load([
            'customer',
            'solver',
            'creator',
            'ticketReplies.user',
            'customer.contracts'
        ]);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function ticketReply(Request $request, Tiket $ticket)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $ticket->replies()->create([
            'ticket_id' => $ticket->id,
            'reply' => $request->content,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Reply added successfully');
    }

    public function paymentsIndex(Request $request)
    {
        $query = payments::with(['customer', 'contract'])
            ->orderBy('due_date', 'desc');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('payment_status', $request->status);
        }

        $payments = $query->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function paymentShow(payments $payment)
    {
        $payment->load([
            'customer',
            'contract',
            'postponementRequests'
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    public function updatePaymentStatus(Request $request, payments $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,overdue',
            'paid_at' => 'required_if:status,paid|date',
            'payment_method' => 'required_if:status,paid|string'
        ]);

        $oldStatus = $payment->payment_status;
        
        $payment->update([
            'payment_status' => $request->status,
            'paid_at' => $request->status === 'paid' ? $request->paid_at : null,
            'payment_method' => $request->payment_method
        ]);

        // Notify client, sales, and finance about the payment status update
        if ($oldStatus !== $request->status) {
            $notificationData = [
                'title' => 'Payment Status Updated',
                'message' => 'Payment #' . $payment->invoice_number . ' status has been updated to ' . ucfirst($request->status),
                'type' => $request->status === 'paid' ? 'success' : ($request->status === 'overdue' ? 'warning' : 'info'),
                'url' => "#",
                'priority' => $request->status === 'overdue' ? 'high' : 'normal',
            ];
            
            // Create a new instance of NotificationService
            $notificationService = new NotificationService();
            $notificationService->notifyRoles(['client', 'sales', 'finance'], $notificationData);
        }

        return redirect()->back()->with('success', 'Payment status updated successfully');
    }

    public function paymentsReports()
    {
        return view('admin.payments.reports');
    }

    public function getPaymentsReportsData()
    {
        // Get payment statistics
        $totalPayments = payments::count();
        $paidPayments = payments::where('payment_status', 'paid')->count();
        $pendingPayments = payments::where('payment_status', 'unpaid')->count();
        $overduePayments = payments::where('payment_status', 'overdue')->count();

        // Get monthly payment amounts
        $monthlyPayments = payments::selectRaw('SUM(payment_amount) as amount, MONTH(due_date) as month')
            ->whereYear('due_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get top customers by payment amount
        $topCustomers = payments::with('customer')
            ->selectRaw('customer_id, SUM(payment_amount) as total_amount, COUNT(*) as payment_count')
            ->where('payment_status', 'paid')
            ->groupBy('customer_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        return response()->json([
            'statistics' => [
                'total' => $totalPayments,
                'paid' => $paidPayments,
                'pending' => $pendingPayments,
                'overdue' => $overduePayments
            ],
            'monthly' => $monthlyPayments,
            'topCustomers' => $topCustomers
        ]);
    }

    public function generalReport()
    {
        return view('admin.generalReport');
    }

    public function getGeneralReportData()
    {
        // Get payment statistics
        $totalPayments = payments::count();
        $paidPayments = payments::where('payment_status', 'paid')->count();
        $unpaidPayments = payments::where('payment_status', 'unpaid')->count();
        $overduePayments = payments::where('payment_status', 'overdue')->count();
        $totalPaymentAmount = payments::where('payment_status', 'paid')->sum('payment_amount');
        $pendingPaymentAmount = payments::whereIn('payment_status', ['unpaid', 'overdue', 'pending'])->sum('payment_amount');
        
        // Add total sums for paid and unpaid payments
        $totalPaidSum = payments::where('payment_status', 'paid')->sum('payment_amount');
        $totalUnpaidSum = payments::whereIn('payment_status', ['unpaid', 'overdue', 'pending'])->sum('payment_amount');

        // Get contract statistics
        $totalContracts = contracts::count();
        $activeContracts = contracts::where('contract_status', 'approved')->count();
        $expiredContracts = contracts::where('contract_status', 'completed')->count();
        $pendingContracts = contracts::where('contract_status', 'pending')->count();
        $contractsEndingSoon = contracts::where('contract_status', 'approved')
            ->where('contract_end_date', '<=', now()->addDays(30))
            ->count();

        // Get ticket statistics
        $totalTickets = Tiket::count();
        $openTickets = Tiket::where('status', 'open')->count();
        $closedTickets = Tiket::where('status', 'closed')->count();
        $pendingTickets = Tiket::where('status', 'pending')->count();

        // Get monthly data
        $monthlyData = [
            'payments' => payments::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(payment_amount) as amount')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            'contracts' => contracts::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            'tickets' => Tiket::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get()
        ];

        // Get top customers
        $topCustomers = client::withCount(['contracts', 'tickets'])
            ->withSum(['payments' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'payment_amount')
            ->orderByDesc('payments_sum_payment_amount')
            ->limit(5)
            ->get();

        // Get recent activities
        $recentActivities = collect();
        
        // Add recent payments
        payments::with('customer')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function($payment) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'payment',
                    'title' => "Payment #{$payment->invoice_number}",
                    'description' => "Payment of " . number_format($payment->payment_amount, 2) . " by {$payment->customer->name}",
                    'status' => $payment->payment_status,
                    'date' => $payment->created_at
                ]);
            });

        // Add recent tickets
        Tiket::with('customer')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function($ticket) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'ticket',
                    'title' => "Ticket #{$ticket->id}",
                    'description' => "{$ticket->customer->name}: {$ticket->title}",
                    'status' => $ticket->status,
                    'date' => $ticket->created_at
                ]);
            });

        // Add recent contracts
        contracts::with('customer')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function($contract) use ($recentActivities) {
                $recentActivities->push([
                    'type' => 'contract',
                    'title' => "Contract #{$contract->contract_number}",
                    'description' => "Contract with {$contract->customer->name}",
                    'status' => $contract->status,
                    'date' => $contract->created_at
                ]);
            });

        // Sort activities by date
        $recentActivities = $recentActivities->sortByDesc('date')->values();

        return response()->json([
            'statistics' => [
                'payments' => [
                    'total' => $totalPayments,
                    'paid' => $paidPayments,
                    'unpaid' => $unpaidPayments,
                    'overdue' => $overduePayments,
                    'totalAmount' => $totalPaymentAmount,
                    'pendingAmount' => $pendingPaymentAmount,
                    'totalPaidSum' => $totalPaidSum,
                    'totalUnpaidSum' => $totalUnpaidSum
                ],
                'contracts' => [
                    'total' => $totalContracts,
                    'active' => $activeContracts,
                    'expired' => $expiredContracts,
                    'pending' => $pendingContracts,
                    'endingSoon' => $contractsEndingSoon
                ],
                'tickets' => [
                    'total' => $totalTickets,
                    'open' => $openTickets,
                    'closed' => $closedTickets,
                    'pending' => $pendingTickets
                ]
            ],
            'monthlyData' => $monthlyData,
            'topCustomers' => $topCustomers,
            'recentActivities' => $recentActivities
        ]);
    }
}
