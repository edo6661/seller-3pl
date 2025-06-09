<header>
   
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