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
            
            {{-- @if($unreadCount > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>
                        <span class="text-sm font-medium text-blue-900">
                            Anda memiliki {{ $unreadCount }} pesan yang belum dibaca
                        </span>
                    </div>
                </div>
            @endif --}}
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
            @if($conversations->count() > 0)
                <div class="divide-y divide-neutral-200">
                    @foreach($conversations as $conversation)
                        @php
                            $otherParticipant = $conversation->getOtherParticipant(auth()->user());
                            $unreadCount = $conversation->unreadMessagesCount(auth()->id());
                            $latestMessage = $conversation->latestMessage;
                        @endphp
                        
                        <div class="p-6 hover:bg-neutral-50 transition cursor-pointer"
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
                                            @if($conversation->last_message_at)
                                                <span class="text-xs text-neutral-500">
                                                    {{ $conversation->last_message_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($latestMessage)
                                            <p class="text-sm text-neutral-600 truncate">
                                                @if($latestMessage->sender_id === auth()->id())
                                                    <span class="text-neutral-500">Anda: </span>
                                                @endif
                                                {{ Str::limit($latestMessage->content, 60) }}
                                            </p>
                                        @else
                                            <p class="text-sm text-neutral-400">Belum ada pesan</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Unread Badge -->
                                @if($unreadCount > 0)
                                    <div class="flex-shrink-0 ml-4">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-medium bg-primary-600 text-white min-w-[20px]">
                                            {{ $unreadCount }}
                                        </span>
                                    </div>
                                @endif
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

    <script>
        function chatManager() {
            return {
                init() {
                    // Auto refresh unread count setiap 30 detik
                    setInterval(() => {
                        this.refreshUnreadCount();
                    }, 30000);
                },
                
                async refreshUnreadCount() {
                    try {
                        // Implementasi refresh unread count jika diperlukan
                        // window.location.reload();
                    } catch (error) {
                        console.error('Gagal refresh unread count:', error);
                    }
                }
            }
        }
    </script>
</x-layouts.plain-app>