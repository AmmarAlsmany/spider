<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\contracts;
use App\Models\payments;
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
                'message' => "Payment of {$payment->payment_amount} is should be paid by {$payment->due_date} for Contract #{$payment->contract_number}"
            ]);
        }

        return count($duePayments);
    }

    public function reminderRenewal()
    {
        $oneMonthFromNow = Carbon::now()->addMonths(1);
        $twoMonthsFromNow = Carbon::now()->addMonths(2);

        // Get contracts that need renewal (ending between 1 and 2 months from now)
        $renewalContracts = contracts::where('contract_status', 'approved')
            ->whereBetween('contract_end_date', [$oneMonthFromNow, $twoMonthsFromNow])
            ->get();

        foreach ($renewalContracts as $contract) {
            // Create alert for contract renewal
            Alert::create([
                'title' => 'Reminder',
                'message' => "Contract #{$contract->contract_number} will expire in {$contract->contract_end_date} Dont miss renewal!",
                'type' => 'Contract Renewal',
                'priority' => 'medium'
            ]);
        }

        return count($renewalContracts);
    }

    public function generateMonthlyReport()
    {
        $duePaymentsCount = $this->checkDuePayments();
        $renewalCount = $this->reminderRenewal();

        $report = [
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'due_payments' => $duePaymentsCount,
            'contracts_needing_renewal' => $renewalCount
        ];

        // Create alert for monthly report
        Alert::create([
            'type' => 'monthly_report',
            'message' => "Monthly Report Generated: {$duePaymentsCount} due payments, {$renewalCount} contracts needing renewal"
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
                    // Delete the notification instead of marking it as read
                    $notification->delete();
                    return redirect()->back()->with('success', 'Notification removed successfully');
                }
            }
        } else {
            // This is an alert
            $alert = Alert::findOrFail($id);
            // Only mark as read for the current user, don't delete the alert
            $alert->readByUsers()->syncWithoutDetaching([Auth::id()]);
        }
        
        return redirect()->back();
    }

    public function markAllAsRead()
    {
        // Mark all alerts as read for the current user only
        $alerts = Alert::all();
        foreach ($alerts as $alert) {
            // Only mark as read for the current user, don't delete the alert
            $alert->readByUsers()->syncWithoutDetaching([Auth::id()]);
        }
        
        // Delete all notifications instead of marking them as read
        $user = $this->getAuthenticatedUser();
        if ($user) {
            // Get count of notifications for the success message
            $notificationCount = $user->notifications->count();
            
            // Delete all notifications
            $user->notifications()->delete();
            
            return redirect()->back()->with('success', "All alerts marked as read and notifications removed");
        }
        
        return redirect()->back()->with('success', 'All alerts marked as read');
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
    
    /**
     * Delete a notification
     *
     * @param string $id Notification ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteNotification($id)
    {
        // Check if the notification exists
        $user = $this->getAuthenticatedUser();
        if ($user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->delete();
                return redirect()->route('alerts.index')
                    ->with('success', 'Notification deleted successfully');
            }
        }
        
        return redirect()->route('alerts.index')
            ->with('error', 'Notification not found');
    }
}
