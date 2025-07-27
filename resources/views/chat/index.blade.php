<x-layouts.plain-app>
    <x-slot name="title">Chat</x-slot>
    
    <div class="container mx-auto px-4 py-8" x-data="chatManager()">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-neutral-900">Chat</h1>
                @if(auth()->user()->isSeller())
                    <a href="{{ route('chat.start') }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-5 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
                        <i class="fas fa-comments mr-2"></i>
                        Chat dengan Admin
                    </a>
                @endif
            </div>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <form action="{{ route('chat.index') }}" method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-neutral-400"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="Cari percakapan..."
                       class="w-full pl-10 pr-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            </form>
        </div>

        <!-- Conversations List -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div x-ref="conversationsContainer">
                @if($conversations->count() > 0)
                    <div class="divide-y divide-neutral-200">
                        @foreach($conversations as $conversation)
                            @php
                                $otherParticipant = $conversation->getOtherParticipant(auth()->user());
                                $unreadCount = $conversation->unreadMessagesCount(auth()->id());
                                $latestMessage = $conversation->latestMessage;
                            @endphp
                            
                            <div class="conversation-item p-6 hover:bg-neutral-50 transition cursor-pointer"
                                 data-conversation-id="{{ $conversation->id }}"
                                 onclick="window.location='{{ route('chat.show', $conversation) }}'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 h-12 w-12 mr-4">
                                            @if($otherParticipant->avatar)
                                                <img class="h-12 w-12 rounded-full object-cover" 
                                                     src="{{ $otherParticipant->avatar }}" 
                                                     alt="{{ $otherParticipant->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-primary-600"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Conversation Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="text-sm font-medium text-neutral-900 truncate">
                                                    {{ $otherParticipant->name }}
                                                    <span class="ml-2 text-xs text-neutral-500">
                                                        ({{ $otherParticipant->role_label }})
                                                    </span>
                                                </h3>
                                                <span class="conversation-time text-xs text-neutral-500">
                                                    @if($conversation->last_message_at)
                                                        {{ $conversation->last_message_at->diffForHumans() }}
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <p class="conversation-preview text-sm text-neutral-600 truncate">
                                                @if($latestMessage)
                                                    @if($latestMessage->sender_id === auth()->id())
                                                        <span class="text-neutral-500">Anda: </span>
                                                    @endif
                                                    {{ Str::limit($latestMessage->content, 60) }}
                                                @else
                                                    Belum ada pesan
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Unread Badge -->
                                    <div class="flex-shrink-0 ml-4">
                                        <span class="conversation-unread inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-medium bg-primary-600 text-white min-w-[20px] {{ $unreadCount > 0 ? '' : 'hidden' }}">
                                            {{ $unreadCount }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-neutral-300 text-6xl mb-4">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="mt-2 text-lg font-medium text-neutral-900">Belum ada percakapan</h3>
                        <p class="mt-1 text-sm text-neutral-500">
                            @if($search)
                                Tidak ada percakapan yang cocok dengan pencarian "{{ $search }}".
                            @else
                                @if(auth()->user()->isSeller())
                                    Mulai chat dengan admin untuk mendapatkan bantuan.
                                @else
                                    Percakapan akan muncul ketika seller menghubungi Anda.
                                @endif
                            @endif
                        </p>
                        @if(auth()->user()->isSeller() && !$search)
                            <div class="mt-6">
                                <a href="{{ route('chat.start') }}"
                                   class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition">
                                    <i class="fas fa-comments mr-2"></i>
                                    Mulai Chat dengan Admin
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function chatManager() {
            return {
                currentUserId: {{ auth()->id() }},
                currentUserRole: '{{ auth()->user()->role->value }}',
                conversations: {},
                processedMessages: new Set(), // Track processed messages to prevent duplicates
                
                init() {
                    console.log('Chat index manager initialized for user:', this.currentUserId, 'role:', this.currentUserRole);
                    this.setupRealtimeListeners();
                    this.initializeConversationsData();
                },
                
                initializeConversationsData() {
                    // Simpan data conversations yang ada untuk tracking
                    const conversationItems = document.querySelectorAll('.conversation-item');
                    conversationItems.forEach(item => {
                        const conversationId = item.dataset.conversationId;
                        if (conversationId) {
                            this.conversations[conversationId] = {
                                element: item,
                                lastMessageTime: new Date().getTime()
                            };
                        }
                    });
                },
                
                setupRealtimeListeners() {
                    // Listen untuk pesan baru di semua conversations
                    if (this.currentUserRole === 'admin') {
                        // Admin listen ke channel admin untuk pesan dari seller
                        window.Echo.channel('admin-notifications')
                            .listen('.message.sent', (e) => {
                                console.log('Admin received message from seller:', e);
                                this.handleNewMessage(e);
                            });
                        
                        // Admin juga listen ke channel personal (backup/redundancy)
                        window.Echo.channel(`user.${this.currentUserId}`)
                            .listen('.message.sent', (e) => {
                                console.log('Admin received direct message:', e);
                                this.handleNewMessage(e);
                            });
                    } else {
                        // Seller listen ke channel personal saja
                        window.Echo.channel(`user.${this.currentUserId}`)
                            .listen('.message.sent', (e) => {
                                console.log('Seller received message:', e);
                                this.handleNewMessage(e);
                            });
                    }
                    
                    // Listen untuk update unread count
                    window.Echo.channel(`unread-count.${this.currentUserId}`)
                        .listen('.count.updated', (e) => {
                            console.log('Unread count updated:', e);
                            // Update total unread count di UI jika ada
                        });
                    
                    // Connection status
                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        console.log('✅ Connected to Pusher - Chat Index');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('disconnected', () => {
                        console.log('❌ Disconnected from Pusher - Chat Index');
                    });
                },
                
                handleNewMessage(messageData) {
                    console.log('Processing new message in chat index:', messageData);
                    
                    // Jangan proses pesan dari diri sendiri
                    if (messageData.sender_id === this.currentUserId) {
                        console.log('Ignoring message from self');
                        return;
                    }
                    
                    // Prevent duplicate processing of the same message
                    const messageKey = `${messageData.id}-${messageData.conversation_id}`;
                    if (this.processedMessages.has(messageKey)) {
                        console.log('Message already processed, skipping:', messageKey);
                        return;
                    }
                    
                    // Mark message as processed
                    this.processedMessages.add(messageKey);
                    
                    // Clean up old processed messages (keep only last 100)
                    if (this.processedMessages.size > 100) {
                        const messagesArray = Array.from(this.processedMessages);
                        this.processedMessages.clear();
                        messagesArray.slice(-50).forEach(key => this.processedMessages.add(key));
                    }
                    
                    const conversationId = messageData.conversation_id;
                    
                    // Update conversation yang ada
                    this.updateExistingConversation(conversationId, messageData);
                    
                    // Pindahkan conversation ke atas
                    this.moveConversationToTop(conversationId);
                    
                    // Play notification sound
                    this.playNotificationSound();
                },
                
                updateExistingConversation(conversationId, messageData) {
                    const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    
                    if (conversationElement) {
                        // Update preview message
                        const previewElement = conversationElement.querySelector('.conversation-preview');
                        if (previewElement) {
                            previewElement.innerHTML = this.escapeHtml(this.truncateMessage(messageData.content, 60));
                        }
                        
                        // Update timestamp
                        const timeElement = conversationElement.querySelector('.conversation-time');
                        if (timeElement) {
                            timeElement.textContent = this.formatTime(messageData.created_at);
                        }
                        
                        // Update unread count
                        const unreadElement = conversationElement.querySelector('.conversation-unread');
                        if (unreadElement) {
                            // Get current unread count dan tambah 1
                            let currentCount = parseInt(unreadElement.textContent) || 0;
                            currentCount += 1;
                            
                            unreadElement.textContent = currentCount;
                            unreadElement.classList.remove('hidden');
                        }
                        
                        // Add visual indicator untuk pesan baru
                        conversationElement.classList.add('bg-blue-50', 'border-l-4', 'border-blue-500');
                        setTimeout(() => {
                            conversationElement.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500');
                        }, 3000);
                    } else {
                        // Jika conversation tidak ada, refresh halaman
                        console.log('Conversation not found, refreshing page');
                        window.location.reload();
                    }
                },
                
                moveConversationToTop(conversationId) {
                    const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                    const container = conversationElement?.parentNode;
                    
                    if (conversationElement && container) {
                        // Pindahkan ke atas dengan animasi
                        container.insertBefore(conversationElement, container.firstChild);
                        
                        // Add highlight effect
                        conversationElement.style.backgroundColor = '#f0f9ff';
                        setTimeout(() => {
                            conversationElement.style.backgroundColor = '';
                        }, 2000);
                    }
                },
                
                playNotificationSound() {
                    try {
                        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.value = 800;
                        oscillator.type = 'sine';
                        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                    } catch (error) {
                        console.log('Cannot play notification sound:', error);
                    }
                },
                
                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = now - date;
                    
                    if (diff < 60000) {
                        return 'Baru saja';
                    }
                    if (diff < 3600000) {
                        const minutes = Math.floor(diff / 60000);
                        return `${minutes} menit lalu`;
                    }
                    if (date.toDateString() === now.toDateString()) {
                        return date.toLocaleTimeString('id-ID', { 
                            hour: '2-digit', 
                            minute: '2-digit' 
                        });
                    }
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },
                
                truncateMessage(message, length = 50) {
                    if (message.length <= length) return message;
                    return message.substring(0, length) + '...';
                },
                
                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                },
                
                async refreshUnreadCount() {
                    try {
                        const response = await fetch('/chat/unread-count', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Refreshed unread count:', data.count);
                            // Update global unread count jika diperlukan
                        }
                    } catch (error) {
                        console.error('Gagal refresh unread count:', error);
                    }
                }
            }
        }
    </script>
</x-layouts.plain-app>