<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\contracts;
use App\Models\payments;
use App\Models\User;
use App\Models\client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AlertController extends Controller
{
    public function index()
    {
        // Get system alerts
        $alerts = Alert::with(['contract', 'readByUsers'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($alert) {
                $alert->is_read = $alert->isReadByUser(Auth::id());
                return $alert;
            });

        // Get user notifications
        $notifications = $this->getUserNotifications();
        
        return view('alerts.index', compact('alerts', 'notifications'));
    }

    /**
     * Get user notifications formatted for the alerts view
     */
    protected function getUserNotifications()
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return collect();
        }
        
        // Both User and Client models use the Notifiable trait
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'info',
                    'title' => $notification->data['title'] ?? '',
                    'message' => $notification->data['message'] ?? '',
                    'priority' => $notification->data['priority'] ?? 'normal',
                    'url' => $notification->data['url'] ?? '#',
                    'is_read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at,
                    'is_notification' => true
                ];
            });
    }
    
    /**
     * Get the authenticated user from either guard
     */
    protected function getAuthenticatedUser(): ?Model
    {
        if (Auth::guard('client')->check()) {
            return Auth::guard('client')->user();
        }
        
        if (Auth::check()) {
            return Auth::user();
        }
        
        return null;
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
        // Check if this is a notification (UUID format) or an alert (numeric ID)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $id)) {
            // This is a notification
            $user = $this->getAuthenticatedUser();
            if ($user) {
                $notification = $user->notifications()->where('id', $id)->first();
                if ($notification) {
                    $notification->markAsRead();
                }
            }
        } else {
            // This is an alert
            $alert = Alert::findOrFail($id);
            $alert->readByUsers()->syncWithoutDetaching([Auth::id()]);
        }
        
        return redirect()->back();
    }

    public function markAllAsRead()
    {
        // Mark all alerts as read
        $alerts = Alert::all();
        foreach ($alerts as $alert) {
            $alert->readByUsers()->syncWithoutDetaching([Auth::id()]);
        }
        
        // Mark all notifications as read
        $user = $this->getAuthenticatedUser();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        
        return redirect()->back()->with('success', 'All alerts and notifications marked as read');
    }

    public function getUnreadCount()
    {
        // Count unread alerts
        $alertCount = Alert::whereDoesntHave('readByUsers', function ($query) {
            $query->where('user_id', Auth::id());
        })->count();
        
        // Count unread notifications
        $user = $this->getAuthenticatedUser();
        $notificationCount = $user ? $user->unreadNotifications->count() : 0;
        
        return $alertCount + $notificationCount;
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
