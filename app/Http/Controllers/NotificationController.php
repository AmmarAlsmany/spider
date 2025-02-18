<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\csrf_token;

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
                return Client::where('user_id', $user->id)->first();
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
            $notification = $user->notifications()->findOrFail($id);
            $notification->delete();
            return response()->json(['message' => 'Notification deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting notification'], 500);
        }
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $user->notifications()->delete();
            return response()->json(['message' => 'All notifications deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting notifications'], 500);
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
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? '',
                        'message' => $notification->data['message'] ?? '',
                        'type' => $notification->data['type'] ?? 'info',
                        'priority' => $notification->data['priority'] ?? 'normal',
                        'url' => $notification->data['url'] ?? '',
                        'read' => !is_null($notification->read_at),
                        'created_at' => $notification->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'notifications' => $notifications,
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
