<?php
namespace App\Listeners;

use App\Events\PickupRequestStatusUpdated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PickupRequestStatusUpdatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(PickupRequestStatusUpdated $event): void
    {
        $pickupRequest = $event->pickupRequest;
        $status = $pickupRequest->status;

        // Buat notifikasi berdasarkan status
        $notification = $this->getNotificationForStatus($status, $pickupRequest->pickup_code);
        
        if ($notification) {
            // Notifikasi untuk user pemilik request
            $this->notificationService->createForUser(
                $pickupRequest->user_id,
                'pickup_status_updated',
                $notification['title'],
                $notification['message']
            );

            // Notifikasi untuk admin jika diperlukan
            if ($this->shouldNotifyAdmin($status)) {
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $this->notificationService->createForUser(
                        $admin->id,
                        'pickup_status_updated',
                        $notification['admin_title'] ?? $notification['title'],
                        $notification['admin_message'] ?? $notification['message']
                    );
                }
            }
        }
    }

    private function getNotificationForStatus(string $status, string $pickupCode): ?array
    {
        $notifications = [
            'confirmed' => [
                'title' => 'Pickup Request Dikonfirmasi',
                'message' => "Pickup request {$pickupCode} telah dikonfirmasi dan akan segera diproses.",
                'admin_title' => 'Pickup Request Dikonfirmasi',
                'admin_message' => "Pickup request {$pickupCode} telah dikonfirmasi."
            ],
            'pickup_scheduled' => [
                'title' => 'Pickup Dijadwalkan',
                'message' => "Pickup request {$pickupCode} telah dijadwalkan. Kurir akan segera mengambil paket Anda.",
            ],
            'picked_up' => [
                'title' => 'Paket Telah Diambil',
                'message' => "Paket {$pickupCode} telah diambil kurir dan sedang dalam perjalanan ke tujuan.",
            ],
            'delivered' => [
                'title' => 'Paket Telah Diterima',
                'message' => "Paket {$pickupCode} telah berhasil diterima oleh penerima. Terima kasih atas kepercayaan Anda!",
                'admin_title' => 'Pickup Request Selesai',
                'admin_message' => "Pickup request {$pickupCode} telah berhasil diselesaikan."
            ],
            'cancelled' => [
                'title' => 'Pickup Request Dibatalkan',
                'message' => "Pickup request {$pickupCode} telah dibatalkan. Jika ada pembayaran, akan dikembalikan ke wallet Anda.",
                'admin_title' => 'Pickup Request Dibatalkan',
                'admin_message' => "Pickup request {$pickupCode} telah dibatalkan."
            ],
            'failed' => [
                'title' => 'Pickup Request Gagal',
                'message' => "Pickup request {$pickupCode} gagal diproses. Silakan hubungi customer service untuk bantuan.",
                'admin_title' => 'Pickup Request Gagal',
                'admin_message' => "Pickup request {$pickupCode} gagal diproses dan memerlukan penanganan."
            ]
        ];

        return $notifications[$status] ?? null;
    }

    private function shouldNotifyAdmin(string $status): bool
    {
        return in_array($status, ['delivered', 'cancelled', 'failed']);
    }

    public function failed(PickupRequestStatusUpdated $event, \Throwable $exception): void
    {
        Log::error('Failed to process PickupRequestStatusUpdated event', [
            'pickup_request_id' => $event->pickupRequest->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
