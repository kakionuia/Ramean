<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $laundry->store_name }} · Laundryku</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
</head>
<body class="customer-page">
    <header class="customer-nav">
        <div class="nav-user-actions"><a class="nav-profile-link" href="{{ route('profile') }}"><span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="nav-user-copy"><small>Akun</small><strong>{{ auth()->user()->name }}</strong></span></a><a class="nav-back-link" href="{{ route('dashboard') }}">← Beranda</a></div>
        <a class="customer-brand" href="{{ route('dashboard') }}"><span class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a>
    </header>

    <main class="detail-main">
        <nav class="breadcrumbs"><a href="{{ route('dashboard') }}">Beranda</a><span>/</span><span>{{ $laundry->store_name }}</span></nav>
        <section class="detail-cover" style="--laundry-image: url('{{ $laundry->logo ? asset('storage/' . $laundry->logo) : 'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?auto=format&fit=crop&w=1600&q=85' }}')">
            <span class="cover-kicker">LAUNDRY DI SEKITARMU</span><h1>{{ $laundry->store_name }}</h1><p>{{ $laundry->address }}</p>
        </section>

        @if (session('success'))<div class="customer-notice">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="customer-notice notice-error">{{ $errors->first() }}</div>@endif

        <div class="detail-columns">
            <section class="detail-about">
                <p class="overline">TENTANG TEMPAT INI</p><h2>Rawat pakaianmu dengan lebih mudah.</h2>
                <p>{{ $laundry->description ?: 'Laundry lokal yang siap membantu kebutuhan perawatan pakaianmu.' }}</p>
                <div class="detail-facts"><span><small>ALAMAT</small><strong>{{ $laundry->address }}</strong></span><span><small>TELEPON</small><strong>{{ $laundry->store_phone }}</strong></span><span><small>PEMILIK</small><strong>{{ $laundry->owner_name }}</strong></span></div>
            </section>
            <section class="detail-services" aria-labelledby="servicesHeading">
                <div class="section-heading"><div><p class="overline">PILIHAN PERAWATAN</p><h2 id="servicesHeading">Layanan tersedia</h2></div></div>
                <div class="detail-service-list">
                    @forelse ($services as $service)
                        <article class="detail-service-card">
                            <div class="service-card-photo" role="img" aria-label="Pakaian bersih"></div>
                            <div class="service-card-copy"><div class="service-card-heading"><h3>{{ $service->name }}</h3>@if ($service->original_price && $service->original_price > $service->price && (!$service->promo_until || $service->promo_until >= today()->toDateString()))<span class="package-tag">{{ $service->promo_label ?: 'PROMO' }}</span>@endif</div><p>{{ $service->description ?: 'Perawatan pakaian oleh ' . $laundry->store_name }}</p><small>Estimasi {{ $service->duration_hours }} jam</small><div class="service-price"><strong>Rp {{ number_format($service->price, 0, ',', '.') }}</strong>@if ($service->original_price && $service->original_price > $service->price)<del>Rp {{ number_format($service->original_price, 0, ',', '.') }}</del>@endif</div></div>
                            <button class="primary-button service-order-button" type="button" data-order-service="{{ $service->id }}">Pesan</button>
                        </article>
                    @empty
                        <div class="empty-discovery"><span>◈</span><h3>Belum ada layanan aktif</h3><p>Laundry ini belum menambahkan layanan untuk dipesan.</p></div>
                    @endforelse
                </div>
            </section>
        </div>

        @if ($services->isNotEmpty())
            <section class="booking-section" id="booking-form" hidden>
                <div class="booking-intro"><p class="overline">PESAN SEKARANG</p><h2>Detail cucianmu</h2><p>Pilih kebutuhan cucian dan cara bayar. Laundry akan mengonfirmasi pesananmu.</p><small class="wallet-available">Saldo tersedia: <strong>Rp {{ number_format($walletBalance, 0, ',', '.') }}</strong></small></div>
                <form class="booking-form" action="{{ route('orders.store', $laundry->id) }}" method="POST">@csrf
                    <label>Paket layanan<select name="service_id" id="serviceSelect" required>@foreach ($services as $service)<option value="{{ $service->id }}" data-price="{{ $service->price }}" @selected((string) request('service') === (string) $service->id)>{{ $service->name }} · Rp {{ number_format($service->price, 0, ',', '.') }}</option>@endforeach</select></label>
                    <div class="form-grid"><label>Jenis pakaian<select name="garment_type" required><option value="kiloan">Pakaian kiloan</option><option value="pakaian">Pakaian satuan</option><option value="selimut">Selimut</option><option value="sepatu">Sepatu</option><option value="lainnya">Lainnya</option></select></label><label>Jenis jasa cuci<select name="wash_type" required><option value="cuci">Cuci</option><option value="setrika">Setrika</option><option value="cuci_setrika">Cuci dan setrika</option></select></label></div>
                    <div class="form-grid"><label>Jumlah paket / kg<input type="number" name="quantity" min="1" max="100" value="1" required></label><label>Catatan untuk laundry<input type="text" name="notes" maxlength="500" placeholder="Contoh: pisahkan pakaian putih"></label></div>
                    <fieldset class="payment-choice"><legend>Metode pembayaran</legend><label class="payment-option"><input type="radio" name="payment_method" value="qris" checked><span><strong>QRIS simulasi</strong><small>Bayar lalu konfirmasi dari riwayat transaksi</small></span><b>QR</b></label><label class="payment-option"><input type="radio" name="payment_method" value="wallet" data-wallet-option data-wallet-balance="{{ $walletBalance }}"><span><strong>Saldo Laundryku</strong><small>Potong saldo saat pesanan dibuat</small></span><b>Rp</b></label><p class="wallet-warning" data-wallet-warning hidden>Saldo belum cukup untuk pilihan layanan ini.</p></fieldset>
                    <div class="booking-footer"><p>Total: <strong data-order-total>Rp {{ number_format($services->firstWhere('id', request('service'))?->price ?? $services->first()->price, 0, ',', '.') }}</strong> per paket / kg.</p><button class="primary-button" type="submit">Buat pesanan <span>→</span></button></div>
                </form>
            </section>
        @endif
    </main>

    <footer class="customer-footer"><a class="customer-brand" href="{{ route('dashboard') }}"><span class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a><span>Jasa laundry lokal dalam satu tempat.</span><a href="{{ route('profile', ['tab' => 'orders']) }}">Riwayat pesanan</a></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
</body>
</html>