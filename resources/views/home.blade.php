<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laundry terdekat · Laundryku</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
</head>
<body class="customer-page">
    <header class="customer-nav">
        <div class="nav-user-actions">
            <a class="nav-profile-link" href="{{ route('profile') }}" aria-label="Buka profil">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="nav-user-copy"><small>Halo,</small><strong>{{ auth()->user()->name }}</strong></span>
            </a>
            <div class="notification-wrap">
                <button class="nav-icon-button" id="notificationButton" type="button" aria-label="Notifikasi" aria-expanded="false">
                    <span aria-hidden="true">♧</span>
                    @if ($notifications->isNotEmpty())<i></i>@endif
                </button>
                <div class="notification-popover" id="notificationPopover" hidden>
                    <strong>Aktivitas pesanan</strong>
                    @forelse ($notifications as $notification)
                        <a href="{{ route('profile', ['tab' => 'orders']) }}"><span>{{ $notification->service_name }}</span><small>{{ ucfirst($notification->status) }} · {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small></a>
                    @empty
                        <p>Belum ada notifikasi.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <a class="customer-brand" href="{{ route('dashboard') }}" aria-label="Laundryku beranda"><span class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a>
    </header>

    <main>
        <section class="home-hero" aria-label="Promo laundry">
            <div class="hero-copy">
                <p class="overline">LAUNDRY LOKAL, LEBIH DEKAT</p>
                <h1>Waktu luangmu<br>mulai dari cucian.</h1>
                <p>Temukan jasa laundry tepercaya di sekitar dan pesan tanpa repot.</p>
                <a class="primary-button" href="#laundry-list">Cari laundry <span>→</span></a>
            </div>
            <div class="hero-visual" role="img" aria-label="Pakaian bersih dan segar"></div>
            <div class="hero-carousel" data-carousel>
                @forelse ($promos as $promo)
                    <article class="promo-slide {{ $loop->first ? 'current' : '' }}" data-slide>
                        <div><span class="promo-kicker">{{ $promo->promo_label ?: 'PAKET PILIHAN' }}</span><h2>{{ $promo->store_name }}</h2><p>{{ $promo->name }} · {{ $promo->address }}</p><strong>Rp {{ number_format($promo->price, 0, ',', '.') }} <del>Rp {{ number_format($promo->original_price, 0, ',', '.') }}</del></strong><a href="{{ route('laundry.show', $promo->laundry_id) }}?service={{ $promo->id }}#booking-form">Lihat promo <span>→</span></a></div>
                    </article>
                @empty
                    <article class="promo-slide current" data-slide><div><span class="promo-kicker">PAKET PROMO</span><h2>Segar kembali,<br>lebih hemat.</h2><p>Paket cucian pilihan dari laundry di sekitarmu.</p><a href="#promo-list">Jelajahi paket <span>→</span></a></div></article>
                    <article class="promo-slide" data-slide><div><span class="promo-kicker">CUCI TANPA RIBET</span><h2>Perawatan yang<br>pas untuk harimu.</h2><p>Pilih layanan, atur kebutuhan, dan kirim pesanan.</p><a href="#laundry-list">Cari layanan <span>→</span></a></div></article>
                    <article class="promo-slide" data-slide><div><span class="promo-kicker">DI SEKITAR KAMU</span><h2>Dukung laundry<br>lokal hari ini.</h2><p>Temukan jasa cuci terdekat dari lokasimu.</p><a href="#laundry-list">Lihat laundry <span>→</span></a></div></article>
                @endforelse
                <div class="carousel-controls"><button type="button" data-carousel-prev aria-label="Promo sebelumnya">←</button><div class="carousel-dots">@foreach ($promos->isNotEmpty() ? $promos : collect([1, 2, 3]) as $index => $item)<button type="button" data-carousel-dot="{{ $index }}" aria-label="Promo {{ $index + 1 }}" class="{{ $index === 0 ? 'active' : '' }}"></button>@endforeach</div><button type="button" data-carousel-next aria-label="Promo berikutnya">→</button></div>
            </div>
        </section>

        @if (session('success'))<div class="customer-notice">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="customer-notice notice-error">{{ $errors->first() }}</div>@endif

        <section class="discovery-section" id="laundry-list">
            <div class="section-heading"><div><p class="overline">TEMUKAN DI SEKITARMU</p><h2>Cari laundry</h2></div><span class="result-count">{{ $laundries->count() }} tempat</span></div>
            <form class="search-panel" action="{{ route('dashboard') }}" method="GET">
                <label class="search-input"><span aria-hidden="true">⌕</span><input type="search" name="q" value="{{ $search }}" placeholder="Cari laundry atau layanan..." aria-label="Cari laundry atau layanan"></label>
                <label class="location-select"><span aria-hidden="true">⌖</span><select name="location" aria-label="Filter lokasi"><option value="">Semua lokasi</option>@foreach ($locations as $place)<option value="{{ $place }}" @selected($location === $place)>{{ $place }}</option>@endforeach</select></label>
                <button class="primary-button" type="submit">Cari</button>
            </form>

            @if ($locations->isNotEmpty())
                <div class="location-tags"><span>Lokasi populer</span><a href="{{ route('dashboard') }}" class="{{ $location === '' ? 'tag-active' : '' }}">Semua</a>@foreach ($locations as $place)<a href="{{ route('dashboard', ['location' => $place, 'q' => $search]) }}" class="{{ $location === $place ? 'tag-active' : '' }}">{{ $place }}</a>@endforeach</div>
            @endif

            <div class="laundry-grid">
                @forelse ($laundries as $laundry)
                    <article class="laundry-card">
                        <a href="{{ route('laundry.show', $laundry->id) }}" class="laundry-image-link">
                            <div class="laundry-image" style="--laundry-image: url('{{ $laundry->logo ? asset('storage/' . $laundry->logo) : 'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?auto=format&fit=crop&w=900&q=80' }}')"><span class="open-label">Laundry lokal</span><span class="image-arrow">↗</span></div>
                        </a>
                        <div class="laundry-card-content">
                            <div class="card-title-line"><div><p class="card-location">{{ $laundry->kecamatan ?: $laundry->domisili ?: 'Dekat kamu' }}</p><h3><a href="{{ route('laundry.show', $laundry->id) }}">{{ $laundry->store_name }}</a></h3></div><span class="rating-mark">★ <b>Baru</b></span></div>
                            <p class="laundry-address">{{ $laundry->address }}</p>
                            <div class="service-chips">@forelse ($laundry->services->take(3) as $service)<span>{{ $service->name }}</span>@empty<span>Hubungi laundry untuk layanan</span>@endforelse</div>
                            <div class="laundry-card-bottom"><span>{{ $laundry->services->isNotEmpty() ? 'Mulai Rp ' . number_format($laundry->services->min('price'), 0, ',', '.') : 'Lihat detail' }}</span><a href="{{ route('laundry.show', $laundry->id) }}">Lihat tempat <span>→</span></a></div>
                        </div>
                    </article>
                @empty
                    <div class="empty-discovery"><span>⌕</span><h3>Belum ada laundry ditemukan</h3><p>Coba kata pencarian atau lokasi lain. Laundry baru akan muncul di sini setelah terdaftar.</p></div>
                @endforelse
            </div>
        </section>

        <section class="promo-section" id="promo-list">
            <div class="section-heading"><div><p class="overline">PILIHAN HEMAT</p><h2>Paket promo cucian</h2></div></div>
            <div class="promo-grid">
                @forelse ($promos as $promo)
                    <a class="package-card" href="{{ route('laundry.show', $promo->laundry_id) }}?service={{ $promo->id }}#booking-form"><span class="package-tag">{{ $promo->promo_label ?: 'PROMO' }}</span><div class="package-text"><small>{{ $promo->store_name }} · {{ $promo->address }}</small><h3>{{ $promo->name }}</h3><p>{{ $promo->description ?: 'Paket perawatan pakaian pilihan.' }}</p><div><strong>Rp {{ number_format($promo->price, 0, ',', '.') }}</strong><del>Rp {{ number_format($promo->original_price, 0, ',', '.') }}</del></div></div><span class="package-arrow">↗</span></a>
                @empty
                    <div class="promo-empty"><span>Belum ada paket promo</span><p>Paket promo dari laundry lokal akan ditampilkan di sini.</p></div>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="customer-footer"><a class="customer-brand" href="{{ route('dashboard') }}"><span class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a><span>Jasa laundry lokal dalam satu tempat.</span><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Keluar</button></form></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
</body>
</html>