@props([
    'unreadCount' => 0
])
<div class="relative" x-data="notificationManager()" x-init="init()">
    <button @click="toggleNotifications()" 
            class="relative p-2 text-neutral-600 hover:text-primary-600 transition-colors duration-200 rounded-lg hover:bg-neutral-100"
            :class="{ 'text-primary-600 bg-primary-50': showNotifications }">
        <i class="fas fa-bell text-lg"></i>
        <span x-show="totalUnread > 0" 
              x-text="totalUnread > 99 ? '99+' : totalUnread"
              x-transition:enter="transform ease-out duration-200"
              x-transition:enter-start="scale-0 opacity-0"
              x-transition:enter-end="scale-100 opacity-100"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center min-w-[20px]">
        </span>
        <span x-show="hasNewNotification" 
              x-transition:enter="transform ease-out duration-200"
              x-transition:enter-start="scale-0 opacity-0"
              x-transition:enter-end="scale-100 opacity-100"
              class="absolute -top-1 -right-1 bg-red-500 rounded-full h-3 w-3 animate-ping">
        </span>
    </button>
    <!-- ... rest of the template remains the same ... -->
    <div x-show="showNotifications" 
         x-cloak 
         @click.away="showNotifications = false"
         x-transition:enter="transform ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transform ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-neutral-200 z-50 max-h-96 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-neutral-200 bg-neutral-50">
            <h3 class="text-lg font-semibold text-neutral-900">Notifikasi</h3>
            <div class="flex items-center space-x-2">
                <button @click="markAllAsRead()" 
                        x-show="totalUnread > 0"
                        :disabled="isLoading"
                        class="text-sm text-primary-600 hover:text-primary-800 font-medium disabled:opacity-50">
                    <span x-show="!isLoading">Tandai Semua Dibaca</span>
                    <span x-show="isLoading">Loading...</span>
                </button>
                <button @click="clearAllNotifications()" 
                        :disabled="isLoading"
                        class="text-sm text-neutral-500 hover:text-neutral-700 disabled:opacity-50">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </div>
        </div>
        <!-- Loading state -->
        <div x-show="isLoading && notifications.length === 0" class="p-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
            <p class="text-neutral-500 text-sm mt-2">Memuat notifikasi...</p>
        </div>
        <!-- Error state -->
        <div x-show="error && !isLoading" class="p-8 text-center">
            <div class="text-red-400 text-4xl mb-3">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p class="text-red-500 text-sm mb-3" x-text="error"></p>
            <button @click="loadNotifications()" 
                    class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                Coba Lagi
            </button>
        </div>
        <!-- Notifications list -->
        <div class="max-h-80 overflow-y-auto" x-show="!isLoading && !error">
            <template x-for="notification in notifications" :key="notification.id">
                <div class="p-4 border-b border-neutral-100 hover:bg-neutral-50 transition-colors cursor-pointer"
                     :class="{ 'bg-blue-50': !notification.read_at }"
                     @click="handleNotificationClick(notification)">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                 :class="getNotificationColor(notification.type)">
                                <i :class="getNotificationIcon(notification.type)" class="text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-900" x-text="notification.title"></p>
                            <p class="text-sm text-neutral-600 mt-1" x-text="notification.message"></p>
                            <p class="text-xs text-neutral-400 mt-2" x-text="formatTime(notification.created_at)"></p>
                        </div>
                        <div class="flex-shrink-0" x-show="!notification.read_at">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Empty state -->
            <div x-show="notifications.length === 0 && !isLoading && !error" class="p-8 text-center">
                <div class="text-neutral-300 text-4xl mb-3">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <p class="text-neutral-500 text-sm">Tidak ada notifikasi</p>
            </div>
        </div>
        <!-- Footer -->
        <div class="p-3 border-t border-neutral-200 bg-neutral-50" x-show="!isLoading && !error">
            <a href="#" class="block text-center text-sm text-primary-600 hover:text-primary-800 font-medium">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>
<script>
function notificationManager() {
    return {
        showNotifications: false,
        notifications: [],
        totalUnread: {{ $unreadCount }},
        hasNewNotification: false,
        isLoading: false,
        error: null,
        currentUserId: {{ auth()->id() ?? 'null' }},
        currentUserRole: '{{ auth()->user()->role->value ?? "guest" }}',
        authToken: null,
        processedNotifications: new Set(), 
        init() {
            console.log('Notification manager initialized');
            this.authToken = this.getAuthToken();
            if (!this.authToken) {
                console.error('No auth token found');
                this.error = 'Token autentikasi tidak ditemukan';
                return;
            }
            this.loadNotifications();
            if (this.currentUserId && window.Echo) {
                this.setupRealtimeListeners();
            }
        },
        getAuthToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        },
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        },
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
            this.hasNewNotification = false;
            if (this.showNotifications && !this.isLoading) {
                this.loadNotifications();
            }
        },
        setupRealtimeListeners() {
            try {
                if (this.currentUserRole === 'admin') {
                    window.Echo.channel('admin-notifications')
                        .listen('.pickup.created', (e) => {
                            console.log('Admin received pickup created notification:', e);
                            this.handleNewNotification(e);
                        })
                        .listen('.pickup.status.updated', (e) => {
                            console.log('Admin received pickup status updated notification:', e);
                            this.handleNewNotification(e);
                        })
                        .listen('.chat.notification', (e) => {
                            console.log('Admin received chat notification:', e);
                            this.handleNewNotification(e);
                        });
                }
                window.Echo.channel(`user.${this.currentUserId}`)
                    .listen('.pickup.created', (e) => {
                        console.log('User received pickup created notification:', e);
                        this.handleNewNotification(e);
                    })
                    .listen('.pickup.status.updated', (e) => {
                        console.log('User received pickup status updated notification:', e);
                        this.handleNewNotification(e);
                    })
                    .listen('.chat.notification', (e) => {
                        console.log('User received chat notification:', e);
                        this.handleNewNotification(e);
                    });
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('✅ Connected to Pusher - Notifications');
                });
            } catch (error) {
                console.error('Error setting up realtime listeners:', error);
            }
        },
        handleNewNotification(eventData) {
            console.log('Processing new notification:', eventData);
            if (!eventData.notification) {
                console.warn('No notification data in event:', eventData);
                return;
            }
            const notificationKey = `${eventData.notification.type}-${eventData.message_id || eventData.notification.created_at || Date.now()}`;
            if (this.processedNotifications.has(notificationKey)) {
                console.log('Notification already processed:', notificationKey);
                return;
            }
            this.processedNotifications.add(notificationKey);
            const notification = {
                id: eventData.notification.id || Date.now(),
                type: eventData.notification.type,
                title: eventData.notification.title,
                message: eventData.notification.message,
                created_at: eventData.notification.created_at || new Date().toISOString(),
                read_at: null,
                data: eventData
            };
            const existingIndex = this.notifications.findIndex(n => n.id === notification.id);
            if (existingIndex === -1) {
                if (this.notifications.length >= 50) {
                    this.notifications.pop(); 
                }
                this.notifications.unshift(notification);
                this.totalUnread++;
                this.hasNewNotification = true;
                this.playNotificationSound();
                this.showToastNotification(notification);
                console.log('New notification added:', notification);
            } else {
                console.log('Notification already exists:', notification.id);
            }
        },
        showToastNotification(notification) {
            if (window.showHeaderToast) {
                window.showHeaderToast('info', notification.title + ': ' + notification.message);
            }
        },
        async loadNotifications() {
            if (!this.authToken) {
                this.error = 'Token autentikasi tidak ditemukan';
                return;
            }
            this.isLoading = true;
            this.error = null;
            try {
                const response = await fetch('/notifications', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.authToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('Sesi telah berakhir. Silakan login kembali.');
                    } else if (response.status === 403) {
                        throw new Error('Akses ditolak.');
                    } else {
                        throw new Error(`Error ${response.status}: ${response.statusText}`);
                    }
                }
                const data = await response.json();
                if (data.success) {
                    this.notifications = data.notifications || [];
                    this.totalUnread = data.unread_count || 0;
                    console.log('Notifications loaded from server:', {
                        count: this.notifications.length,
                        unread_count: this.totalUnread
                    });
                } else {
                    throw new Error(data.message || 'Gagal memuat notifikasi');
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
                this.error = error.message;
            } finally {
                this.isLoading = false;
            }
        },
        async markAsRead(notificationId) {
            if (!this.authToken) return;
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.authToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                if (response.ok) {
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.read_at) {
                        notification.read_at = new Date().toISOString();
                        this.totalUnread = Math.max(0, this.totalUnread - 1);
                    }
                } else {
                    console.error('Failed to mark notification as read:', response.statusText);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        async markAllAsRead() {
            if (!this.authToken || this.isLoading) return;
            this.isLoading = true;
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.authToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                if (response.ok) {
                    this.notifications.forEach(notification => {
                        if (!notification.read_at) {
                            notification.read_at = new Date().toISOString();
                        }
                    });
                    this.totalUnread = 0;
                } else {
                    console.error('Failed to mark all notifications as read:', response.statusText);
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            } finally {
                this.isLoading = false;
            }
        },
        async clearAllNotifications() {
            if (!this.authToken || this.isLoading) return;
            if (!confirm('Hapus semua notifikasi?')) return;
            this.isLoading = true;
            try {
                const response = await fetch('/notifications/clear-all', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.authToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                if (response.ok) {
                    this.notifications = [];
                    this.totalUnread = 0;
                    this.processedNotifications.clear(); 
                } else {
                    console.error('Failed to clear notifications:', response.statusText);
                }
            } catch (error) {
                console.error('Error clearing notifications:', error);
            } finally {
                this.isLoading = false;
            }
        },
        handleNotificationClick(notification) {
            if (!notification.read_at) {
                this.markAsRead(notification.id);
            }
            if (notification.type === 'pickup_created' || notification.type === 'pickup_status_updated') {
                if (notification.data && notification.data.id) {
                    if (this.currentUserRole === 'admin') {
                        window.location.href = `/admin/pickup-requests/${notification.data.id}`;
                    } else {
                        window.location.href = `/seller/pickup-request/${notification.data.id}`;
                    }
                }
            } else if (notification.type === 'new_chat_message') {
                if (notification.data && notification.data.conversation_id) {
                    window.location.href = `/chat/${notification.data.conversation_id}`;
                }
            }
            this.showNotifications = false;
        },
        getNotificationIcon(type) {
            const icons = {
                'pickup_created': 'fas fa-truck',
                'pickup_status_updated': 'fas fa-clipboard-check',
                'new_chat_message': 'fas fa-comments',
                'default': 'fas fa-bell'
            };
            return icons[type] || icons.default;
        },
        getNotificationColor(type) {
            const colors = {
                'pickup_created': 'bg-blue-100 text-blue-600',
                'pickup_status_updated': 'bg-green-100 text-green-600',
                'new_chat_message': 'bg-purple-100 text-purple-600',
                'default': 'bg-gray-100 text-gray-600'
            };
            return colors[type] || colors.default;
        },
        formatTime(timestamp) {
            try {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = now - date;
                if (diff < 60000) {
                    return 'Baru saja';
                }
                if (diff < 3600000) {
                    const minutes = Math.floor(diff / 60000);
                    return `${minutes} menit yang lalu`;
                }
                if (date.toDateString() === now.toDateString()) {
                    return date.toLocaleTimeString('id-ID', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }
                return date.toLocaleDateString('id-ID');
            } catch (error) {
                console.error('Error formatting time:', error);
                return 'Waktu tidak valid';
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
        }
    }
}
</script>