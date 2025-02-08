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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now());

        $report = [
            'total_revenue' => payments::whereBetween('due_date', [$startDate, $endDate])
                ->sum('payment_amount'),
            'pending_payments' => payments::where('payment_status', 'overdue')
                ->orWhere('payment_status', 'unpaid')
                ->orWhere('payment_status', "pending")
                ->sum('payment_amount'),
            'monthly_revenue' => payments::whereMonth('due_date', Carbon::now()->month)
                ->sum('payment_amount'),
            'payment_history' => payments::with(['contract', 'customer'])
                ->whereBetween('due_date', [$startDate, $endDate])
                ->paginate(10)
        ];

        return view('managers.finance.reports.financial', compact('report'));
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
