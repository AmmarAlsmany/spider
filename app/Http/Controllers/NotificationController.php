<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\client;
use Illuminate\Support\Facades\csrf_token;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        // Remove middleware - we'll handle auth manually
    }

    protected function getUser()
    {
        // First check client guard
        if (Auth::guard('client')->check()) {
            return Auth::guard('client')->user();
        }

        // Then check web guard
        if (Auth::check()) {
            $user = Auth::user();

            // If web user is a client type, get their client profile
            if ($user->role === 'client') {
                return client::where('user_id', $user->id)->first();
            }

            return $user;
        }

        return null;
    }

    public function markAsRead($id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $notification = DatabaseNotification::where('id', $id)
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->firstOrFail();
            
            // Store the URL before marking as read
            $redirectUrl = $notification->data['url'] ?? '';
            
            // Delete notification instead of marking as read
            $notification->delete();

            return response()->json([
                'message' => 'Notification removed successfully',
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing notification: ' . $e->getMessage()], 500);
        }
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Count notifications before deleting for the message
            $count = DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->count();
                
            // Delete all notifications instead of marking them as read
            DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->delete();

            return response()->json(['message' => "{$count} notifications removed successfully"]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing notifications: ' . $e->getMessage()], 500);
        }
    }

    public function getNotifications(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json([
                'notifications' => [],
                'message' => 'Unauthenticated'
            ], 401);
        }

        try {
            $limit = request()->input('limit', 10);
            $category = request()->input('category', null);
            $page = request()->input('page', 1);
            $skipCount = ($page - 1) * $limit;

            $query = DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->orderBy('created_at', 'desc');

            // Filter by notification type/category if provided
            if ($category) {
                $query->where('data->type', $category);
            }

            // Get total count for pagination
            $totalCount = $query->count();

            // Apply pagination
            $notifications = $query->skip($skipCount)
                ->take($limit)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? '',
                        'message' => $notification->data['message'] ?? '',
                        'type' => $notification->data['type'] ?? 'info',
                        'priority' => $notification->data['priority'] ?? 'normal',
                        'url' => $notification->data['url'] ?? '',
                        'icon' => $notification->data['icon'] ?? 'bell',
                        'color' => $notification->data['color'] ?? 'primary',
                        'read' => !is_null($notification->read_at),
                        'created_at' => $notification->created_at->diffForHumans(),
                        'created_at_raw' => $notification->created_at
                    ];
                });

            // Count unread notifications
            $unreadCount = DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'total_count' => $totalCount,
                'page' => $page,
                'limit' => $limit,
                'has_more' => ($skipCount + $limit) < $totalCount,
                'csrf_token' => csrf_token() // Include CSRF token in response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'notifications' => [],
                'message' => 'Error fetching notifications'
            ], 500);
        }
    }
}
