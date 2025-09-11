<?php
namespace App\Services;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
class NotificationService
{
    public function getUserNotifications(int $userId, int $limit = 20): Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getUnreadNotifications(int $userId): Collection
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function createNotification(array $data): Notification
    {
        return Notification::create($data);
    }
    public function createForUser(int $userId, string $type, string $title, string $message, array $additionalData = []): Notification
    {
        if ($type === 'new_chat_message' && isset($additionalData['message_id'])) {
            $existingNotification = Notification::where([
                'user_id' => $userId,
                'type' => $type,
                'message' => $message
            ])
            ->where('created_at', '>=', now()->subMinutes(1)) 
            ->first();
            if ($existingNotification) {
                Log::warning('Duplicate chat notification prevented', [
                    'user_id' => $userId,
                    'type' => $type,
                    'message_id' => $additionalData['message_id'],
                    'existing_notification_id' => $existingNotification->id
                ]);
                return $existingNotification;
            }
        }
        $notificationData = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message
        ];
        if (!empty($additionalData)) {
            $notificationData = array_merge($notificationData, $additionalData);
        }
        $notification = $this->createNotification($notificationData);
        return $notification;
    }
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }
    public function deleteNotification(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        return $notification ? $notification->delete() : false;
    }
    public function getUnreadCount(int $userId): int
    {
        $count = Notification::where('user_id', $userId)->unread()->count();
        Log::debug('Unread count retrieved', [
            'user_id' => $userId,
            'count' => $count
        ]);
        return $count;
    }
    public function broadcastToSellers(string $type, string $title, string $message): int
    {
        $sellers = User::sellers()->get();
        $count = 0;
        foreach ($sellers as $seller) {
            $this->createForUser($seller->id, $type, $title, $message);
            $count++;
        }
        return $count;
    }
    public function createPickupNotification(int $userId, string $pickupCode, string $status): Notification
    {
        $titles = [
            'requested' => 'Permintaan Pickup Baru',
            'confirmed' => 'Pickup Dikonfirmasi',
            'picked_up' => 'Paket Telah Diambil',
            'delivered' => 'Paket Telah Diterima',
            'cancelled' => 'Pickup Dibatalkan'
        ];
        $messages = [
            'requested' => "Permintaan pickup dengan kode {$pickupCode} telah dibuat dan sedang menunggu konfirmasi.",
            'confirmed' => "Pickup dengan kode {$pickupCode} telah dikonfirmasi dan akan segera diambil kurir.",
            'picked_up' => "Paket dengan kode {$pickupCode} telah diambil kurir dan sedang dalam perjalanan.",
            'delivered' => "Paket dengan kode {$pickupCode} telah berhasil diterima oleh penerima.",
            'cancelled' => "Pickup dengan kode {$pickupCode} telah dibatalkan."
        ];
        return $this->createForUser(
            $userId,
            'pickup_update',
            $titles[$status] ?? 'Update Pickup',
            $messages[$status] ?? "Status pickup {$pickupCode} telah diupdate."
        );
    }
    public function cleanupDuplicateNotifications(int $userId): int
    {
        $duplicates = Notification::where('user_id', $userId)
            ->where('type', 'new_chat_message')
            ->selectRaw('MIN(id) as keep_id, message, COUNT(*) as count')
            ->groupBy('message')
            ->having('count', '>', 1)
            ->get();
        $deletedCount = 0;
        foreach ($duplicates as $duplicate) {
            $deleted = Notification::where('user_id', $userId)
                ->where('type', 'new_chat_message')
                ->where('message', $duplicate->message)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
            $deletedCount += $deleted;
        }
        return $deletedCount;
    }
}