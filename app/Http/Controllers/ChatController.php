<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $search = $request->get('search');
            
            if ($search) {
                $conversations = $this->chatService->searchConversations($user, $search);
            } else {
                $conversations = $this->chatService->getConversationsForUser($user);
            }
            
            $unreadCount = $this->chatService->getUnreadMessageCount($user);
            
            return view('chat.index', compact('conversations', 'unreadCount', 'search'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat daftar chat: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->back()->with('error', 'Gagal memuat daftar chat. Silakan coba lagi.');
        }
    }

    public function show(Conversation $conversation)
    {
        try {
            $user = auth()->user();
            
            if (!$this->chatService->canUserAccessConversation($user, $conversation)) {
                abort(403, 'Anda tidak memiliki akses ke percakapan ini.');
            }
            
            $messages = $this->chatService->getMessagesForConversation($conversation);
            $this->chatService->markConversationAsRead($conversation, $user);
            
            $otherParticipant = $conversation->getOtherParticipant($user);
            
            return view('chat.show', compact('conversation', 'messages', 'otherParticipant'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat percakapan: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
            ]);
            
            return redirect()->route('chat.index')->with('error', 'Gagal memuat percakapan. Silakan coba lagi.');
        }
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Pesan tidak boleh kosong.',
            'content.max' => 'Pesan terlalu panjang (maksimal 1000 karakter).',
        ]);

        try {
            $user = auth()->user();
            
            if (!$this->chatService->canUserSendMessage($user, $conversation)) {
                abort(403, 'Anda tidak dapat mengirim pesan di percakapan ini.');
            }
            
            $message = $this->chatService->sendMessage($conversation, $user, $request->content);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'content' => $message->content,
                        'sender_id' => $message->sender_id,
                        'sender_name' => $message->sender->name,
                        'created_at' => $message->created_at->format('H:i'),
                    ],
                ]);
            }
            
            return redirect()->route('chat.show', $conversation)->with('success', 'Pesan berhasil dikirim!');
        } catch (\Exception $e) {
            Log::error('Gagal mengirim pesan: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'content' => $request->content,
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim pesan. Silakan coba lagi.',
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal mengirim pesan. Silakan coba lagi.');
        }
    }

    // TAMBAHAN: Method untuk load pesan lama (older messages)
    public function getOlderMessages(Request $request, Conversation $conversation)
    {
        try {
            $user = auth()->user();
            
            if (!$this->chatService->canUserAccessConversation($user, $conversation)) {
                abort(403, 'Anda tidak memiliki akses ke percakapan ini.');
            }
            
            $beforeId = $request->get('before', 0);
            $limit = 20;
            
            $messages = $this->chatService->getOlderMessages($conversation, $beforeId, $limit);
            
            // Check if there are more messages
            $hasMore = $messages->count() === $limit;
            
            $messagesData = $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'created_at' => $message->created_at->format('H:i'),
                    'read_at' => $message->read_at,
                ];
            });
            
            return response()->json([
                'success' => true,
                'messages' => $messagesData,
                'hasMore' => $hasMore,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memuat pesan lama: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'before_id' => $request->get('before'),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pesan lama.',
            ], 500);
        }
    }

    public function startChat()
    {
        try {
            $user = auth()->user();
            
            if (!$user->isSeller()) {
                abort(403, 'Hanya seller yang dapat memulai chat dengan admin.');
            }
            
            $admin = $this->chatService->getAdminUser();
            $conversation = $this->chatService->findOrCreateConversation($user, $admin);
            
            return redirect()->route('chat.show', $conversation);
        } catch (\Exception $e) {
            Log::error('Gagal memulai chat: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->back()->with('error', 'Gagal memulai chat dengan admin. Silakan coba lagi.');
        }
    }

    public function markAsRead(Conversation $conversation)
    {
        try {
            $user = auth()->user();
            
            if (!$this->chatService->canUserAccessConversation($user, $conversation)) {
                abort(403, 'Anda tidak memiliki akses ke percakapan ini.');
            }
            
            $this->chatService->markConversationAsRead($conversation, $user);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Gagal menandai pesan sebagai dibaca: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
            ]);
            
            return response()->json(['success' => false], 500);
        }
    }
    public function typing(Conversation $conversation)
    {
        try {
            $user = auth()->user();
            
            if (!$this->chatService->canUserAccessConversation($user, $conversation)) {
                abort(403, 'Anda tidak memiliki akses ke percakapan ini.');
            }
            
            event(new \App\Events\UserTyping($conversation->id, $user));
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function stopTyping(Conversation $conversation)
    {
        try {
            $user = auth()->user();
            
            if (!$this->chatService->canUserAccessConversation($user, $conversation)) {
                abort(403, 'Anda tidak memiliki akses ke percakapan ini.');
            }
            
            event(new \App\Events\UserStoppedTyping($conversation->id, $user));
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}