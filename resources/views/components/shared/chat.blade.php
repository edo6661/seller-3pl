<div x-data="chatWidget()" 
     x-init="init()" 
     class="fixed bottom-4 right-4 z-50"
     style="font-family: system-ui, -apple-system, sans-serif;">
    <div x-show="!isExpanded" 
         @click="toggleChat()"
         x-cloak
         class="relative bg-primary-600 hover:bg-primary-700 text-white rounded-full w-14 h-14 flex items-center justify-center cursor-pointer shadow-lg hover:shadow-xl transition-all duration-300 group">
        <i class="fas fa-comments text-xl"></i>
        <div x-show="totalUnreadCount > 0" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-75"
             x-transition:enter-end="opacity-100 scale-100"
             class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[20px] h-5 flex items-center justify-center font-medium shadow-md">
            <span x-text="totalUnreadCount > 99 ? '99+' : totalUnreadCount"></span>
        </div>
        <div x-show="hasNewMessage" 
             class="absolute inset-0 bg-primary-400 rounded-full animate-ping"></div>
    </div>
    <div x-show="isExpanded" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="bg-white rounded-lg shadow-2xl border border-gray-200 w-80 h-[500px] flex flex-col">
        <div class="bg-primary-600 text-white p-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-comments mr-2"></i>
                <span class="font-medium" x-text="currentView === 'conversations' ? 'Chat' : (activeConversation ? activeConversation.other_participant_name : 'Chat')"></span>
            </div>
            <div class="flex items-center space-x-2">
                <button x-show="currentView === 'conversation'" 
                        @click="backToConversations()"
                        class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <button @click="toggleChat()" 
                        class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-hidden">
            <div x-cloak x-show="currentView === 'conversations'" class="h-full flex flex-col">
                <div class="p-3 border-b border-gray-200">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input x-model="searchQuery" 
                               @input="searchConversations()"
                               type="text" 
                               placeholder="Cari percakapan..."
                               class="w-full pl-8 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
                <template x-if="currentUserRole === 'seller'">
                    <div class="p-3 border-b border-gray-200">
                        <button @click="startChatWithAdmin()"
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Chat dengan Admin
                        </button>
                    </div>
                </template>
                <div class="flex-1 overflow-y-auto">
                    <template x-if="isLoadingConversations">
                        <div class="flex items-center justify-center h-32">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            <span class="ml-2 text-sm text-gray-500">Memuat...</span>
                        </div>
                    </template>
                    <template x-if="!isLoadingConversations && conversations.length === 0">
                        <div class="flex flex-col items-center justify-center h-32 text-center p-4">
                            <i class="fas fa-comments text-gray-300 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-500">Belum ada percakapan</p>
                        </div>
                    </template>
                    <template x-for="conversation in filteredConversations" :key="conversation.id">
                        <div @click="openConversation(conversation)"
                             class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="flex-shrink-0 w-8 h-8 mr-3">
                                        <template x-if="conversation.other_participant_avatar">
                                            <img :src="conversation.other_participant_avatar" 
                                                 :alt="conversation.other_participant_name"
                                                 class="w-8 h-8 rounded-full object-cover">
                                        </template>
                                        <template x-if="!conversation.other_participant_avatar">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                <i class="fas fa-user text-primary-600 text-sm"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate" x-text="conversation.other_participant_name"></h4>
                                        <p class="text-xs text-gray-500" x-text="conversation.other_participant_role"></p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_message_at)"></span>
                                    <template x-if="conversation.unread_count > 0">
                                        <span class="bg-primary-600 text-white text-xs rounded-full px-2 py-0.5 mt-1 min-w-[18px] text-center" 
                                              x-text="conversation.unread_count"></span>
                                    </template>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 truncate pl-11" x-text="conversation.last_message_preview"></p>
                        </div>
                    </template>
                </div>
            </div>
            <div x-cloak x-show="currentView === 'conversation'" class="h-full flex flex-col">
                <div x-ref="messagesContainer" 
                     @scroll="handleMessagesScroll()"
                     class="flex-1 overflow-y-auto p-3 space-y-3 bg-gray-50">
                    <template x-if="hasOlderMessages && !isLoadingOlder">
                        <div class="text-center">
                            <button @click="loadOlderMessages()" 
                                    class="text-primary-600 hover:text-primary-700 text-sm bg-white px-3 py-1 rounded-full shadow-sm">
                                <i class="fas fa-chevron-up mr-1"></i>
                                Muat pesan sebelumnya
                            </button>
                        </div>
                    </template>
                    <template x-if="isLoadingOlder">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </div>
                    </template>
                    <template x-for="message in messages" :key="message.id">
                        <div :class="message.sender_id === currentUserId ? 'flex justify-end' : 'flex justify-start'">
                            <div class="max-w-[70%]">
                                <div :class="message.sender_id === currentUserId ? 
                                    'bg-primary-600 text-white rounded-lg rounded-br-sm' : 
                                    'bg-white text-gray-900 rounded-lg rounded-bl-sm shadow-sm'"
                                     class="px-3 py-2 text-sm">
                                    <p x-text="message.content"></p>
                                </div>
                                <div :class="message.sender_id === currentUserId ? 'text-right' : 'text-left'" 
                                     class="text-xs text-gray-500 mt-1 px-1">
                                    <span x-text="formatTime(message.created_at)"></span>
                                    <template x-if="message.sender_id === currentUserId">
                                        <span class="ml-1">
                                            <i :class="message.read_at ? 'fas fa-check-double text-primary-400' : 'fas fa-check text-gray-400'"></i>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="messages.length === 0 && !isLoadingMessages">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <i class="fas fa-comments text-gray-300 text-3xl mb-2"></i>
                                <p class="text-sm text-gray-500">Belum ada pesan</p>
                                <p class="text-sm text-gray-400">Mulai percakapan!</p>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="p-3 border-t border-gray-200 bg-white">
                    <form @submit.prevent="sendMessage()" class="flex space-x-2">
                        <textarea x-model="newMessage" 
                                  x-ref="messageInput"
                                  @keydown.enter.prevent="handleEnterKey($event)"
                                  @input="autoResizeTextarea()"
                                  placeholder="Ketik pesan..."
                                  rows="1"
                                  class="flex-1 resize-none border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                  :disabled="isSendingMessage"></textarea>
                        <button type="submit" 
                                :disabled="!newMessage.trim() || isSendingMessage"
                                :class="{ 'opacity-50 cursor-not-allowed': !newMessage.trim() || isSendingMessage }"
                                class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 rounded-lg transition flex items-center justify-center">
                            <template x-if="!isSendingMessage">
                                <i class="fas fa-paper-plane text-sm"></i>
                            </template>
                            <template x-if="isSendingMessage">
                                <i class="fas fa-spinner fa-spin text-sm"></i>
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div 
         x-cloak
         x-show="showToast && !isExpanded" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="absolute bottom-16 right-0 w-72 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
        <div class="p-3">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-8 h-8 mr-3">
                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-user text-primary-600 text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900" x-text="lastMessage.sender_name"></h4>
                        <button @click="hideToast()" 
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-1" x-text="lastMessage.content"></p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-500" x-text="lastMessage.time"></span>
                        <button @click="openConversationFromToast()" 
                                class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                            Balas
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gray-100">
            <div class="h-full bg-primary-500 transition-all duration-[3000ms] ease-linear"
                 :style="`width: ${toastProgress}%`"></div>
        </div>
    </div>
</div>
<script>
function chatWidget() {
    return {
        // Widget state
        isExpanded: false,
        currentView: 'conversations', // 'conversations' or 'conversation'
        // User data
        currentUserId: {{ auth()->id() }},
        currentUserRole: '{{ auth()->user()->role->value }}',
        // Conversations data
        conversations: [],
        filteredConversations: [],
        searchQuery: '',
        isLoadingConversations: false,
        totalUnreadCount: 0,
        // Active conversation
        activeConversation: null,
        messages: [],
        newMessage: '',
        isSendingMessage: false,
        isLoadingMessages: false,
        hasOlderMessages: false,
        isLoadingOlder: false,
        // Notifications
        showToast: false,
        hasNewMessage: false,
        toastProgress: 100,
        toastTimer: null,
        lastMessage: {
            sender_name: '',
            content: '',
            time: '',
            conversation_id: null
        },
        init() {
            this.setupGlobalEcho();
            this.loadInitialData();
        },
        async loadInitialData() {
            await this.loadUnreadCount();
            if (this.isExpanded) {
                await this.loadConversations();
            }
        },
        async toggleChat() {
            this.isExpanded = !this.isExpanded;
            if (this.isExpanded) {
                await this.loadConversations();
                this.hideToast();
            } else {
                this.currentView = 'conversations';
                this.activeConversation = null;
                this.messages = [];
            }
        },
        async loadConversations() {
            this.isLoadingConversations = true;
            try {
                const response = await fetch('/chat/conversations-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.conversations = data.conversations;
                    this.filteredConversations = this.conversations;
                    this.totalUnreadCount = data.total_unread;
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            } finally {
                this.isLoadingConversations = false;
            }
        },
        searchConversations() {
            if (!this.searchQuery.trim()) {
                this.filteredConversations = this.conversations;
                return;
            }
            const query = this.searchQuery.toLowerCase();
            this.filteredConversations = this.conversations.filter(conv => 
                conv.other_participant_name.toLowerCase().includes(query) ||
                conv.last_message_preview.toLowerCase().includes(query)
            );
        },
        async openConversation(conversation) {
            this.activeConversation = conversation;
            this.currentView = 'conversation';
            this.messages = [];
            this.hasOlderMessages = false;
            await this.loadMessages(conversation.id);
            await this.markConversationAsRead(conversation.id);
            this.$nextTick(() => {
                this.scrollToBottom();
                this.focusMessageInput();
            });
        },
        async loadMessages(conversationId) {
            this.isLoadingMessages = true;
            try {
                const response = await fetch(`/chat/${conversationId}/messages-data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.messages = data.messages;
                    this.hasOlderMessages = data.has_older;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.isLoadingMessages = false;
            }
        },
        async loadOlderMessages() {
            if (this.isLoadingOlder || !this.hasOlderMessages || !this.activeConversation) return;
            this.isLoadingOlder = true;
            try {
                const firstMessage = this.messages[0];
                const beforeId = firstMessage ? firstMessage.id : 0;
                const response = await fetch(`/chat/${this.activeConversation.id}/messages/older?before=${beforeId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.messages.length > 0) {
                        // Simpan posisi scroll
                        const container = this.$refs.messagesContainer;
                        const oldScrollHeight = container.scrollHeight;
                        // Tambahkan pesan lama ke awal array
                        this.messages = [...data.messages, ...this.messages];
                        // Restore posisi scroll
                        this.$nextTick(() => {
                            const newScrollHeight = container.scrollHeight;
                            container.scrollTop = newScrollHeight - oldScrollHeight;
                        });
                        this.hasOlderMessages = data.hasMore;
                    } else {
                        this.hasOlderMessages = false;
                    }
                }
            } catch (error) {
                console.error('Error loading older messages:', error);
            } finally {
                this.isLoadingOlder = false;
            }
        },
        async sendMessage() {
            if (!this.newMessage.trim() || this.isSendingMessage || !this.activeConversation) return;
            this.isSendingMessage = true;
            const message = this.newMessage.trim();
            this.newMessage = '';
            try {
                const response = await fetch(`/chat/${this.activeConversation.id}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: message })
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.messages.push(data.message);
                        this.scrollToBottom();
                        // Update conversation preview
                        const conv = this.conversations.find(c => c.id === this.activeConversation.id);
                        if (conv) {
                            conv.last_message_preview = `Anda: ${this.truncateMessage(message, 30)}`;
                            conv.last_message_at = new Date().toISOString();
                        }
                    }
                } else {
                    throw new Error('Failed to send message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.newMessage = message;
                alert('Gagal mengirim pesan. Silakan coba lagi.');
            } finally {
                this.isSendingMessage = false;
                this.focusMessageInput();
            }
        },
        async startChatWithAdmin() {
            try {
                const response = await fetch('/chat/start-json', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.conversation) {
                        await this.loadConversations();
                        const conversation = this.conversations.find(c => c.id === data.conversation.id);
                        if (conversation) {
                            await this.openConversation(conversation);
                        }
                    }
                }
            } catch (error) {
                console.error('Error starting chat with admin:', error);
            }
        },
        async markConversationAsRead(conversationId) {
            try {
                await fetch(`/chat/${conversationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                // Update unread count
                const conv = this.conversations.find(c => c.id === conversationId);
                if (conv && conv.unread_count > 0) {
                    this.totalUnreadCount -= conv.unread_count;
                    conv.unread_count = 0;
                }
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },
        async loadUnreadCount() {
            try {
                const response = await fetch('/chat/unread-count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.totalUnreadCount = data.count || 0;
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },
        backToConversations() {
            this.currentView = 'conversations';
            this.activeConversation = null;
            this.messages = [];
        },
        // Event handlers
        handleEnterKey(event) {
            if (!event.shiftKey) {
                this.sendMessage();
            }
        },
        handleMessagesScroll() {
            const container = this.$refs.messagesContainer;
            if (container.scrollTop === 0 && this.hasOlderMessages && !this.isLoadingOlder) {
                this.loadOlderMessages();
            }
        },
        autoResizeTextarea() {
            this.$nextTick(() => {
                const textarea = this.$refs.messageInput;
                if (textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
                }
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
        focusMessageInput() {
            this.$nextTick(() => {
                const input = this.$refs.messageInput;
                if (input) {
                    input.focus();
                }
            });
        },
        // Real-time functionality
        setupGlobalEcho() {
            if (this.currentUserRole === 'admin') {
                window.Echo.channel('admin-notifications')
                    .listen('.message.sent', (e) => {
                        this.handleNewMessage(e);
                    });
                window.Echo.channel(`user.${this.currentUserId}`)
                    .listen('.message.sent', (e) => {
                        this.handleNewMessage(e);
                    });
            } else {
                window.Echo.channel(`user.${this.currentUserId}`)
                    .listen('.message.sent', (e) => {
                        this.handleNewMessage(e);
                    });
            }
            window.Echo.channel(`unread-count.${this.currentUserId}`)
                .listen('.count.updated', (e) => {
                    this.totalUnreadCount = e.count;
                });
        },
        handleNewMessage(messageData) {
            if (messageData.sender_id === this.currentUserId) {
                return;
            }
            // Update conversations list
            this.updateConversationWithNewMessage(messageData);
            // Jika sedang di conversation yang sama, tambahkan pesan
            if (this.activeConversation && this.activeConversation.id === messageData.conversation_id) {
                this.messages.push({
                    id: messageData.id,
                    content: messageData.content,
                    sender_id: messageData.sender_id,
                    sender_name: messageData.sender_name,
                    created_at: messageData.created_at,
                    read_at: null
                });
                this.scrollToBottom();
                this.markConversationAsRead(messageData.conversation_id);
            } else {
                // Update unread count
                this.totalUnreadCount += 1;
                // Show notification
                this.showNewMessageNotification(messageData);
            }
            this.playNotificationSound();
        },
        updateConversationWithNewMessage(messageData) {
            const conv = this.conversations.find(c => c.id === messageData.conversation_id);
            if (conv) {
                conv.last_message_preview = this.truncateMessage(messageData.content, 40);
                conv.last_message_at = messageData.created_at;
                if (this.activeConversation?.id !== messageData.conversation_id) {
                    conv.unread_count = (conv.unread_count || 0) + 1;
                }
                // Pindahkan ke atas
                this.conversations = this.conversations.filter(c => c.id !== conv.id);
                this.conversations.unshift(conv);
                this.searchConversations();
            } else {
                // Reload conversations jika conversation baru
                this.loadConversations();
            }
        },
        showNewMessageNotification(messageData) {
            this.lastMessage = {
                sender_name: messageData.sender_name,
                content: this.truncateMessage(messageData.content),
                time: this.formatTime(messageData.created_at),
                conversation_id: messageData.conversation_id
            };
            this.hasNewMessage = true;
            setTimeout(() => {
                this.hasNewMessage = false;
            }, 3000);
            if (!this.isExpanded) {
                this.showToastNotification();
            }
        },
        showToastNotification() {
            this.toastProgress = 100;
            this.showToast = true;
            if (this.toastTimer) {
                clearInterval(this.toastTimer);
            }
            let duration = 5000;
            let interval = 50;
            let step = (100 / (duration / interval));
            this.toastTimer = setInterval(() => {
                this.toastProgress -= step;
                if (this.toastProgress <= 0) {
                    this.hideToast();
                }
            }, interval);
        },
        hideToast() {
            this.showToast = false;
            if (this.toastTimer) {
                clearInterval(this.toastTimer);
                this.toastTimer = null;
            }
        },
        async openConversationFromToast() {
            if (!this.lastMessage.conversation_id) return;
            this.hideToast();
            this.isExpanded = true;
            await this.loadConversations();
            const conversation = this.conversations.find(c => c.id === this.lastMessage.conversation_id);
            if (conversation) {
                await this.openConversation(conversation);
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
        // Utility functions
        formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            if (diff < 60000) {
                return 'Baru saja';
            }
            if (diff < 3600000) {
                const minutes = Math.floor(diff / 60000);
                return `${minutes}m`;
            }
            if (date.toDateString() === now.toDateString()) {
                return date.toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            }
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            if (date.toDateString() === yesterday.toDateString()) {
                return 'Kemarin';
            }
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short'
            });
        },
        truncateMessage(message, length = 50) {
            if (!message || message.length <= length) return message;
            return message.substring(0, length) + '...';
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }
}
</script>