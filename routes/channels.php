<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// Channel untuk user authentication
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk conversation
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    return $conversation->seller_id === $user->id || $conversation->admin_id === $user->id;
});

// Channel untuk user notification (chat notification) - PERBAIKAN
Broadcast::channel('user.{userId}', function ($user, $userId) {
    // Admin bisa mendengarkan notifikasi dari semua user
    if ($user->isAdmin()) {
        return true;
    }
    
    // User hanya bisa mendengarkan notifikasi mereka sendiri
    return (int) $user->id === (int) $userId;
});

// Channel untuk unread count updates
Broadcast::channel('unread-count.{userId}', function ($user, $userId) {
    // Admin bisa mengakses unread count semua user untuk dashboard
    if ($user->isAdmin()) {
        return true;
    }
    
    return (int) $user->id === (int) $userId;
});

// Channel khusus untuk admin notifications - PERBAIKAN
Broadcast::channel('admin-notifications', function ($user) {
    return $user->isAdmin();
});

// TAMBAHAN: Channel untuk semua admin user
Broadcast::channel('admin-global', function ($user) {
    return $user->isAdmin();
});