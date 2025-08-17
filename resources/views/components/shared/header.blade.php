<header class="bg-white shadow-md sticky top-0 z-50 border-b border-neutral-200" x-data="{
    mobileMenuOpen: false,
    showNotification: false,
    notificationType: '',
    notificationMessage: '',
    showToast(type, message) {
        this.notificationType = type;
        this.notificationMessage = message;
        this.showNotification = true;
        setTimeout(() => this.showNotification = false, 5000);
    }
}"
    x-init="@if(session('success'))
    showToast('success', '{{ session('success') }}');
    @endif
    @if(session('status'))
    showToast('success', '{{ session('status') }}');
    @endif
    @if(session('error'))
    showToast('error', '{{ session('error') }}');
    @endif">
    {{-- @auth
        <x-shared.chat-notification :unreadCount="auth()->user()->getTotalUnreadMessages()" />
    @endauth --}}

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('guest.home') }}" class="flex items-center space-x-2">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-white text-sm"></i>
                    </div>
                    <span class="text-2xl font-bold text-neutral-800">ShipApp</span>
                </a>
            </div>

            <nav class="hidden md:flex items-center space-x-8">
                @guest
                    <a href="{{ route('guest.auth.login') }}"
                        class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('guest.auth.register') }}"
                        class="bg-primary-500 text-white px-4 py-2 rounded-lg hover:bg-primary-600 transition-colors duration-200 font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                    <a href="{{ route('guest.auth.redirect', ['provider' => 'google']) }}"
                        class="bg-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-secondary-600 transition-colors duration-200 font-medium">
                        <i class="fab fa-google mr-2"></i>Login with Google
                    </a>
                @endguest

                @auth
                
                    @if (auth()->user()->isAdmin())
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="{{ route('admin.buyer-ratings.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-star mr-2"></i>Ratings
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-users mr-2"></i>Users
                            </a>
                            <a href="{{ route('admin.wallets.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-wallet mr-2"></i>Wallets
                            </a>
                            <a href="{{ route('admin.products.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-box mr-2"></i>Products
                            </a>
                            <a href="{{ route('admin.pickup-requests.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-truck mr-2"></i>Pickup Requests
                            </a>
                            <a href="{{ route('chat.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-comments mr-2"></i>Chat
                            </a>
                            <a href="{{ route('admin.support.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-headset mr-2"></i>Support
                            </a>
                        </div>
                    @endif

                    @if (auth()->user()->isSeller())
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('seller.dashboard') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="{{ route('seller.wallet.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-wallet mr-2"></i>Wallet
                            </a>
                            <a href="{{ route('seller.products.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-box mr-2"></i>Produk
                            </a>
                            <a href="{{ route('seller.pickup-request.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-truck mr-2"></i>Requests
                            </a>
                            <a href="{{ route('chat.start') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-comments mr-2"></i>Chat
                            </a>
                            <a href="{{ route('seller.addresses.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                            </a>
                            <a href="{{ route('seller.support.index') }}"
                                class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                                <i class="fas fa-headset mr-2"></i>Support
                            </a>
                        </div>
                    @endif

                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center space-x-2 text-neutral-600 hover:text-primary-600 transition-colors duration-200">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div x-show="userMenuOpen" x-cloak @click.away="userMenuOpen = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 py-2">
                            <a href="{{ route('profile.index') }}"
                                class="block px-4 py-2 text-neutral-700 hover:bg-neutral-50 transition-colors duration-200">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <hr class="my-1 border-neutral-200">
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-error-600 hover:bg-error-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </nav>

            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden text-neutral-600 hover:text-primary-600 transition-colors duration-200">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <div x-cloak x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-neutral-200 py-4">
            @guest
                <div class="space-y-4">
                    <a href="{{ route('guest.auth.login') }}"
                        class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('guest.auth.register') }}"
                        class="block bg-primary-500 text-white px-4 py-2 rounded-lg hover:bg-primary-600 transition-colors duration-200 font-medium text-center">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                    <a href="{{ route('guest.auth.redirect', ['provider' => 'google']) }}"
                        class="block bg-secondary-500 text-white px-4 py-2 rounded-lg hover:bg-secondary-600 transition-colors duration-200 font-medium text-center">
                        <i class="fab fa-google mr-2"></i>Login with Google
                    </a>
                </div>
            @endguest

            @auth
                @if (auth()->user()->isAdmin())
                    <div class="space-y-4">
                        <a href="{{ route('admin.dashboard') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.buyer-ratings.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-star mr-2"></i>Buyer Ratings
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-users mr-2"></i>Users
                        </a>
                        <a href="{{ route('admin.wallets.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-wallet mr-2"></i>Wallets
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-box mr-2"></i>Products
                        </a>
                        <a href="{{ route('admin.pickup-requests.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-truck mr-2"></i>Pickup Requests
                        </a>
                        <a href="{{ route('chat.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-comments mr-2"></i>Chat
                        </a>
                    </div>
                @endif

                @if (auth()->user()->isSeller())
                    <div class="space-y-4">
                        <a href="{{ route('seller.dashboard') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('seller.wallet.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-wallet mr-2"></i>Wallet
                        </a>
                        <a href="{{ route('seller.products.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-box mr-2"></i>Produk
                        </a>
                        <a href="{{ route('seller.pickup-request.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-truck mr-2"></i>Requests
                        </a>
                         <a href="{{ route('chat.start') }}"
                            class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-comments mr-2"></i>Chat
                        </a>
                         <a href="{{ route('seller.support.index') }}"
                            class="text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-headset mr-2"></i>Support
                        </a>
                    </div>
                @endif

                <div class="border-t border-neutral-200 pt-4 mt-4">
                    <div class="flex items-center space-x-3 mb-4">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-neutral-800">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-neutral-600">{{ auth()->user()->getRoleLabelAttribute() }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('profile.index') }}"
                            class="block text-neutral-600 hover:text-primary-600 transition-colors duration-200 font-medium">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left text-error-600 hover:text-error-800 transition-colors duration-200 font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-cloak x-show="showNotification" x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
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
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p x-text="notificationMessage" class="text-sm font-medium"
                        :class="notificationType === 'success' ? 'text-success-800' : 'text-error-800'">
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
        <div class="h-1 w-full" :class="notificationType === 'success' ? 'bg-success-100' : 'bg-error-100'">
            <div class="h-full animate-pulse"
                :class="notificationType === 'success' ? 'bg-success-600' : 'bg-error-600'"
                style="animation: shrink 5s linear;">
            </div>
        </div>
    </div>

    <style>
        @keyframes shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
</header>
