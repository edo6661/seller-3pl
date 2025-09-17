<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    function handleClick($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('user_id', $user->id)->find($id);
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }
            if (!$notification->read_at) {
                $this->notificationService->markAsRead($id);
            }
            $redirectUrl = $this->getRedirectUrl($notification, $user);
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
                'message' => 'Notifikasi telah ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses notifikasi'
            ], 500);
        }
    }
    /**
     * Get redirect URL based on notification type
     */
    private function getRedirectUrl($notification, $user): string
    {
        $isAdmin = $user->role->value === 'admin';
        $additionalData = $notification->additional_data ?? [];
        switch ($notification->type) {
            case 'chat':
                $conversationId = $additionalData['conversation_id'] ?? null;
                if ($conversationId) {
                    return route('chat.show', $conversationId);
                }
                return route('chat.index');
            case 'pick_up_request':
                $pickupId = $additionalData['pickup_request_id'] ?? null;
                if ($pickupId) {
                    return $isAdmin
                        ? route('admin.pickup-requests.show', $pickupId)
                        : route('seller.pickup-request.show', $pickupId);
                }
                return $isAdmin
                    ? route('admin.pickup-requests.index')
                    : route('seller.pickup-request.index');
            case 'support_ticket':
                $ticketId = $additionalData['ticket_id'] ?? null;
                if ($ticketId) {
                    return $isAdmin
                        ? route('admin.support.show', $ticketId)
                        : route('seller.support.show', $ticketId);
                }
                return $isAdmin
                    ? route('admin.support.index')
                    : route('seller.support.index');
            case 'wallet':
                $transactionId = $additionalData['transaction_id'] ?? null;
                if ($transactionId) {
                    return $isAdmin
                        ? route('admin.wallets.index')
                        : route('seller.wallet.transaction.detail', $transactionId);
                }
                return $isAdmin
                    ? route('admin.wallets.index')
                    : route('seller.wallet.index');
            case 'seller_verification':
                if ($isAdmin) {
                    return route('admin.sellers.verification');
                } else {
                    $newStatus = $additionalData['new_status'] ?? null;
                    if ($newStatus === 'rejected') {
                        return route('profile.verification.resubmit');
                    }
                    return route('profile.index');
                }
            case 'seller_documents_uploaded':
                if ($isAdmin) {
                    return route('admin.sellers.verification');
                }
                return route('admin.dashboard');
            default:
                return $isAdmin
                    ? route('admin.dashboard')
                    : route('seller.dashboard');
        }
    }
}