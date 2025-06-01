<header>
    <p>
        nav
    </p>
    <a href="{{ route('guest.home') }}">
        test
    </a>
    @guest
        <a href="{{ route('guest.auth.login') }}">
            login
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
        <a href="{{ route('admin.buyer-ratings.index') }}">
            index
        </a>
    @endif
</header>