<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\SupportTicketService;
use App\Models\User;
use App\Requests\SupportTicket\AdminResponseRequest;
use App\Requests\SupportTicket\AssignTicketRequest;
use App\Requests\SupportTicket\ResolveTicketRequest;
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
     * Menampilkan daftar semua tiket (admin view)
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
            'category' => $request->get('category'),
            'assigned_to' => $request->get('assigned_to'),
            'search' => $request->get('search'),
        ];
        $tickets = $this->ticketService->getAllTickets($filters);
        $stats = $this->ticketService->getTicketStats();
        $adminUsers = User::where('role', 'admin')->get();
        return view('admin.support.index', compact('tickets', 'stats', 'filters', 'adminUsers'));
    }
    /**
     * Menampilkan detail tiket (admin view)
     */
    public function show(int $id)
    {
        $ticket = $this->ticketService->getTicketDetail($id);
        if (!$ticket) {
            abort(404, 'Tiket tidak ditemukan');
        }
        $adminUsers = User::where('role', 'admin')->get();
        return view('admin.support.show', compact('ticket', 'adminUsers'));
    }
    /**
     * Assign tiket ke admin
     */
    public function assign(AssignTicketRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            $this->ticketService->assignTicket($id, $data['admin_id']);
            return back()->with('success', 'Tiket berhasil di-assign.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Update status tiket
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting_user,resolved,closed'
        ]);
        try {
            $this->ticketService->updateTicketStatus($id, $request->status, Auth::id());
            $statusLabel = match($request->status) {
                'open' => 'Terbuka',
                'in_progress' => 'Dalam Proses',
                'waiting_user' => 'Menunggu User',
                'resolved' => 'Diselesaikan',
                'closed' => 'Ditutup',
            };
            return back()->with('success', "Status tiket berhasil diubah menjadi: {$statusLabel}");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Resolve tiket dengan resolution
     */
    public function resolve(ResolveTicketRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            $this->ticketService->resolveTicket($id, $data['resolution'], Auth::id());
            return back()->with('success', 'Tiket berhasil diselesaikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Menambahkan respons admin ke tiket
     */
    /**
 * @param \Illuminate\Http\Request $request
 */
    public function addResponse(AdminResponseRequest $request, int $id)
    {
        $ticket = $this->ticketService->getTicketDetail($id);
        if (!$ticket) {
            abort(404, 'Tiket tidak ditemukan');
        }
        if (!$ticket->canReceiveResponse()) {
            return back()->with('error', 'Tidak dapat menambahkan respons ke tiket yang sudah diselesaikan atau ditutup.');
        }
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['is_admin_response'] = true;
            if ($request->hasFile('attachments')) {
                $data['attachments'] = $request->file('attachments');
            }
            $this->ticketService->addResponse($id, $data);
            if ($request->filled('change_status')) {
                $this->ticketService->updateTicketStatus($id, $request->change_status, Auth::id());
            }
            return back()->with('success', 'Respons berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Dashboard tiket untuk admin
     */
    public function dashboard()
    {
        $stats = $this->ticketService->getTicketStats();
        $highPriorityTickets = $this->ticketService->getAllTickets([])
            ->whereIn('priority', ['high', 'urgent'])
            ->where('status', '!=', 'closed')
            ->take(10);
        $unassignedTickets = $this->ticketService->getAllTickets([])
            ->whereNull('assigned_to')
            ->where('status', '!=', 'closed')
            ->take(10);
        return view('admin.support.dashboard', compact(
            'stats', 
            'highPriorityTickets', 
            'unassignedTickets'
        ));
    }
    /**
     * Bulk actions untuk multiple tikets
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
            'action' => 'required|in:assign,close,change_status',
            'admin_id' => 'required_if:action,assign|exists:users,id',
            'status' => 'required_if:action,change_status|in:open,in_progress,waiting_user,resolved,closed'
        ]);
        try {
            $ticketIds = $request->ticket_ids;
            $successCount = 0;
            foreach ($ticketIds as $ticketId) {
                switch ($request->action) {
                    case 'assign':
                        $this->ticketService->assignTicket($ticketId, $request->admin_id);
                        $successCount++;
                        break;
                    case 'close':
                        $this->ticketService->updateTicketStatus($ticketId, 'closed', Auth::id());
                        $successCount++;
                        break;
                    case 'change_status':
                        $this->ticketService->updateTicketStatus($ticketId, $request->status, Auth::id());
                        $successCount++;
                        break;
                }
            }
            $actionLabel = match($request->action) {
                'assign' => 'di-assign',
                'close' => 'ditutup',
                'change_status' => 'status diubah',
            };
            return back()->with('success', "{$successCount} tiket berhasil {$actionLabel}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Export tickets data
     */
    public function export(Request $request)
    {
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }
}