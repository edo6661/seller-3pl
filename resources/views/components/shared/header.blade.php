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
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none">
                Logout
            </button>
        </form>
    @endauth
    @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}">
            Dashboard
        </a>
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
        </nav>
    @endif
</header>