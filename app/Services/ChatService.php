<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChatService
{
    public function getConversationsForUser(User $user): Collection
    {
        $query = Conversation::with(['seller', 'admin', 'latestMessage.sender'])
                            ->forUser($user)
                            ->orderByDesc('last_message_at');
                            
        return $query->get();
    }

    public function findOrCreateConversation(User $seller, User $admin): Conversation
    {
        if (!$seller->isSeller() || !$admin->isAdmin()) {
            throw new \InvalidArgumentException('Invalid user roles for conversation');
        }

        return Conversation::firstOrCreate(
            [
                'seller_id' => $seller->id,
                'admin_id' => $admin->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );
    }

    public function sendMessage(Conversation $conversation, User $sender, string $content): Message
    {
        if (!$this->canUserSendMessage($sender, $conversation)) {
            throw new \InvalidArgumentException('User not authorized to send message in this conversation');
        }

        return DB::transaction(function () use ($conversation, $sender, $content) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'content' => trim($content),
            ]);

            $conversation->update([
                'last_message_at' => $message->created_at,
            ]);

            event(new MessageSent($message));

            return $message;
        });
    }

    // PERBAIKAN: Ubah urutan pesan dari oldest ke newest (chronological order)
    public function getMessagesForConversation(Conversation $conversation, int $limit = 50): Collection
    {
        return $conversation->messages()
                           ->with('sender')
                           ->oldest('created_at') // Ubah dari latest ke oldest
                           ->limit($limit)
                           ->get(); // Hapus reverse() dan values()
    }

    public function markConversationAsRead(Conversation $conversation, User $user): void
    {
        if (!$this->canUserAccessConversation($user, $conversation)) {
            throw new \InvalidArgumentException('User not authorized to access this conversation');
        }

        $conversation->markMessagesAsRead($user->id);
    }

    public function canUserAccessConversation(User $user, Conversation $conversation): bool
    {
        return $conversation->seller_id === $user->id || 
               $conversation->admin_id === $user->id;
    }

    public function canUserSendMessage(User $user, Conversation $conversation): bool
    {
        return $this->canUserAccessConversation($user, $conversation);
    }

    public function getUnreadMessageCount(User $user): int
    {
        return $user->getTotalUnreadMessages();
    }

    public function searchConversations(User $user, string $search): Collection
    {
        $query = Conversation::forUser($user)
                            ->with(['seller', 'admin', 'latestMessage.sender']);

        if ($user->isAdmin()) {
            $query->whereHas('seller', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        } else {
            $query->whereHas('admin', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('last_message_at')->get();
    }

    public function getAdminUser(): User
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            throw new \Exception('No admin user found');
        }
        
        return $admin;
    }

    public function deleteConversation(Conversation $conversation): bool
    {
        return DB::transaction(function () use ($conversation) {
            $conversation->messages()->delete();
            return $conversation->delete();
        });
    }

    // TAMBAHAN: Method untuk load more messages (pagination)
    public function getOlderMessages(Conversation $conversation, int $beforeMessageId, int $limit = 20): Collection
    {
        return $conversation->messages()
                           ->with('sender')
                           ->where('id', '<', $beforeMessageId)
                           ->oldest('created_at')
                           ->limit($limit)
                           ->get();
    }
}