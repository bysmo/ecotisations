<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembreNotificationController extends Controller
{
    /**
     * Récupérer les notifications non lues (JSON pour la cloche)
     */
    public function unread()
    {
        $membre = Auth::guard('membre')->user();
        if (!$membre) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $notifications = $membre->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'info',
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                ];
            });

        $unreadCount = $membre->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $membre = Auth::guard('membre')->user();
        $notification = $membre ? $membre->notifications()->find($id) : null;

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        $membre = Auth::guard('membre')->user();
        if ($membre) {
            $membre->unreadNotifications->each->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    /**
     * Page liste de toutes les notifications (optionnel)
     */
    public function index()
    {
        $membre = Auth::guard('membre')->user();
        $notifications = $membre->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(\App\Models\AppSetting::get('pagination_par_page', 15));

        return view('membres.notifications.index', compact('notifications'));
    }
}
