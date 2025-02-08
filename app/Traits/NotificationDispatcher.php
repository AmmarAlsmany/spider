<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\AdminNotification;
use App\Notifications\ClientNotification;
use App\Notifications\FinancialNotification;
use App\Notifications\SalesNotification;
use App\Notifications\SalesManagerNotification;
use App\Notifications\TeamLeaderNotification;
use App\Notifications\TechnicalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait NotificationDispatcher
{
    
    protected function getRole()
    {
        return Auth::user()->role;
    }

    protected function notifyByRole(string $role, array $data)
    {
        // Ensure data has required fields
        $data = array_merge([
            'title' => '',
            'message' => '',
            'type' => 'info',
            'url' => '',
            'priority' => 'normal'
        ], $data);

        $notificationClass = match($role) {
            'client' => ClientNotification::class,
            'sales' => SalesNotification::class,
            'sales_manager' => SalesManagerNotification::class,
            'technical' => TechnicalNotification::class,
            'team_leader' => TeamLeaderNotification::class,
            'admin' => AdminNotification::class,
            'financial' => FinancialNotification::class,
            default => null
        };

        if (!$notificationClass) {
            return;
        }

        User::where('role', $role)->each(function ($user) use ($data, $notificationClass) {
            $user->notify(new $notificationClass($data));
        });
    }

    // Helper method to notify multiple roles at once
    protected function notifyRoles(array $roles, array $data)
    {
        foreach ($roles as $role) {
            $this->notifyByRole($role, $data);
        }
    }

    /**
     * Mark a specific notification as read
     */
    protected function markNotificationAsRead($notificationId)
    {
        $user = Auth::guard('client')->check() ? Auth::guard('client')->user() : Auth::user();
        
        if (!$user) {
            return false;
        }

        try {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for the current user
     */
    protected function markAllNotificationsAsRead()
    {
        $user = Auth::guard('client')->check() ? Auth::guard('client')->user() : Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            return true;
        }
        return false;
    }

    /**
     * Send a notification with priority level
     */
    protected function notifyWithPriority(string $role, array $data, string $priority = 'normal')
    {
        $data['priority'] = $priority;
        $data['type'] = match($priority) {
            'high' => 'error',
            'medium' => 'warning',
            default => 'info'
        };
        
        $this->notifyByRole($role, $data);
    }
}
