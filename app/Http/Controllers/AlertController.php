<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\contracts;
use App\Models\payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::with(['contract', 'readByUsers'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($alert) {
                $alert->is_read = $alert->isReadByUser(Auth::id());
                return $alert;
            });

        return view('alerts.index', compact('alerts'));
    }

    public function checkExpiredContracts()
    {
        $today = Carbon::now();

        // Get contracts that have expired
        $expiredContracts = contracts::where('contract_status', 'approved')
            ->get();

        foreach ($expiredContracts as $contract) {
            // Check if all visits are completed
            $allVisitsCompleted = $contract->visitSchedules->every(function ($visit) {
                return $visit->status === 'completed';
            });

            if ($allVisitsCompleted) {
                // Create alert for expired contract
                Alert::create([
                    'type' => 'expired',
                    'contract_id' => $contract->id,
                    'message' => "Contract #{$contract->id} has expired on " . Carbon::now()->format('Y-m-d') . "!"
                ]);
            }
        }

        return count($expiredContracts);
    }

    public function checkDuePayments()
    {
        $today = Carbon::now();
        $threeDaysFromNow = Carbon::now()->addDays(3);

        // Get payments that are due within 3 days
        $duePayments = payments::whereBetween('due_date', [$today, $threeDaysFromNow])
            ->where('payment_status', 'unpaid')
            ->with('contract')
            ->get();

        foreach ($duePayments as $payment) {
            // Create alert for due payment
            Alert::create([
                'type' => 'payment_due',
                'contract_id' => $payment->contract_id,
                'message' => "Payment of {$payment->payment_amount} is due on {$payment->due_date} for Contract #{$payment->contract_id}"
            ]);
        }

        return count($duePayments);
    }

    public function checkRenewalNeeded()
    {
        $threeMonthsFromNow = Carbon::now()->addMonths(3);
        $twoMonthsFromNow = Carbon::now()->addMonths(2);

        // Get contracts that need renewal (ending in 2-3 months)
        $renewalContracts = contracts::whereBetween('contract_end_date', [$twoMonthsFromNow, $threeMonthsFromNow])
            ->where('contract_status', 'approved')
            ->get();

        foreach ($renewalContracts as $contract) {
            // Create alert for contract renewal
            Alert::create([
                'type' => 'renewal_needed',
                'contract_id' => $contract->id,
                'message' => "Contract #{$contract->id} needs renewal. Expires on {$contract->contract_end_date}!"
            ]);
        }

        return count($renewalContracts);
    }

    public function generateMonthlyReport()
    {
        $expiredCount = $this->checkExpiredContracts();
        $duePaymentsCount = $this->checkDuePayments();
        $renewalCount = $this->checkRenewalNeeded();

        $report = [
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'expired_contracts' => $expiredCount,
            'due_payments' => $duePaymentsCount,
            'contracts_needing_renewal' => $renewalCount
        ];

        // Create alert for monthly report
        Alert::create([
            'type' => 'monthly_report',
            'message' => "Monthly Report Generated: {$expiredCount} expired contracts, {$duePaymentsCount} due payments, {$renewalCount} contracts needing renewal"
        ]);

        return $report;
    }

    public function markAsRead($id)
    {
        $alert = Alert::findOrFail($id);
        $alert->readByUsers()->syncWithoutDetaching([Auth::id()]);
        return redirect()->back();
    }

    public function getUnreadCount()
    {
        return Alert::whereDoesntHave('readByUsers', function ($query) {
            $query->where('user_id', Auth::id());
        })->count();
    }

    public function destroy($id)
    {
        $alert = Alert::findOrFail($id);
        
        // Delete the alert and its read records
        $alert->readByUsers()->detach();
        $alert->delete();
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alert deleted successfully');
    }
}
