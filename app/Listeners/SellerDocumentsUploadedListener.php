<?php
namespace App\Listeners;

use App\Events\SellerDocumentsUploaded;
use App\Services\NotificationService;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SellerDocumentsUploadedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(SellerDocumentsUploaded $event): void
    {
        try {
            // Kirim notifikasi ke semua admin
            $admins = User::where('role', UserRole::ADMIN)->get();

            $additionalData = [
                'seller_id' => $event->seller->id,
                'seller_name' => $event->seller->name,
                'uploaded_documents' => $event->uploadedDocuments,
                'is_resubmission' => $event->isResubmission,
                'verification_url' => route('admin.sellers.verification'),
            ];

            $title = $event->isResubmission 
                ? 'Dokumen Verifikasi Diajukan Ulang' 
                : 'Dokumen Verifikasi Baru';

            $documentsText = $this->getDocumentsText($event->uploadedDocuments);
            $action = $event->isResubmission ? 'mengajukan ulang' : 'mengunggah';
            $message = "{$event->seller->name} telah {$action} {$documentsText} dan memerlukan review segera.";

            foreach ($admins as $admin) {
                $this->notificationService->createForUser(
                    $admin->id,
                    'seller_documents_uploaded',
                    $title,
                    $message,
                    $additionalData
                );
            }

            Log::info('Seller documents uploaded notifications sent to admins', [
                'seller_id' => $event->seller->id,
                'uploaded_documents' => $event->uploadedDocuments,
                'is_resubmission' => $event->isResubmission,
                'admin_count' => $admins->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create seller documents uploaded notifications', [
                'seller_id' => $event->seller->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getDocumentsText(array $documents): string
    {
        if (empty($documents)) {
            return 'dokumen verifikasi';
        }

        $documentNames = [];
        foreach ($documents as $doc) {
            $documentNames[] = match ($doc) {
                'ktp' => 'foto KTP',
                'passbook' => 'foto buku tabungan',
                default => $doc
            };
        }

        if (count($documentNames) === 1) {
            return $documentNames[0];
        }

        if (count($documentNames) === 2) {
            return implode(' dan ', $documentNames);
        }

        $last = array_pop($documentNames);
        return implode(', ', $documentNames) . ', dan ' . $last;
    }
}