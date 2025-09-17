<?php
namespace App\Listeners;

use App\Events\SellerVerificationStatusChanged;
use App\Services\NotificationService;
use App\Enums\SellerVerificationStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SellerVerificationStatusChangedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(SellerVerificationStatusChanged $event): void
    {
        try {
            $additionalData = [
                'seller_id' => $event->seller->id,
                'old_status' => $event->oldStatus->value,
                'new_status' => $event->newStatus->value,
                'verification_notes' => $event->notes,
                'seller_profile_id' => $event->seller->sellerProfile->id ?? null,
            ];

            $title = $this->getNotificationTitle($event->newStatus);
            $message = $this->getNotificationMessage($event->newStatus, $event->notes);

            $this->notificationService->createForUser(
                $event->seller->id,
                'seller_verification',
                $title,
                $message,
                $additionalData
            );

            Log::info('Seller verification status notification created', [
                'seller_id' => $event->seller->id,
                'old_status' => $event->oldStatus->value,
                'new_status' => $event->newStatus->value,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create seller verification status notification', [
                'seller_id' => $event->seller->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getNotificationTitle(SellerVerificationStatus $status): string
    {
        return match ($status) {
            SellerVerificationStatus::VERIFIED => 'Verifikasi Disetujui',
            SellerVerificationStatus::REJECTED => 'Verifikasi Ditolak',
            SellerVerificationStatus::PENDING => 'Status Verifikasi Diperbarui',
        };
    }

    private function getNotificationMessage(SellerVerificationStatus $status, ?string $notes): string
    {
        $baseMessage = match ($status) {
            SellerVerificationStatus::VERIFIED => 'Selamat! Akun seller Anda telah berhasil diverifikasi dan Anda dapat menggunakan semua fitur seller.',
            SellerVerificationStatus::REJECTED => 'Dokumen verifikasi Anda ditolak. Silakan periksa catatan dari admin dan ajukan ulang dokumen yang sesuai.',
            SellerVerificationStatus::PENDING => 'Dokumen verifikasi Anda sedang dalam proses review oleh tim kami.',
        };

        if ($status === SellerVerificationStatus::REJECTED && $notes) {
            $baseMessage .= " Catatan: {$notes}";
        }

        return $baseMessage;
    }
}