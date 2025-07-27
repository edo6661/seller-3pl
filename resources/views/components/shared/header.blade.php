<header class="bg-white shadow-md sticky top-0 z-50 border-b border-neutral-200" x-data="{ mobileMenuOpen: false }">
    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="fixed bottom-4 right-4 bg-success-50  border-success-200 text-success-700 px-4 py-3 rounded-lg shadow-lg z-50"
            role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-success-600 mr-3"></i>
                <span class="font-medium">{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()"
                    class="ml-4 text-success-600 hover:text-success-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="fixed top-4 right-4 bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded-lg shadow-lg z-50"
            role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-success-600 mr-3"></i>
                <span class="font-medium">{{ session('status') }}</span>
                <button onclick="this.parentElement.parentElement.remove()"
                    class="ml-4 text-success-600 hover:text-success-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-error-50 border border-error-200 text-error-700 px-4 py-3 rounded-lg shadow-lg z-50"
            role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-error-600 mr-3"></i>
                <span class="font-medium">{{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.remove()"
                    class="ml-4 text-error-600 hover:text-error-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Main Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('guest.home') }}" class="flex items-center space-x-2">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-white text-sm"></i>
                    </div>
                    <span class="text-2xl font-bold text-neutral-800">ShipApp</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
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
                    <!-- Admin Navigation -->
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
                        </div>
                    @endif

                    <!-- Seller Navigation -->
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
                        </div>
                    @endif

                    <!-- User Menu -->
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

                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition
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

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden text-neutral-600 hover:text-primary-600 transition-colors duration-200">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-neutral-200 py-4">
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
                <!-- Mobile Admin Navigation -->
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

                <!-- Mobile Seller Navigation -->
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
                    </div>
                @endif

                <!-- Mobile User Menu -->
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
</header>
