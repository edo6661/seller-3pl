<header>
   @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-4 w-4 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif
    @if(session('status'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('status') }}
            </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-4 w-4 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/>
                    </svg>
                </div>
                <div>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif
    <a href="{{ route('guest.home') }}">
        home
    </a>
    @guest
        <a href="{{ route('guest.auth.login') }}">
            login
        </a>
        <a href="{{ route('guest.auth.register') }}">
            register
        </a>
        <a href="{{ route('guest.auth.redirect',['provider' => 'google']) }}">
            login with google with redirect
        </a>
        @endguest
        @auth
        <a href="{{ route('profile.index') }}">
            profile
        </a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none">
                Logout
            </button>
        </form>
    @endauth
    @if(auth()->check() && auth()->user()->isAdmin())
        <nav class="flex items-center gap-20">
            <a href="{{ route('admin.dashboard') }}">
                Dashboard
            </a>
            <a href="{{ route('admin.buyer-ratings.index') }}">
                Buyer Ratings
            </a>
            <a href="{{ route('admin.users.index') }}">
                Users
            </a>
            <a href="{{ route('admin.wallets.index') }}">
                Wallets
            </a>
            <a href="{{ route('admin.products.index') }}">
                Products
            </a>
            <a href="{{ route('admin.pickup-requests.index') }}">
                Pickup Requests
            </a>
        </nav>
    @endif
    @if(auth()->check() && auth()->user()->isSeller())
        <nav class="flex items-center gap-20">
            <a href="{{ route('seller.dashboard') }}">
                Dashboard
            </a>
            <a href="{{ route('seller.wallet.index') }}">
                Wallet
            </a>
            <a href="{{ route('seller.products.index') }}">
                Produk
            </a>
            <a href="{{ route('seller.pickup-request.index') }}">
                Requests
            </a>
        </nav>
    @endif
</header>