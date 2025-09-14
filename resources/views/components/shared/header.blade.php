<div x-data="{ sidebarOpen: false }">
    <aside class="fixed left-0 top-0 z-40 w-64 h-screen bg-white shadow-lg border-r border-neutral-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" 
           x-data="{
               showNotification: false,
               notificationType: '',
               notificationMessage: '',
               showToast(type, message) {
                   this.notificationType = type;
                   this.notificationMessage = message;
                   this.showNotification = true;
                   setTimeout(() => { this.showNotification = false; }, 5000);
               }
           }"
           x-init="
               @if(session('success'))
               showToast('success', '{{ session('success') }}');
               @endif
               @if(session('status'))
               showToast('success', '{{ session('status') }}');
               @endif
               @if(session('error'))
               showToast('error', '{{ session('error') }}');
               @endif
               @if(session('warning'))
               showToast('warning', '{{ session('warning') }}');
               @endif
               
               window.showHeaderToast = (type, message) => {
                   $data.showToast(type, message);
               };
           "
           :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">

        <div class="flex items-center justify-between p-4 border-b border-neutral-200">
            <a href="{{ route('guest.home') }}" class="flex items-center space-x-3">
                <div class="rounded-xl flex items-center justify-center">
                       <img src="{{ asset('assets/logo.png') }}" alt="Pusat Kirim Logo" class=" rounded object-cover w-16">
                </div>
                
                <div>
                    <span class="text-xl font-bold text-neutral-800">Pusat Kirim</span>
                    <p class="text-xs text-neutral-500">Delivery Platform</p>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-neutral-500 hover:text-neutral-700 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex flex-col h-full">
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                @guest
                    <div class="space-y-3">
                        <h3 class="text-xs font-semibold text-neutral-400 uppercase tracking-wider px-3">Autentikasi</h3>
                        <a href="{{ route('guest.auth.login') }}" 
                            class="flex items-center px-3 py-2.5 text-neutral-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('guest.auth.login') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                            <i class="fas fa-sign-in-alt mr-3 text-sm w-5"></i>
                            <span class="font-medium">Login</span>
                        </a>
                        <a href="{{ route('guest.auth.register') }}" 
                            class="flex items-center px-3 py-2.5 bg-primary-500 text-white hover:bg-primary-600 rounded-lg transition-all duration-200 group {{ request()->routeIs('guest.auth.register') ? 'bg-primary-600' : '' }}">
                            <i class="fas fa-user-plus mr-3 text-sm w-5"></i>
                            <span class="font-medium">Register</span>
                        </a>
                        <a href="{{ route('guest.auth.redirect', ['provider' => 'google']) }}" 
                            class="flex items-center px-3 py-2.5 bg-secondary-500 text-white hover:bg-secondary-600 rounded-lg transition-all duration-200 group">
                            <i class="fab fa-google mr-3 text-sm w-5"></i>
                            <span class="font-medium">Login with Google</span>
                        </a>
                    </div>
                @endguest

                @auth
                    @if (auth()->user()->isAdmin())
                        <div class="space-y-3">
                            <h3 class="text-xs font-semibold text-neutral-400 uppercase tracking-wider px-3">Admin Panel</h3>
                            <a href="{{ route('admin.dashboard') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-tachometer-alt mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                            <a href="{{ route('admin.buyer-ratings.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.buyer-ratings.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-star mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Ratings</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-users mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Users</span>
                            </a>
                            <a href="{{ route('admin.wallets.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.wallets.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-wallet mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Wallets</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.products.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-box mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Products</span>
                            </a>
                            <a href="{{ route('admin.pickup-requests.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.pickup-requests.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-truck mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Pickup Requests</span>
                            </a>
                            <a href="{{ route('chat.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('chat.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-comments mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Chat</span>
                            </a>
                            <a href="{{ route('admin.support.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.support.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-headset mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Support</span>
                            </a>
                        </div>
                    @endif

                    @if (auth()->user()->isSeller())
                        <div class="space-y-3">
                            <h3 class="text-xs font-semibold text-neutral-400 uppercase tracking-wider px-3">Seller Panel</h3>
                            <a href="{{ route('seller.dashboard') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.dashboard') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-tachometer-alt mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                            <a href="{{ route('seller.wallet.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.wallet.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-wallet mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Wallet</span>
                            </a>
                            <a href="{{ route('seller.products.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.products.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-box mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Produk</span>
                            </a>
                            <a href="{{ route('seller.pickup-request.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.pickup-request.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-truck mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Requests</span>
                            </a>
                            <a href="{{ route('chat.start') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('chat.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-comments mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Chat</span>
                            </a>
                            <a href="{{ route('seller.addresses.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.addresses.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-map-marker-alt mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Addresses</span>
                            </a>
                            <a href="{{ route('seller.support.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.support.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-headset mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Support</span>
                            </a>
                            <a href="{{ route('seller.team.index') }}" 
                                class="flex items-center px-3 py-2.5 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 group {{ request()->routeIs('seller.team.*') ? 'bg-primary-100 text-primary-700 border-l-4 border-primary-600' : '' }}">
                                <i class="fas fa-users mr-3 text-sm w-5 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Team</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </nav>

            </div>
    </aside>

    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <div class="lg:ml-64">
        <!-- Mobile Header -->
        <header class="lg:hidden sticky top-0 bg-white shadow-md border-b border-neutral-200 z-20">
            <div class="flex items-center justify-between px-4 py-3">
                <button @click="sidebarOpen = !sidebarOpen" class="text-neutral-600 hover:text-primary-600 p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <a href="{{ route('guest.home') }}" class="flex items-center space-x-2">
                    <div class="rounded-lg flex items-center justify-center">
                        <img src="{{ asset('assets/logo.png') }}" alt="Pusat Kirim Logo" class=" rounded object-cover w-16">
                    </div>
                    <span class="text-lg font-bold text-neutral-800">Pusat Kirim</span>
                </a>
                
                @auth
                <div class="flex items-center space-x-3">
                    <!-- Mobile Notification Bell -->
                    <div>
                        @php
                            $notificationService = app(\App\Services\NotificationService::class);
                            $unreadCount = $notificationService->getUnreadCount(auth()->id());
                        @endphp
                        <x-shared.notification-bell :unreadCount="$unreadCount" />
                    </div>
                    
                    <!-- Mobile User Menu -->
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center shadow-sm">
                                <i class="fas fa-user text-white text-xs"></i>
                            </div>
                        </button>
    
                        <div x-show="userMenuOpen" x-cloak @click.away="userMenuOpen = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute top-full  right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 py-2 z-50">
                            <div class="px-4 py-2 border-b border-neutral-200">
                                <p class="font-semibold text-sm text-neutral-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-neutral-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors duration-200">
                                <i class="fas fa-user mr-3 text-sm w-5"></i>
                                <span class="font-medium text-sm">Profile</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-error-600 hover:bg-error-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-3 text-sm w-5"></i>
                                    <span class="font-medium text-sm">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="w-10"></div>
                @endauth
            </div>
        </header>

        <!-- Desktop Header -->
        <header class="hidden lg:flex sticky top-0 bg-white shadow-md border-b border-neutral-200 z-20">
            <div class="flex-1 flex items-center justify-end px-6 py-[17.5px]">
                @auth
                <div class="flex items-center space-x-4">
                    <div>
                        @php
                            $notificationService = app(\App\Services\NotificationService::class);
                            $unreadCount = $notificationService->getUnreadCount(auth()->id());
                        @endphp
                        <x-shared.notification-bell :unreadCount="$unreadCount" />
                    </div>

                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2">
                            <div class="w-9 h-9 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center shadow-sm">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-sm text-neutral-800 truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-neutral-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }"></i>
                        </button>
    
                        <div x-show="userMenuOpen" x-cloak @click.away="userMenuOpen = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 py-2">
                            <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors duration-200">
                                <i class="fas fa-user mr-3 text-sm w-5"></i>
                                <span class="font-medium text-sm">Profile</span>
                            </a>
                            <hr class="my-1 border-neutral-200">
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-error-600 hover:bg-error-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-3 text-sm w-5"></i>
                                    <span class="font-medium text-sm">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <main class="p-6">
            {{-- Main content goes here --}}
        </main>
    </div>
</div>

{{-- Toast Notification --}}
@auth
    <div x-cloak x-show="showNotification" 
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100" 
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 z-50 w-full bg-white border rounded-lg shadow-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
         style="display: none; max-width: 24rem;">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div x-show="notificationType === 'success'" class="text-success-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div x-show="notificationType === 'error'" class="text-error-600">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div x-show="notificationType === 'warning'" class="text-warning-600">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div x-show="notificationType === 'info'" class="text-blue-600">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p x-text="notificationMessage" class="text-sm font-medium"
                        :class="{
                            'text-success-800': notificationType === 'success',
                            'text-error-800': notificationType === 'error',
                            'text-warning-800': notificationType === 'warning',
                            'text-blue-800': notificationType === 'info'
                        }">
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="showNotification = false"
                            class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="h-1 w-full" 
             :class="{
                 'bg-success-100': notificationType === 'success',
                 'bg-error-100': notificationType === 'error',
                 'bg-warning-100': notificationType === 'warning',
                 'bg-blue-100': notificationType === 'info'
             }">
            <div class="h-full animate-pulse"
                :class="{
                    'bg-success-600': notificationType === 'success',
                    'bg-error-600': notificationType === 'error',
                    'bg-warning-600': notificationType === 'warning',
                    'bg-blue-600': notificationType === 'info'
                }"
                style="animation: shrink 5s linear;">
            </div>
        </div>
    </div>
@endauth

<style>
    @keyframes shrink {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }
    
    aside::-webkit-scrollbar {
        width: 6px;
    }
    
    aside::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    aside::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    aside::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>