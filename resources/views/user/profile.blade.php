<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil · Laundryku</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
</head>

<body class="customer-page">
    <header class="customer-nav">
        <div class="nav-user-actions"><a class="nav-profile-link" href="{{ route('profile') }}"><span
                    class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span><span
                    class="nav-user-copy"><small>Akun</small><strong>{{ $user->name }}</strong></span></a><a
                class="nav-back-link" href="{{ route('dashboard') }}">← Beranda</a></div>
        <a class="customer-brand" href="{{ route('dashboard') }}"><span
                class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a>
    </header>

    <main class="profile-main">
        @if (session('success'))
            <div class="customer-notice">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="customer-notice notice-error">{{ $errors->first() }}</div>
        @endif
        <section class="profile-heading">
            <p class="overline">AKUN PELANGGAN</p>
            <h1>Profil saya</h1>
            <p>Kelola informasi akun, pesanan, dan saldo Laundryku.</p>
        </section>
        <div class="profile-layout">
            <aside class="profile-sidebar">
                <div class="profile-identity"><span
                        class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small>
                </div>
                <nav class="profile-tabs" aria-label="Menu profil"><a
                        href="{{ route('profile', ['tab' => 'account']) }}"
                        class="{{ $activeTab === 'account' ? 'active' : '' }}">Informasi akun</a><a
                        href="{{ route('profile', ['tab' => 'orders']) }}"
                        class="{{ $activeTab === 'orders' ? 'active' : '' }}">Riwayat transaksi</a><a
                        href="{{ route('profile', ['tab' => 'wallet']) }}"
                        class="{{ $activeTab === 'wallet' ? 'active' : '' }}">Saldo & top-up</a></nav>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="profile-logout"
                        type="submit">Keluar dari akun <span>→</span></button></form>
            </aside>

            <div class="profile-content">
                @if ($activeTab === 'orders')
                    <section class="profile-panel">
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">PESANANMU</p>
                                <h2>Riwayat transaksi</h2>
                            </div><a class="text-action" href="{{ route('dashboard') }}">Cari laundry →</a>
                        </div>
                        <div class="order-history-list">
                            @forelse ($transactions as $transaction)
                                <article class="order-history-card">
                                    <div class="order-history-top">
                                        <div>
                                            <small>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y · H:i') }}</small>
                                            <h3>{{ $transaction->store_name }}</h3>
                                        </div><span
                                            class="status-pill status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                                    </div>
                                    <p>{{ $transaction->service_name }}</p>
                                    @if ($transaction->order_details)
                                        <small class="order-details">{{ $transaction->order_details }}</small>
                                    @endif
                                    <div class="order-history-bottom">
                                        <span>{{ $transaction->weight_kg !== null ? number_format($transaction->weight_kg, 2, ',', '.') . ' kg' : ($transaction->delivery_method === 'pickup' ? 'Antar-jemput' : 'Drop-off') . ' · Menunggu timbang' }}</span><strong>{{ $transaction->weighed_at ? 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') : 'Tagihan menunggu timbang' }}</strong><span
                                            class="payment-note">{{ ucfirst($transaction->payment_status) }} ·
                                            {{ $transaction->payment_method ? strtoupper($transaction->payment_method) : '—' }}</span><a
                                            class="order-detail-link"
                                            href="{{ route('orders.show', $transaction->id) }}">Detail →</a>
                                    </div>
                                    @if ($transaction->payment_status === 'unpaid' && $transaction->payment_method === 'qris')
                                        <div class="simulated-payment"><canvas class="fake-qr" width="168"
                                                height="168" data-fake-qr="{{ $transaction->payment_reference }}"
                                                aria-label="QRIS simulasi untuk pesanan"></canvas>
                                            <div><strong>QRIS simulasi</strong>
                                                <p>Gunakan referensi pembayaran ini sebagai kode demo. Tidak ada
                                                    transaksi uang nyata.</p>
                                                <code>{{ $transaction->payment_reference }}</code>
                                                <form action="{{ route('orders.confirm-qris', $transaction->id) }}"
                                                    method="POST" data-payment-confirm>@csrf<button
                                                        class="primary-button" type="submit">Konfirmasi simulasi
                                                        pembayaran</button></form>
                                            </div>
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="empty-profile"><span>↕</span>
                                    <h3>Belum ada pesanan</h3>
                                    <p>Pesanan laundry yang kamu buat akan muncul di sini.</p><a class="primary-button"
                                        href="{{ route('dashboard') }}">Temukan laundry</a>
                                </div>
                            @endforelse
                        </div>
                        @if ($transactions->hasPages())
                            <div class="profile-pagination">{{ $transactions->links() }}</div>
                        @endif
                    </section>
                @elseif ($activeTab === 'wallet')
                    <section class="profile-panel wallet-panel">
                        <div class="wallet-card">
                            <div>
                                <p class="overline">SALDO LAUNDRYKU</p><small>Saldo tersedia</small><strong>Rp
                                    {{ number_format($balance, 0, ',', '.') }}</strong>
                            </div><span class="wallet-mark">L</span>
                        </div>
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">ISI SALDO</p>
                                <h2>Ajukan top-up QRIS</h2>
                                <p>Pindai kode simulasi; saldo ditambahkan setelah admin menyetujui permintaan.</p>
                            </div>
                        </div>
                        <form class="topup-form" action="{{ route('profile.topups.store') }}" method="POST">
                            @csrf<label>Nominal<input type="number" name="amount" min="10000" max="5000000"
                                    step="5000" placeholder="Minimum Rp 10.000" required></label><input
                                type="hidden" name="payment_method" value="qris">
                            <div class="qris-method-label"><span>QR</span><strong>QRIS simulasi</strong><small>Kode
                                    pembayaran akan dibuat setelah permintaan dikirim.</small></div><button
                                class="primary-button" type="submit">Buat kode QRIS <span>→</span></button>
                        </form>
                        <div class="topup-history">
                            <h3>Riwayat top-up</h3>
                            @forelse ($topups as $topup)
                                <div class="topup-row"><span><strong>Rp
                                            {{ number_format($topup->amount, 0, ',', '.') }}</strong><small>QRIS ·
                                            {{ \Carbon\Carbon::parse($topup->created_at)->format('d M Y') }}</small></span><span
                                        class="status-pill status-{{ $topup->status }}">{{ ucfirst($topup->status) }}</span>
                                </div>
                                @if ($topup->status === 'pending')
                                    <div class="simulated-payment topup-qr"><canvas class="fake-qr" width="168"
                                            height="168" data-fake-qr="{{ $topup->payment_reference }}"
                                            aria-label="QRIS simulasi untuk top-up"></canvas>
                                        <div><strong>QRIS simulasi</strong>
                                            <p>Tunjukkan referensi ini untuk pencocokan manual oleh admin. Kode ini
                                                tidak memproses uang nyata.</p>
                                            <code>{{ $topup->payment_reference }}</code>
                                        </div>
                                    </div>
                                @endif @empty<p class="muted-copy">Belum ada permintaan top-up.
                                </p>
                            @endforelse
                        </div>
                    </section>
                @else
                    <section class="profile-panel">
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">INFORMASI PRIBADI</p>
                                <h2>Detail akun</h2>
                            </div><span class="account-status">AKUN AKTIF</span>
                        </div>
                        <div class="account-details">
                            <div><small>Nama lengkap</small><strong>{{ $user->name }}</strong></div>
                            <div><small>Email</small><strong>{{ $user->email }}</strong></div>
                            <div><small>Nomor telepon</small><strong>{{ $user->phone ?: 'Belum ditambahkan' }}</strong>
                            </div>
                            <div><small>Alamat</small><strong>{{ $user->address ?: 'Belum ditambahkan' }}</strong>
                            </div>
                            <div>
                                <small>Domisili</small><strong>{{ implode(', ', array_filter([$user->wilayah_akhir, $user->kecamatan, $user->sub_domisili, $user->domisili])) ?: 'Belum ditambahkan' }}</strong>
                            </div>
                            <div><small>Bergabung
                                    sejak</small><strong>{{ $user->created_at?->format('d M Y') ?: '—' }}</strong>
                            </div>
                        </div>
                        <div class="account-shortcuts"><a
                                href="{{ route('profile', ['tab' => 'orders']) }}"><span>↕</span><span><strong>Riwayat
                                        transaksi</strong><small>Lihat status dan detail
                                        pesanan</small></span><b>→</b></a><a
                                href="{{ route('profile', ['tab' => 'wallet']) }}"><span>Rp</span><span><strong>Saldo
                                        & top-up</strong><small>Saldo saat ini Rp
                                        {{ number_format($balance, 0, ',', '.') }}</small></span><b>→</b></a></div>
                    </section>
                @endif
            </div>
        </div>
    </main>

    <footer class="customer-footer"><a class="customer-brand" href="{{ route('dashboard') }}"><span
                class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a><span>Jasa laundry lokal dalam satu
            tempat.</span><a href="{{ route('dashboard') }}">Beranda</a></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
</body>

</html>
