<?php
namespace App\Events;

use App\Models\User;
use App\Enums\SellerVerificationStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerVerificationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $seller,
        public SellerVerificationStatus $oldStatus,
        public SellerVerificationStatus $newStatus,
        public ?string $notes = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->seller->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'seller_id' => $this->seller->id,
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'notes' => $this->notes,
            'notification' => [
                'type' => 'seller_verification',
                'title' => $this->getNotificationTitle(),
                'message' => $this->getNotificationMessage(),
                'icon' => $this->getNotificationIcon(),
                'color' => $this->newStatus->color()
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'seller.verification.status.changed';
    }

    private function getNotificationTitle(): string
    {
        return match ($this->newStatus) {
            SellerVerificationStatus::VERIFIED => 'Verifikasi Disetujui',
            SellerVerificationStatus::REJECTED => 'Verifikasi Ditolak',
            SellerVerificationStatus::PENDING => 'Status Verifikasi Diperbarui',
        };
    }

    private function getNotificationMessage(): string
    {
        return match ($this->newStatus) {
            SellerVerificationStatus::VERIFIED => 'Selamat! Akun seller Anda telah berhasil diverifikasi.',
            SellerVerificationStatus::REJECTED => 'Dokumen verifikasi Anda ditolak. Silakan periksa catatan dan ajukan ulang.',
            SellerVerificationStatus::PENDING => 'Status verifikasi Anda telah diperbarui menjadi menunggu review.',
        };
    }

    private function getNotificationIcon(): string
    {
        return match ($this->newStatus) {
            SellerVerificationStatus::VERIFIED => 'fas fa-check-circle',
            SellerVerificationStatus::REJECTED => 'fas fa-times-circle',
            SellerVerificationStatus::PENDING => 'fas fa-clock',
        };
    }
}