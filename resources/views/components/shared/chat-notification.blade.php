{{-- resources/views/components/shared/chat-notification.blade.php --}}
<div x-data="chatNotification()" 
     x-init="init()" 
     class="relative">
    <a href="{{ route('chat.index') }}" 
       class="relative flex items-center px-3 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200 group">
        <div class="relative">
            <i class="fas fa-comments text-xl"></i>
            <div x-show="unreadCount > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-75"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-75"
                 class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-medium shadow-lg">
                <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
            </div>
            <div x-show="hasNewMessage" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-50"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-50"
                 class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full animate-ping"></div>
        </div>
        <span class="ml-2 hidden md:inline-block">Chat</span>
    </a>

    <!-- Toast Notification -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-900" x-text="lastMessage.sender_name"></h4>
                        <button @click="hideToast()" 
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="lastMessage.content"></p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-500" x-text="lastMessage.time"></span>
                        <a :href="`/chat/${lastMessage.conversation_id}`" 
                           class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                            Balas
                        </a>
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
function chatNotification() {
    return {
        unreadCount: {{ $unreadCount ?? 0 }},
        showToast: false,
        hasNewMessage: false,
        toastProgress: 100,
        toastTimer: null,
        currentUserId: {{ auth()->id() }},
        currentUserRole: '{{ auth()->user()->role->value }}',
        lastMessage: {
            sender_name: '',
            content: '',
            time: '',
            conversation_id: null
        },

        init() {
            console.log('Chat notification initialized for user:', this.currentUserId, 'role:', this.currentUserRole);
            this.setupGlobalEcho();
            this.loadInitialUnreadCount();
        },

        setupGlobalEcho() {
            // PERBAIKAN: Setup listener berdasarkan role
            if (this.currentUserRole === 'admin') {
                // Admin mendengarkan dari channel admin-notifications untuk pesan dari seller
                window.Echo.channel('admin-notifications')
                    .listen('.message.sent', (e) => {
                        console.log('Admin received message from seller:', e);
                        this.handleNewMessage(e);
                    });

                // Admin juga mendengarkan channel user sendiri untuk pesan langsung
                window.Echo.channel(`user.${this.currentUserId}`)
                    .listen('.message.sent', (e) => {
                        console.log('Admin received direct message:', e);
                        this.handleNewMessage(e);
                    });
            } else {
                // Seller hanya mendengarkan channel user sendiri
                window.Echo.channel(`user.${this.currentUserId}`)
                    .listen('.message.sent', (e) => {
                        console.log('Seller received message:', e);
                        this.handleNewMessage(e);
                    });
            }

            // Channel untuk update unread count
            window.Echo.channel(`unread-count.${this.currentUserId}`)
                .listen('.count.updated', (e) => {
                    console.log('Unread count updated:', e);
                    this.unreadCount = e.count;
                });

            // Debug connection status
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('✅ Connected to Pusher');
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.log('❌ Disconnected from Pusher');
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('❌ Pusher connection error:', err);
            });
        },

        handleNewMessage(messageData) {
            console.log('Processing new message:', messageData);
            
            // Jangan tampilkan notifikasi untuk pesan dari user sendiri
            if (messageData.sender_id === this.currentUserId) {
                console.log('Ignoring message from self');
                return;
            }

            // Jangan tampilkan notifikasi jika user sedang di halaman chat conversation ini
            if (this.isOnChatPage(messageData.conversation_id)) {
                console.log('User is on chat page, not showing notification');
                return;
            }

            console.log('Showing notification for message:', messageData);

            // Update unread count
            this.unreadCount = messageData.total_unread || (this.unreadCount + 1);

            // Update last message info
            this.lastMessage = {
                sender_name: messageData.sender_name,
                content: this.truncateMessage(messageData.content),
                time: this.formatTime(messageData.created_at),
                conversation_id: messageData.conversation_id
            };

            // Show visual indicators
            this.showNewMessageIndicator();
            this.showToastNotification();
            this.playNotificationSound();
            this.showDesktopNotification();
        },

        showNewMessageIndicator() {
            this.hasNewMessage = true;
            setTimeout(() => {
                this.hasNewMessage = false;
            }, 3000);
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

        playNotificationSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator1 = audioContext.createOscillator();
                const gainNode1 = audioContext.createGain();

                oscillator1.connect(gainNode1);
                gainNode1.connect(audioContext.destination);

                oscillator1.frequency.value = 800;
                oscillator1.type = 'sine';
                gainNode1.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode1.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                oscillator1.start(audioContext.currentTime);
                oscillator1.stop(audioContext.currentTime + 0.2);

                setTimeout(() => {
                    const oscillator2 = audioContext.createOscillator();
                    const gainNode2 = audioContext.createGain();

                    oscillator2.connect(gainNode2);
                    gainNode2.connect(audioContext.destination);

                    oscillator2.frequency.value = 1000;
                    oscillator2.type = 'sine';
                    gainNode2.gain.setValueAtTime(0.08, audioContext.currentTime);
                    gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                    oscillator2.start(audioContext.currentTime);
                    oscillator2.stop(audioContext.currentTime + 0.2);
                }, 100);
            } catch (error) {
                console.log('Cannot play notification sound:', error);
            }
        },

        showDesktopNotification() {
            if (Notification.permission === 'granted') {
                const notification = new Notification(`Pesan baru dari ${this.lastMessage.sender_name}`, {
                    body: this.lastMessage.content,
                    icon: '/favicon.ico', 
                    badge: '/favicon.ico',
                    tag: `chat-${this.lastMessage.conversation_id}`,
                    requireInteraction: false,
                    silent: false
                });

                notification.onclick = () => {
                    window.focus();
                    window.location.href = `/chat/${this.lastMessage.conversation_id}`;
                    notification.close();
                };

                setTimeout(() => {
                    notification.close();
                }, 5000);
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        },

        async loadInitialUnreadCount() {
            try {
                const response = await fetch('/chat/unread-count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.unreadCount = data.count || 0;
                    console.log('Initial unread count loaded:', this.unreadCount);
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },

        isOnChatPage(conversationId) {
            const currentPath = window.location.pathname;
            return currentPath.includes('/chat/') && currentPath.includes(conversationId);
        },

        truncateMessage(message, length = 50) {
            if (message.length <= length) return message;
            return message.substring(0, length) + '...';
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
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>