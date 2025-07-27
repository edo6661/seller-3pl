<x-layouts.plain-app>
    <x-slot name="title">Chat dengan {{ $otherParticipant->name }}</x-slot>
    <div class="container mx-auto px-4 py-8 max-w-4xl" x-data="chatRoom({{ $conversation->id }})">
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
                    <span x-show="isOnline" class="flex items-center text-sm text-success-600">
                        <span class="w-2 h-2 bg-success-600 rounded-full mr-2"></span>
                        Online
                    </span>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-sm" style="height: 500px;">
            <div x-ref="messagesContainer" 
                 class="h-full overflow-y-auto p-6 space-y-4"
                 @scroll="handleScroll">
                <!-- Load more button (akan muncul di atas jika ada pesan lama) -->
                <div x-show="hasOlderMessages && !isLoadingOlder" class="text-center py-2">
                    <button @click="loadOlderMessages" 
                            class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Muat pesan sebelumnya
                    </button>
                </div>
                <div x-show="isLoadingOlder" class="text-center py-2">
                    <i class="fas fa-spinner fa-spin text-neutral-400"></i>
                </div>

                @if($messages->count() > 0)
                    <!-- Pesan akan ditampilkan dalam urutan chronological (oldest first) -->
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
                
                <!-- Loading indicator untuk pesan baru -->
                <div x-show="isLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin text-neutral-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-sm rounded-b-xl border-t border-neutral-200 p-6">
            <form @submit.prevent="sendMessage" class="flex space-x-3">
                <div class="flex-1">
                    <textarea x-model="newMessage" 
                              x-ref="messageInput"
                              @keydown.enter.prevent="handleEnterKey($event)"
                              placeholder="Ketik pesan Anda..."
                              rows="1"
                              class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition resize-none"
                              :disabled="isSending"></textarea>
                </div>
                <button type="submit" 
                        :disabled="!newMessage.trim() || isSending"
                        :class="{ 'opacity-50 cursor-not-allowed': !newMessage.trim() || isSending }"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
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
                isOnline: true,
                
                init() {
                    this.scrollToBottom();
                    this.focusInput();
                    
                    // Auto resize textarea
                    this.$refs.messageInput.addEventListener('input', () => {
                        this.autoResizeTextarea();
                    });
                    
                    // Mark messages as read when page loads
                    this.markAsRead();
                    
                    // Check if there are older messages
                    this.checkOlderMessages();
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
                        this.newMessage = message; // Restore message
                    } finally {
                        this.isSending = false;
                        this.focusInput();
                    }
                },
                
                addMessageToChat(message, isOwn = false) {
                    const messagesContainer = this.$refs.messagesContainer;
                    const messageHtml = this.createMessageHtml(message, isOwn);
                    // Tambahkan pesan baru di bagian bawah (setelah pesan terakhir)
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
                
                async loadOlderMessages() {
                    if (this.isLoadingOlder || !this.hasOlderMessages) return;
                    
                    this.isLoadingOlder = true;
                    
                    try {
                        // Ambil ID pesan pertama yang ada
                        const firstMessage = this.$refs.messagesContainer.querySelector('[data-message-id]');
                        const beforeId = firstMessage ? firstMessage.dataset.messageId : 0;
                        
                        const response = await fetch(`/chat/${this.conversationId}/messages/older?before=${beforeId}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success && data.messages.length > 0) {
                            // Simpan posisi scroll saat ini
                            const container = this.$refs.messagesContainer;
                            const oldScrollHeight = container.scrollHeight;
                            
                            // Tambahkan pesan lama di bagian atas
                            data.messages.forEach(message => {
                                const messageHtml = this.createMessageHtml(message, message.sender_id == {{ auth()->id() }});
                                const firstMessageElement = container.querySelector('[data-message-id]');
                                if (firstMessageElement) {
                                    firstMessageElement.insertAdjacentHTML('beforebegin', messageHtml);
                                }
                            });
                            
                            // Maintain scroll position
                            const newScrollHeight = container.scrollHeight;
                            container.scrollTop = newScrollHeight - oldScrollHeight;
                            
                            // Check if there are more messages
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
                    // Set hasOlderMessages berdasarkan jumlah pesan yang dimuat
                    const messageCount = this.$refs.messagesContainer.querySelectorAll('[data-message-id]').length;
                    this.hasOlderMessages = messageCount >= 50; // Sesuaikan dengan limit di service
                },
                
                handleEnterKey(event) {
                    if (!event.shiftKey) {
                        this.sendMessage();
                    }
                },
                
                autoResizeTextarea() {
                    const textarea = this.$refs.messageInput;
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                },
                
                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer;
                        container.scrollTop = container.scrollHeight;
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
                    // Auto load older messages when scrolled to top
                    const container = this.$refs.messagesContainer;
                    if (container.scrollTop === 0 && this.hasOlderMessages && !this.isLoadingOlder) {
                        this.loadOlderMessages();
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