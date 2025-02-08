<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected function getUser()
    {
        return Auth::guard('client')->check() ? Auth::guard('client')->user() : Auth::user();
    }

    public function markAsRead($id): JsonResponse
    {
        $user = $this->getUser();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();
        $user->notifications()->delete();

        return response()->json(['message' => 'All notifications deleted']);
    }

    public function getNotifications(): JsonResponse
    {
        $user = $this->getUser();
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
            'notifications' => $notifications
        ]);
    }
}
