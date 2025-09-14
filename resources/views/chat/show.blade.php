<x-layouts.plain-app>
    <x-slot name="title">Chat dengan {{ $otherParticipant->name }}</x-slot>
    <div class="container mx-auto px-4 py-8 max-w-4xl" x-data="chatRoom({{ $conversation->id }})">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-t-xl border-b border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('chat.index') }}" 
                       class="text-neutral-600 hover:text-neutral-800 mr-4 transition">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div class="flex-shrink-0 h-10 w-10 mr-3">
                        @if($otherParticipant->avatar)
                            <img class="h-10 w-10 rounded-full object-cover" 
                                 src="{{ $otherParticipant->avatar }}" 
                                 alt="{{ $otherParticipant->name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                <i class="fas fa-user text-primary-600"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-neutral-900">{{ $otherParticipant->name }}</h1>
                        <p class="text-sm text-neutral-500">{{ $otherParticipant->role_label }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span x-show="isOnline" class="flex items-center text-sm text-green-600">
                        <span class="w-2 h-2 bg-green-600 rounded-full mr-2 animate-pulse"></span>
                        Online
                    </span>
                    <span x-show="!isOnline" class="flex items-center text-sm text-gray-500">
                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                        Offline
                    </span>
                </div>
            </div>
        </div>
        <!-- Messages Container -->
        <div class="bg-white shadow-sm" style="height: 500px;">
            <div x-ref="messagesContainer" 
                 class="h-full overflow-y-auto p-6 space-y-4"
                 @scroll="handleScroll">
                <!-- Load more button -->
                <div x-show="hasOlderMessages && !isLoadingOlder" class="text-center py-2">
                    <button @click="loadOlderMessages" 
                            class="text-primary-600 hover:text-primary-700 text-sm font-medium bg-primary-50 px-4 py-2 rounded-lg">
                        <i class="fas fa-chevron-up mr-1"></i>
                        Muat pesan sebelumnya
                    </button>
                </div>
                <!-- Loading indicator -->
                <div x-show="isLoadingOlder" class="text-center py-2">
                    <i class="fas fa-spinner fa-spin text-neutral-400"></i>
                    <span class="ml-2 text-sm text-neutral-500">Memuat pesan...</span>
                </div>
                <!-- Messages -->
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}" 
                             data-message-id="{{ $message->id }}">
                            <div class="max-w-3xl">
                                <div class="px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() 
                                    ? 'bg-primary-600 text-white' 
                                    : 'bg-neutral-100 text-neutral-900' }}">
                                    <p class="text-sm">{{ $message->content }}</p>
                                </div>
                                <div class="flex items-center mt-1 text-xs text-neutral-500 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ $message->created_at->format('H:i') }}</span>
                                    @if($message->sender_id === auth()->id())
                                        <span class="ml-2">
                                            @if($message->read_at)
                                                <i class="fas fa-check-double text-primary-400"></i>
                                            @else
                                                <i class="fas fa-check text-neutral-400"></i>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="text-neutral-300 text-4xl mb-2">
                                <i class="fas fa-comments"></i>
                            </div>
                            <p class="text-neutral-500">Belum ada pesan. Mulai percakapan!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Message Input -->
        <div class="bg-white shadow-sm rounded-b-xl border-t border-neutral-200 p-6">
            <form @submit.prevent="sendMessage" class="flex space-x-3">
                <div class="flex-1">
                    <textarea x-model="newMessage" 
                              x-ref="messageInput"
                              @keydown.enter.prevent="handleEnterKey($event)"
                              @input="handleTyping"
                              placeholder="Ketik pesan Anda..."
                              rows="1"
                              class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition resize-none"
                              :disabled="isSending"></textarea>
                </div>
                <button type="submit" 
                        :disabled="!newMessage.trim() || isSending"
                        :class="{ 'opacity-50 cursor-not-allowed': !newMessage.trim() || isSending }"
                        class="bg-primary-600 hover:bg-primary-700 disabled:hover:bg-primary-600 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
                    <span x-show="!isSending">
                        <i class="fas fa-paper-plane"></i>
                    </span>
                    <span x-show="isSending">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </form>
        </div>
    </div>
    <script>
        function chatRoom(conversationId) {
            return {
                conversationId: conversationId,
                newMessage: '',
                isSending: false,
                isLoading: false,
                isLoadingOlder: false,
                hasOlderMessages: true,
                isOnline: false,
                currentUserId: {{ auth()->id() }},
                init() {
                    this.scrollToBottom();
                    this.focusInput();
                    this.setupEcho();
                    this.autoResizeTextarea();
                    this.markAsRead();
                    this.checkOlderMessages();
                },
                setupEcho() {
                    window.Echo.channel(`conversation.${this.conversationId}`)
                        .listen('.message.sent', (e) => {
                            if (e.sender_id !== this.currentUserId) {
                                this.addMessageToChat({
                                    id: e.id,
                                    content: e.content,
                                    sender_id: e.sender_id,
                                    sender_name: e.sender_name,
                                    created_at: new Date(e.created_at).toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })
                                }, false);
                                this.scrollToBottom();
                                this.markAsRead();
                                this.playNotificationSound();
                            }
                        });
                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        this.isOnline = true;
                    });
                    window.Echo.connector.pusher.connection.bind('disconnected', () => {
                        this.isOnline = false;
                    });
                    window.Echo.connector.pusher.connection.bind('error', (err) => {
                        this.isOnline = false;
                        console.error('Pusher connection error:', err);
                    });
                },
                async sendMessage() {
                    if (!this.newMessage.trim() || this.isSending) return;
                    this.isSending = true;
                    const message = this.newMessage.trim();
                    this.newMessage = '';
                    try {
                        const response = await fetch(`/chat/${this.conversationId}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                content: message
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.addMessageToChat(data.message, true);
                            this.scrollToBottom();
                        } else {
                            throw new Error(data.message || 'Gagal mengirim pesan');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Gagal mengirim pesan. Silakan coba lagi.');
                        this.newMessage = message; 
                    } finally {
                        this.isSending = false;
                        this.focusInput();
                        this.autoResizeTextarea();
                    }
                },
                addMessageToChat(message, isOwn = false) {
                    const messagesContainer = this.$refs.messagesContainer;
                    if (messagesContainer.querySelector(`[data-message-id="${message.id}"]`)) {
                        return;
                    }
                    const messageHtml = this.createMessageHtml(message, isOwn);
                    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                },
                createMessageHtml(message, isOwn) {
                    const alignClass = isOwn ? 'justify-end' : 'justify-start';
                    const bubbleClass = isOwn 
                        ? 'bg-primary-600 text-white' 
                        : 'bg-neutral-100 text-neutral-900';
                    const infoAlignClass = isOwn ? 'justify-end' : 'justify-start';
                    return `
                        <div class="flex ${alignClass}" data-message-id="${message.id}">
                            <div class="max-w-3xl">
                                <div class="px-4 py-2 rounded-lg ${bubbleClass}">
                                    <p class="text-sm">${this.escapeHtml(message.content)}</p>
                                </div>
                                <div class="flex items-center mt-1 text-xs text-neutral-500 ${infoAlignClass}">
                                    <span>${message.created_at}</span>
                                    ${isOwn ? '<span class="ml-2"><i class="fas fa-check text-neutral-400"></i></span>' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                },
                handleTyping() {
                    this.autoResizeTextarea();
                },
                async loadOlderMessages() {
                    if (this.isLoadingOlder || !this.hasOlderMessages) return;
                    this.isLoadingOlder = true;
                    try {
                        const firstMessage = this.$refs.messagesContainer.querySelector('[data-message-id]');
                        const beforeId = firstMessage ? firstMessage.dataset.messageId : 0;
                        const response = await fetch(`/chat/${this.conversationId}/messages/older?before=${beforeId}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success && data.messages.length > 0) {
                            const container = this.$refs.messagesContainer;
                            const oldScrollHeight = container.scrollHeight;
                            data.messages.forEach(message => {
                                const messageHtml = this.createMessageHtml(message, message.sender_id == this.currentUserId);
                                const firstMessageElement = container.querySelector('[data-message-id]');
                                if (firstMessageElement) {
                                    firstMessageElement.insertAdjacentHTML('beforebegin', messageHtml);
                                }
                            });
                            const newScrollHeight = container.scrollHeight;
                            container.scrollTop = newScrollHeight - oldScrollHeight;
                            this.hasOlderMessages = data.hasMore;
                        } else {
                            this.hasOlderMessages = false;
                        }
                    } catch (error) {
                        console.error('Error loading older messages:', error);
                    } finally {
                        this.isLoadingOlder = false;
                    }
                },
                checkOlderMessages() {
                    const messageCount = this.$refs.messagesContainer.querySelectorAll('[data-message-id]').length;
                    this.hasOlderMessages = messageCount >= 50;
                },
                handleEnterKey(event) {
                    if (!event.shiftKey) {
                        this.sendMessage();
                    }
                },
                autoResizeTextarea() {
                    this.$nextTick(() => {
                        const textarea = this.$refs.messageInput;
                        textarea.style.height = 'auto';
                        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                    });
                },
                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },
                focusInput() {
                    this.$nextTick(() => {
                        this.$refs.messageInput.focus();
                    });
                },
                async markAsRead() {
                    try {
                        await fetch(`/chat/${this.conversationId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                    } catch (error) {
                        console.error('Error marking as read:', error);
                    }
                },
                handleScroll() {
                    const container = this.$refs.messagesContainer;
                    if (container.scrollTop === 0 && this.hasOlderMessages && !this.isLoadingOlder) {
                        this.loadOlderMessages();
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
                    }
                },
                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            }
        }
    </script>
</x-layouts.plain-app>