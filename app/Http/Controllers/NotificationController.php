<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        
        if ($notification && $notification->user_id === auth()->user()->id) {
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai sudah dibaca'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notifikasi tidak ditemukan'
        ], 404);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => NotificationService::getNavbarBadgeCount(auth()->user()),
        ]);
    }

    /**
     * Get all unread notifications
     */
    public function getUnreadNotifications()
    {
        $notifications = NotificationService::getUnreadNotifications(auth()->user()->id);

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca
     */
    public function markAllAsRead()
    {
        NotificationService::markAllAsReadForUser(auth()->user());

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'count' => 0]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * View all notifications
     */
    public function index()
    {
        $user = auth()->user();

        $notifications = Notification::forUser($user->id)
            ->recent()
            ->paginate(20);

        $unreadCount = NotificationService::getNavbarBadgeCount($user);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
}

