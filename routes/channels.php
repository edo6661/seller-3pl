<?php
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    return $conversation->seller_id === $user->id || $conversation->admin_id === $user->id;
});
Broadcast::channel('user.{userId}', function ($user, $userId) {
    if ($user->isAdmin()) {
        return true;
    }
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('unread-count.{userId}', function ($user, $userId) {
    if ($user->isAdmin()) {
        return true;
    }
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('admin-notifications', function ($user) {
    return $user->isAdmin();
});
Broadcast::channel('admin-global', function ($user) {
    return $user->isAdmin();
});