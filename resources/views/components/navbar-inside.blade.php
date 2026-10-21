<header class="profile-nav">
        <div class="profile-nav-inner">
            <a class="customer-brand" href="{{ route('dashboard') }}"
                ><img
                    class="profile-brand-logo"
                    src="{{ asset('images/washly-logo.png') }}"
                    alt=""
                />Washly</a
            >
            <nav class="profile-nav-links" aria-label="Navigasi utama">
                <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('laundries.discover') }}">Pesan Laundry</a
                ><a href="{{ route('profile', ['tab' => 'orders']) }}">Riwayat</a>
            </nav>
            <a class="profile-nav-user" href="{{ route('profile') }}"
                ><span class="nav-user-avatar">
                    @if (auth()->user()->profile_photo_path)
                        <img
                            src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                            alt=""
                        />
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif</span
                ><span>{{ auth()->user()->name }}</span></a
            >
        </div>
</header>