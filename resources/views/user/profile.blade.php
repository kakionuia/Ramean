<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Profil · Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/general.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />
</head>

<body class="customer-page">
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
                <a href="{{ route('dashboard') }}">Dashboard</a
                ><a href="{{ route('laundries.discover') }}">Pesan Laundry</a
                ><a
                    href="{{ route('profile', ['tab' => 'orders']) }}"
                    class="{{ $activeTab === 'orders' ? 'active' : '' }}"
                    >Riwayat</a
                >
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

    <main class="profile-main">
        @if (session('success'))
            <div class="customer-notice">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="customer-notice notice-error">
                {{ $errors->first() }}
            </div>
        @endif
        <section class="profile-heading">
            <p class="overline">AKUN PELANGGAN</p>
            <h1>Profil saya</h1>
            <p>Kelola informasi akun, pesanan, dan saldo Washly.</p>
        </section>
        <div class="profile-layout">
            <aside class="profile-sidebar">
                <div class="profile-identity">
                    <div class="profile-avatar-wrap">
                        @if ($user->profile_photo_path)
                            <img
                                src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                alt="Foto profil {{ $user->name }}"
                            />
                        @else
                            <span
                                class="profile-avatar"
                                >{{ strtoupper(substr($user->name, 0, 1)) }}</span
                            >
                        @endif
                        <form
                            action="{{ route('profile.photo') }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <label class="avatar-edit" title="Ubah foto profil"
                                ><svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M4 7h3l1.5-2h7L17 7h3v12H4z" />
                                    <circle cx="12" cy="13" r="3.5" />
                                </svg
                                ><input
                                    type="file"
                                    name="photo"
                                    accept="image/*"
                                    aria-label="Ubah foto profil"
                                    onchange="this.form.submit()"
                            /></label>
                        </form>
                    </div>
                    <strong>{{ $user->name }}</strong
                    ><small>{{ $user->email }}</small
                    ><span class="profile-location"
                        ><svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                            <circle cx="12" cy="10" r="2.5" />
                        </svg
                        >{{ $primaryAddress->area ?? $user->kecamatan ?? $user->domisili ?? \Illuminate\Support\Str::limit($user->address ?: 'Alamat belum ditambahkan', 32) }}</span
                    >
                </div>
                <nav class="profile-tabs" aria-label="Menu profil">
                    <a
                        href="{{ route('profile', ['tab' => 'account']) }}"
                        class="{{ $activeTab === 'account' ? 'active' : '' }}"
                        >Informasi akun</a
                    ><a
                        href="{{ route('profile', ['tab' => 'orders']) }}"
                        class="{{ $activeTab === 'orders' ? 'active' : '' }}"
                        >Riwayat transaksi</a
                    ><a
                        href="{{ route('profile', ['tab' => 'wallet']) }}"
                        class="{{ $activeTab === 'wallet' ? 'active' : '' }}"
                        >Saldo & top-up</a
                    ><a
                        href="{{ route('profile', ['tab' => 'addresses']) }}"
                        class="{{ $activeTab === 'addresses' ? 'active' : '' }}"
                        >Daftar Alamat Saya</a
                    >
                </nav>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="profile-logout" type="submit">
                        Keluar dari akun <span>→</span>
                    </button>
                </form>
            </aside>

            <div class="profile-content">
                @if ($activeTab === 'orders')
                    <section class="profile-panel">
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">PESANANMU</p>
                                <h2>Riwayat transaksi</h2>
                            </div>
                            <a
                                class="text-action"
                                href="{{ route('laundries.discover') }}"
                                >Pesan Laundry Sekarang</a
                            >
                        </div>
                        <nav
                            class="order-filters"
                            aria-label="Filter status pesanan"
                        >
                            <a
                                href="{{ route('profile', ['tab' => 'orders', 'status' => 'all']) }}"
                                class="{{ $statusFilter === 'all' ? 'active' : '' }}"
                                >Semua</a
                            ><a
                                href="{{ route('profile', ['tab' => 'orders', 'status' => 'active']) }}"
                                class="{{ $statusFilter === 'active' ? 'active' : '' }}"
                                >Berjalan</a
                            ><a
                                href="{{ route('profile', ['tab' => 'orders', 'status' => 'completed']) }}"
                                class="{{ $statusFilter === 'completed' ? 'active' : '' }}"
                                >Selesai</a
                            ><a
                                href="{{ route('profile', ['tab' => 'orders', 'status' => 'cancelled']) }}"
                                class="{{ $statusFilter === 'cancelled' ? 'active' : '' }}"
                                >Dibatalkan</a
                            >
                        </nav>
                        <div class="order-history-list">
                            @forelse ($transactions as $transaction)
                                <article class="order-history-card">
                                    <div class="order-history-top">
                                        <div>
                                            <small
                                                >{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y · H:i') }}</small
                                            >
                                            <h3>
                                                {{ $transaction->store_name }}
                                            </h3>
                                        </div>
                                        <span
                                            class="status-pill status-{{ $transaction->status }}"
                                            >{{ ucfirst($transaction->status) }}</span
                                        >
                                    </div>
                                    <p>{{ $transaction->service_name }}</p>
                                    @if ($transaction->order_details)
                                        <small
                                            class="order-details"
                                            >{{ $transaction->order_details }}</small
                                        >
                                    @endif
                                    <div class="order-history-bottom">
                                        <span
                                            >{{ $transaction->weight_kg !== null ? number_format($transaction->weight_kg, 2, ',', '.') . ' kg' : ($transaction->delivery_method === 'pickup' ? 'Antar-jemput' : 'Drop-off') . ' · Menunggu timbang' }}</span
                                        ><strong
                                            >{{ $transaction->weighed_at ? 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') : 'Tagihan menunggu timbang' }}</strong
                                        ><span class="payment-note"
                                            >{{ $transaction->payment_status === 'unpaid' && $transaction->payment_method === 'wallet' ? 'Menunggu konfirmasi owner' : ucfirst($transaction->payment_status) }} · {{ match ($transaction->payment_method) { 'cash' => 'Tunai', 'wallet' => 'Saldo Washly', 'qris' => 'QRIS', default => 'Belum dipilih' } }}</span
                                        ><a
                                            class="order-detail-link"
                                            href="{{ route('orders.show', $transaction->id) }}"
                                            >Detail</a
                                        >
                                    </div>
                                    @if ($transaction->payment_status === 'unpaid' && $transaction->payment_method === 'qris')
                                        <div class="simulated-payment">
                                            <canvas
                                                class="fake-qr"
                                                width="168"
                                                height="168"
                                                data-fake-qr="{{ $transaction->payment_reference }}"
                                                aria-label="QRIS simulasi untuk pesanan"
                                            ></canvas>
                                            <div>
                                                <strong>QRIS simulasi</strong>
                                                <p>Gunakan referensi pembayaran ini sebagai kode demo. Tidak ada transaksi uang nyata.</p>
                                                <code
                                                    >{{ $transaction->payment_reference }}</code
                                                >
                                                <form
                                                    action="{{ route('orders.confirm-qris', $transaction->id) }}"
                                                    method="POST"
                                                    data-payment-confirm
                                                >
                                                    @csrf
                                                    <button
                                                        class="primary-button"
                                                        type="submit"
                                                    >
                                                        Konfirmasi simulasi
                                                        pembayaran
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="empty-profile">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="4" y="3" width="16" height="18" rx="2" />
                                        <path d="M4 8h16" />
                                        <circle cx="12" cy="14" r="4" />
                                    </svg>
                                    <h3>Belum ada pesanan</h3>
                                    <p>Pesanan laundry yang kamu buat akan muncul di sini.</p>
                                    <a
                                        class="primary-button"
                                        href="{{ route('laundries.discover') }}"
                                        >Pesan Laundry Sekarang</a
                                    >
                                </div>
                            @endforelse
                        </div>
                        @if ($transactions->hasPages())
                            <div class="profile-pagination">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </section>
                @elseif ($activeTab === 'addresses')
                    <section class="profile-panel">
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">LOKASI JEMPUT</p>
                                <h2>Daftar Alamat Saya</h2>
                                <p>Simpan alamat rumah, kos, atau kantor untuk pesanan laundry.</p>
                            </div>
                        </div>
                        <div class="address-list">
                            @forelse ($addresses as $address)
                                <article class="saved-address">
                                    <div class="saved-address-head">
                                        <h3>{{ $address->label }}</h3>
                                        @if ($address->is_primary)
                                            <span class="primary-address-badge"
                                                >Alamat utama</span
                                            >
                                        @endif
                                    </div>
                                    <p>{{ $address->address }}</p>
                                    <small
                                        >{{ $address->recipient_name }} · {{ $address->recipient_phone }}
                                        @if ($address->area) ·{{ $address->area }}@endif
                                    </small>
                                    <div class="saved-address-actions">
                                        @unless ($address->is_primary)
                                            <form
                                                action="{{ route('profile.addresses.primary', $address->id) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method ('PUT')
                                                <button type="submit">
                                                    Jadikan alamat utama
                                                </button>
                                            </form>
                                        @endunless
                                        <form
                                            action="{{ route('profile.addresses.delete', $address->id) }}"
                                            method="POST"
                                            onsubmit="
                                                return confirm(
                                                    'Hapus alamat ini?',
                                                );
                                            "
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button
                                                class="delete-address"
                                                type="submit"
                                            >
                                                Hapus alamat
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <div class="empty-address">
                                    Belum ada alamat tersimpan. Tambahkan alamat
                                    pertama untuk memudahkan penjemputan.
                                </div>
                            @endforelse
                        </div>
                        <div class="address-form-heading">
                            <strong>Tambah alamat baru</strong
                            ><span
                                >Isi detail lokasi penjemputan dan
                                penerima.</span
                            >
                        </div>
                        <form
                            class="address-form"
                            action="{{ route('profile.addresses.store') }}"
                            method="POST"
                        >
                            @csrf
                            <label
                                >Label alamat<input
                                    name="label"
                                    maxlength="80"
                                    placeholder="Rumah, Kos, atau Kantor"
                                    required
                            /></label>
                            <label
                                >Nama penerima<input
                                    name="recipient_name"
                                    maxlength="120"
                                    value="{{ $user->name }}"
                                    required
                            /></label>
                            <label
                                >Nomor telepon penerima<input
                                    name="recipient_phone"
                                    maxlength="30"
                                    value="{{ $user->phone }}"
                                    required
                            /></label>
                            <label
                                >Area singkat<input
                                    name="area"
                                    maxlength="160"
                                    placeholder="Cileungsi, Bogor"
                            /></label>
                            <label class="wide"
                                >Alamat lengkap<textarea
                                    name="address"
                                    maxlength="2000"
                                    placeholder="Nama jalan, nomor rumah, RT/RW, patokan..."
                                    required
                                ></textarea>
                            </label>
                            <label class="checkbox-field wide"
                                ><input
                                    type="checkbox"
                                    name="is_primary"
                                    value="1"
                                />
                                Jadikan alamat utama</label
                            >
                            <button class="primary-button" type="submit">
                                Simpan Alamat
                            </button>
                        </form>
                    </section>
                @elseif ($activeTab === 'wallet')
                    <section class="profile-panel wallet-panel">
                        <div class="wallet-card">
                            <div>
                                <p class="overline">SALDO WASHLY</p>
                                <small>Saldo tersedia</small
                                ><strong
                                    >Rp {{ number_format($balance, 0, ',', '.') }}</strong
                                >
                            </div>
                            <span class="wallet-mark">W</span>
                        </div>
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">ISI SALDO</p>
                                <h2>Ajukan top-up QRIS</h2>
                                <p>Pindai kode simulasi; saldo ditambahkan setelah admin menyetujui permintaan.</p>
                            </div>
                        </div>
                        <form
                            class="topup-form"
                            action="{{ route('profile.topups.store') }}"
                            method="POST"
                        >
                            @csrf
                            <label
                                >Nominal<input
                                    type="number"
                                    name="amount"
                                    min="10000"
                                    max="5000000"
                                    step="5000"
                                    placeholder="Minimum Rp 10.000"
                                    required /></label
                            ><input
                                type="hidden"
                                name="payment_method"
                                value="qris"
                            />
                            <div class="qris-method-label">
                                <span>QR</span><strong>QRIS simulasi</strong
                                ><small
                                    >Kode pembayaran akan dibuat setelah
                                    permintaan dikirim.</small
                                >
                            </div>
                            <button class="primary-button" type="submit">
                                Buat kode QRIS <span>→</span>
                            </button>
                        </form>
                        <div class="topup-history">
                            <h3>Riwayat top-up</h3>
                            @forelse ($topups as $topup)
                                <div class="topup-row">
                                    <span
                                        ><strong
                                            >Rp {{ number_format($topup->amount, 0, ',', '.') }}</strong
                                        ><small
                                            >QRIS · {{ \Carbon\Carbon::parse($topup->created_at)->format('d M Y') }}</small
                                        ></span
                                    ><span
                                        class="status-pill status-{{ $topup->status }}"
                                        >{{ ucfirst($topup->status) }}</span
                                    >
                                </div>
                                @if ($topup->status === 'pending')
                                    <div class="simulated-payment topup-qr">
                                        <canvas
                                            class="fake-qr"
                                            width="168"
                                            height="168"
                                            data-fake-qr="{{ $topup->payment_reference }}"
                                            aria-label="QRIS simulasi untuk top-up"
                                        ></canvas>
                                        <div>
                                            <strong>QRIS simulasi</strong>
                                            <p>Tunjukkan referensi ini untuk pencocokan manual oleh admin. Kode ini tidak memproses uang nyata.</p>
                                            <code
                                                >{{ $topup->payment_reference }}</code
                                            >
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="muted-copy">Belum ada permintaan top-up.</p>
                            @endforelse
                        </div>
                    </section>
                @else
                    <section class="profile-panel">
                        <div class="profile-panel-heading">
                            <div>
                                <p class="overline">INFORMASI PRIBADI</p>
                                <h2>Detail akun</h2>
                            </div>
                            <span class="account-status">AKUN AKTIF</span>
                        </div>
                        <div class="account-details">
                            <div>
                                <small>Nama lengkap</small
                                ><strong>{{ $user->name }}</strong>
                            </div>
                            <div>
                                <small>Email</small
                                ><strong>{{ $user->email }}</strong>
                            </div>
                            <div>
                                <small>Nomor telepon</small
                                ><strong
                                    >{{ $user->phone ?: 'Belum ditambahkan' }}</strong
                                >
                            </div>
                            <div>
                                <small>Alamat</small
                                ><strong
                                    >{{ $user->address ?: 'Belum ditambahkan' }}</strong
                                >
                            </div>
                            <div>
                                <small>Domisili</small
                                ><strong
                                    >{{ implode(', ', array_filter([$user->wilayah_akhir, $user->kecamatan, $user->sub_domisili, $user->domisili])) ?: 'Belum ditambahkan' }}</strong
                                >
                            </div>
                            <div>
                                <small>Bergabung sejak</small
                                ><strong
                                    >{{ $user->created_at?->format('d M Y') ?: '—' }}</strong
                                >
                            </div>
                        </div>
                        <div class="account-shortcuts">
                            <a
                                href="{{ route('profile', ['tab' => 'orders']) }}"
                                ><span>↕</span
                                ><span
                                    ><strong>Riwayat transaksi</strong
                                    ><small
                                        >Lihat status dan detail pesanan</small
                                    ></span
                                ><b>→</b></a
                            ><a
                                href="{{ route('profile', ['tab' => 'wallet']) }}"
                                ><span>Rp</span
                                ><span
                                    ><strong>Saldo & top-up</strong
                                    ><small
                                        >Saldo saat ini Rp {{ number_format($balance, 0, ',', '.') }}</small
                                    ></span
                                ><b>→</b></a
                            >
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </main>

    <footer class="customer-footer">
        <a class="customer-brand" href="{{ route('dashboard') }}"
            ><img
                class="profile-brand-logo"
                src="{{ asset('images/washly-logo.png') }}"
                alt=""
            />Washly</a
        ><span>Jasa laundry lokal dalam satu tempat.</span
        ><a href="{{ route('dashboard') }}">Beranda</a>
    </footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
