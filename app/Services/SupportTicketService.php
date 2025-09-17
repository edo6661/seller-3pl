<?php
namespace App\Services;
use App\Events\SupportTicketCreated;
use App\Events\SupportTicketReplied;
use App\Models\SupportTicket;
use App\Models\TicketResponse;
use App\Models\PickupRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class SupportTicketService
{
    public function createTicket(array $data): SupportTicket
    {
        return DB::transaction(function () use ($data) {
            if ($data['ticket_type'] === 'shipment') {
                if (!empty($data['pickup_code'])) {
                    $pickupRequest = PickupRequest::where('pickup_code', $data['pickup_code'])->first();
                    if ($pickupRequest) {
                        $data['pickup_request_id'] = $pickupRequest->id;
                        $data['tracking_number'] = $pickupRequest->courier_tracking_number;
                    }
                } elseif (!empty($data['tracking_number'])) {
                    $pickupRequest = PickupRequest::where('courier_tracking_number', $data['tracking_number'])->first();
                    if ($pickupRequest) {
                        $data['pickup_request_id'] = $pickupRequest->id;
                    }
                }
            }
            if (!empty($data['attachments'])) {
                $attachments = [];
                foreach ($data['attachments'] as $file) {
                    $path = $file->store('support_tickets/' . $data['user_id'], 'r2');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getClientMimeType(),
                    ];
                }
                $data['attachments'] = $attachments;
            }
            $ticket = SupportTicket::create($data);
            $user = User::find($data['user_id']);
            event(new SupportTicketCreated($ticket, $user));
            return $ticket;
        });
    }
    public function addResponse(int $ticketId, array $data): TicketResponse
    {
        return DB::transaction(function () use ($ticketId, $data) {
            $ticket = SupportTicket::findOrFail($ticketId);
            if (!$ticket->canReceiveResponse()) {
                throw new \Exception('Tidak dapat menambahkan respons ke tiket yang sudah selesai atau ditutup.');
            }
            $user = User::find($data['user_id']);
            if (!empty($data['attachments'])) {
                $attachments = [];
                foreach ($data['attachments'] as $file) {
                    $path = $file->store('ticket_responses/' . $ticket->user_id, 'r2');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getClientMimeType(),
                    ];
                }
                $data['attachments'] = $attachments;
            }
            $response = TicketResponse::create([
                'support_ticket_id' => $ticketId,
                'user_id' => $data['user_id'],
                'message' => $data['message'],
                'attachments' => $data['attachments'] ?? null,
                'is_admin_response' => $data['is_admin_response'] ?? false,
            ]);
            if ($data['is_admin_response'] && $ticket->status === 'open') {
                $ticket->update(['status' => 'in_progress']);
            } elseif (!$data['is_admin_response'] && $ticket->status === 'waiting_user') {
                $ticket->update(['status' => 'in_progress']);
            }
            event(new SupportTicketReplied($ticket, $response, $user));
            return $response;
        });
    }
    public function getUserTickets(int $userId, array $filters = []): Collection
    {
        $query = SupportTicket::where('user_id', $userId)
            ->with(['pickupRequest', 'responses' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('created_at', 'desc');
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['ticket_type'])) {
            $query->where('ticket_type', $filters['ticket_type']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('ticket_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
        return $query->get();
    }
    public function getAllTickets(array $filters = []): Collection
    {
        $query = SupportTicket::with(['user', 'pickupRequest', 'assignedAdmin'])
            ->orderBy('created_at', 'desc');
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('ticket_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('user', function ($userQuery) use ($filters) {
                      $userQuery->where('name', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }
        return $query->get();
    }
    public function getTicketDetail(int $ticketId, int $userId = null): ?SupportTicket
    {
        $query = SupportTicket::with([
            'user',
            'pickupRequest',
            'assignedAdmin',
            'responses.user'
        ]);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->find($ticketId);
    }
    public function updateTicketStatus(int $ticketId, string $status, int $adminId = null): SupportTicket
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $updateData = ['status' => $status];
        if ($status === 'resolved') {
            $updateData['resolved_at'] = now();
        }
        if ($adminId && !$ticket->assigned_to) {
            $updateData['assigned_to'] = $adminId;
        }
        $ticket->update($updateData);
        return $ticket->fresh();
    }
    public function assignTicket(int $ticketId, int $adminId): SupportTicket
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'assigned_to' => $adminId,
            'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);
        return $ticket->fresh();
    }
    public function resolveTicket(int $ticketId, string $resolution, int $adminId): SupportTicket
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolved_at' => now(),
            'assigned_to' => $ticket->assigned_to ?: $adminId,
        ]);
        return $ticket->fresh();
    }
    public function findPickupRequest(string $identifier): ?PickupRequest
    {
        return PickupRequest::where('pickup_code', $identifier)
            ->orWhere('courier_tracking_number', $identifier)
            ->first();
    }
    public function getTicketStats(int $userId = null): array
    {
        $query = SupportTicket::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return [
            'total' => $query->count(),
            'open' => $query->clone()->where('status', 'open')->count(),
            'in_progress' => $query->clone()->where('status', 'in_progress')->count(),
            'waiting_user' => $query->clone()->where('status', 'waiting_user')->count(),
            'resolved' => $query->clone()->where('status', 'resolved')->count(),
            'closed' => $query->clone()->where('status', 'closed')->count(),
            'high_priority' => $query->clone()->whereIn('priority', ['high', 'urgent'])->count(),
        ];
    }
    public function markResponseAsRead(int $responseId): void
    {
        TicketResponse::findOrFail($responseId)->markAsRead();
    }
    public function getUnreadResponsesCount(int $userId): int
    {
        return TicketResponse::whereHas('supportTicket', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('is_admin_response', true)
        ->where('is_read', false)
        ->count();
    }
    public function deleteAttachment(string $path): bool
    {
        return Storage::disk('r2')->delete($path);
    }
}