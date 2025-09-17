<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerDocumentsUploaded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $seller,
        public array $uploadedDocuments = [], // ['ktp', 'passbook']
        public bool $isResubmission = false
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
            new Channel('admin-global'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'seller_id' => $this->seller->id,
            'seller_name' => $this->seller->name,
            'seller_email' => $this->seller->email,
            'uploaded_documents' => $this->uploadedDocuments,
            'is_resubmission' => $this->isResubmission,
            'notification' => [
                'type' => 'seller_documents_uploaded',
                'title' => $this->isResubmission ? 'Dokumen Verifikasi Diajukan Ulang' : 'Dokumen Verifikasi Baru',
                'message' => $this->getNotificationMessage(),
                'icon' => 'fas fa-file-upload',
                'color' => 'info'
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'seller.documents.uploaded';
    }

    private function getNotificationMessage(): string
    {
        $action = $this->isResubmission ? 'mengajukan ulang' : 'mengunggah';
        $documents = count($this->uploadedDocuments) > 1 
            ? 'dokumen verifikasi' 
            : 'dokumen ' . $this->uploadedDocuments[0];
            
        return "{$this->seller->name} telah {$action} {$documents} dan memerlukan review.";
    }
}