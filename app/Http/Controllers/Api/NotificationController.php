<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 20);
            
            $notifications = $this->notificationService->getUserNotifications($user->id, $limit);
            $unreadCount = $this->notificationService->getUnreadCount($user->id);
            
            return response()->json([
                'success' => true,
                'notifications' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at->toISOString(),
                        'time_ago' => $notification->created_at->diffForHumans(),
                    ];
                }),
                'unread_count' => $unreadCount,
                'total' => $notifications->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi'
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            $notification = \App\Models\Notification::where('user_id', $user->id)->find($id);
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }
            
            $this->notificationService->markAsRead($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi telah ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi sebagai dibaca'
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            $count = $this->notificationService->markAllAsRead($user->id);
            
            return response()->json([
                'success' => true,
                'message' => "{$count} notifikasi telah ditandai sebagai dibaca",
                'marked_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi sebagai dibaca'
            ], 500);
        }
    }

    public function clearAll()
    {
        try {
            $user = Auth::user();
            $count = \App\Models\Notification::where('user_id', $user->id)->count();
            \App\Models\Notification::where('user_id', $user->id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "{$count} notifikasi telah dihapus",
                'deleted_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi'
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            $count = $this->notificationService->getUnreadCount($user->id);
            
            return response()->json([
                'success' => true,
                'unread_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'unread_count' => 0
            ], 500);
        }
    }
}