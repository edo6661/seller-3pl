<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Requests\SupportTicket\AddResponseRequest;
use App\Requests\SupportTicket\CreateTicketRequest;
use App\Services\SupportTicketService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SupportTicketController extends Controller
{
    protected SupportTicketService $ticketService;
    public function __construct(SupportTicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }
    /**
     * Mendapatkan ID seller berdasarkan user yang login
     */
    private function getSellerId()
    {
        $user = Auth::user();
        $membership = $user->memberOf()->first();
        if ($membership) {
            return $membership->seller_id;
        }
        if ($user->isSeller()) {
            return $user->id;
        }
        abort(403, 'Akses tidak diizinkan.');
    }
    /**
     * Mendapatkan instance seller
     */
    private function getSeller(): User
    {
        $sellerId = $this->getSellerId();
        return User::findOrFail($sellerId);
    }
    /**
     * Menampilkan daftar tiket user
     */
    public function index(Request $request)
    {
        $sellerId = $this->getSellerId();
        $filters = [
            'status' => $request->get('status'),
            'category' => $request->get('category'),
            'ticket_type' => $request->get('ticket_type'),
            'search' => $request->get('search'),
        ];
        $tickets = $this->ticketService->getUserTickets($sellerId, $filters);
        $stats = $this->ticketService->getTicketStats($sellerId);
        return view('seller.support.index', compact('tickets', 'stats', 'filters'));
    }
    /**
     * Form untuk membuat tiket baru
     */
    public function create()
    {
        return view('seller.support.create');
    }
    /**
     * Menyimpan tiket baru
     */
    /**
 * @param \Illuminate\Http\Request $request
 */
    public function store(CreateTicketRequest $request)
    {
        try {
            $sellerId = $this->getSellerId();
            $seller = $this->getSeller(); 
            $data = $request->validated();
            $data['user_id'] = $sellerId;
            if ($request->hasFile('attachments')) {
                $data['attachments'] = $request->file('attachments');
            }
            $ticket = $this->ticketService->createTicket($data);
            return redirect()
                ->route('seller.support.show', $ticket->id)
                ->with('success', 'Tiket berhasil dibuat! Nomor tiket: ' . $ticket->ticket_number);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Menampilkan detail tiket
     */
    public function show(int $id)
    {
        $sellerId = $this->getSellerId();
        $ticket = $this->ticketService->getTicketDetail($id, $sellerId);
        if (!$ticket) {
            abort(404, 'Tiket tidak ditemukan');
        }
        abort_unless($ticket->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke tiket ini.');
        foreach ($ticket->responses as $response) {
            if ($response->is_admin_response && !$response->is_read) {
                $this->ticketService->markResponseAsRead($response->id);
            }
        }
        return view('seller.support.show', compact('ticket'));
    }
    /**
     * Menambahkan respons ke tiket
     */
    /**
 * @param \Illuminate\Http\Request $request
 */
    public function addResponse(AddResponseRequest $request, int $id)
    {
        $sellerId = $this->getSellerId();
        $ticket = $this->ticketService->getTicketDetail($id, $sellerId);
        if (!$ticket) {
            abort(404, 'Tiket tidak ditemukan');
        }
        abort_unless($ticket->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke tiket ini.');
        if ($ticket->isClosed()) {
            return back()->with('error', 'Tidak dapat menambahkan respons ke tiket yang sudah ditutup.');
        }
        try {
            $data = $request->validated();
            $data['user_id'] = $sellerId;
            $data['is_admin_response'] = false;
            if ($request->hasFile('attachments')) {
                $data['attachments'] = $request->file('attachments');
            }
            $this->ticketService->addResponse($id, $data);
            return back()->with('success', 'Respons berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * AJAX: Cari pickup request berdasarkan kode/nomor resi
     */
    public function searchPickupRequest(Request $request)
    {
        $identifier = $request->get('identifier');
        if (!$identifier) {
            return response()->json([
                'success' => false,
                'message' => 'Kode pickup atau nomor resi harus diisi'
            ]);
        }
        try {
            $sellerId = $this->getSellerId();
            $pickupRequest = $this->ticketService->findPickupRequest($identifier);
            if (!$pickupRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ]);
            }
            if ($pickupRequest->user_id !== $sellerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ]);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'pickup_code' => $pickupRequest->pickup_code,
                    'tracking_number' => $pickupRequest->courier_tracking_number,
                    'status' => $pickupRequest->status,
                    'recipient_name' => $pickupRequest->recipient_name,
                    'total_amount' => $pickupRequest->total_amount,
                    'created_at' => $pickupRequest->created_at->format('d M Y'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data'
            ]);
        }
    }
    /**
     * Mendapatkan jumlah unread responses
     */
    public function getUnreadCount()
    {
        try {
            $sellerId = $this->getSellerId();
            $count = $this->ticketService->getUnreadResponsesCount($sellerId);
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }
    /**
     * Reopen closed ticket (if allowed)
     */
    public function reopen(int $id)
    {
        $sellerId = $this->getSellerId();
        $ticket = $this->ticketService->getTicketDetail($id, $sellerId);
        if (!$ticket) {
            abort(404, 'Tiket tidak ditemukan');
        }
        abort_unless($ticket->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke tiket ini.');
        if (!$ticket->canBeReopened()) {
            return back()->with('error', 'Tiket ini tidak dapat dibuka kembali');
        }
        try {
            $this->ticketService->updateTicketStatus($id, 'open');
            return back()->with('success', 'Tiket berhasil dibuka kembali');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}