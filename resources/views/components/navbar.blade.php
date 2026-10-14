<div class="nav-bar">
    <div class="nav-logo">
        <h1>Logo</h1>
    </div>
    <div class="nav-menu">
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </div>

    <!-- Muncul HANYA KETIKA USER SUDAH LOGIN -->
    @auth
        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @endauth

    <!-- Muncul HANYA KETIKA USER BELUM LOGIN (Tamu) -->
    @guest
        <div class="menu">
            <a href="{{ url('/login') }}" class="btn">Login</a>
            <a href="{{ url('/register') }}" class="btn">Register</a>
        </div>
    @endguest
</div>