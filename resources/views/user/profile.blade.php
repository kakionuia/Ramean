<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil · Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
    <style>
        body.customer-page{display:flex;min-height:100vh;flex-direction:column;background:#f7f8f5;font-family:Inter,"Segoe UI",Arial,sans-serif}
        .profile-heading h1,.profile-panel-heading h2,.empty-profile h3,.order-history-top h3,.wallet-card strong,.topup-history h3,.saved-address-head h3{font-family:Inter,"Segoe UI",Arial,sans-serif;font-weight:700;letter-spacing:-.035em}
        .customer-brand{font-family:inherit;font-size:21px;color:#08796f}.customer-footer .customer-brand{font-size:21px}.profile-brand-logo{width:42px;height:46px;object-fit:contain}.customer-footer .profile-brand-logo{width:34px;height:38px}
        .profile-nav{position:sticky;top:0;z-index:30;width:100%;border-bottom:1px solid #e7ece8;background:#fff}
        .profile-nav-inner{display:flex;width:min(100% - 40px,1180px);min-height:76px;align-items:center;gap:32px;margin:auto}
        .profile-nav .customer-brand{display:inline-flex;flex:none;align-items:center;gap:9px;font-size:22px;font-weight:750;letter-spacing:-.04em}
        .profile-nav .profile-brand-logo{width:42px;height:44px}
        .profile-nav-links{display:flex;align-items:center;gap:5px;padding:5px;border-radius:14px;background:#f2f2ff}
        .profile-nav-links a{padding:10px 16px;border-radius:10px;color:#52605d;font-size:13px;font-weight:650;white-space:nowrap;transition:background .15s,color .15s}
        .profile-nav-links a:hover{color:#00796f;background:#e4f4f1}.profile-nav-links a.active{color:#fff;background:#00796f;box-shadow:0 3px 8px #00796f2b}
        .profile-nav-user{display:flex;align-items:center;gap:10px;margin-left:auto;padding:6px 12px 6px 6px;border:1px solid #e7ece8;border-radius:30px;color:#25332e;background:#fff;font-size:13px;font-weight:700}
        .profile-nav-user .nav-user-avatar{display:grid;width:36px;height:36px;place-items:center;overflow:hidden;border-radius:50%;color:#15564e;background:#e1f0e7;font-size:13px;font-weight:800}
        .profile-nav-user .nav-user-avatar img{width:100%;height:100%;object-fit:cover}.profile-nav-user svg{width:15px;height:15px;fill:none;stroke:#78857e;stroke-width:1.8}
        .profile-main{width:min(100% - 40px,1180px);flex:1;margin:0 auto;padding:26px 0 42px}.customer-footer{width:100%;margin-top:auto}
        .profile-main .primary-button,.profile-main .text-action{display:inline-flex;min-height:42px;align-items:center;justify-content:center;gap:7px;padding:11px 16px;border:0;border-radius:10px;color:#fff;background:#00796f;font-size:13px;font-weight:700;line-height:1.2;text-decoration:none;white-space:nowrap}.profile-main .primary-button:hover,.profile-main .text-action:hover{background:#00645d}
        .profile-heading{padding:6px 0 23px}.profile-heading h1{font-size:36px}.profile-heading > p:last-child{font-size:13px}
        .profile-layout{grid-template-columns:270px minmax(0,1fr);gap:22px}.profile-sidebar,.profile-panel{border-color:#e2e9e4;border-radius:14px;box-shadow:0 6px 20px #24352b08}.profile-sidebar{padding:20px}
        .profile-identity strong{font-size:14px}.profile-identity small{font-size:11px}.profile-tabs a{padding:12px;border-radius:9px;font-size:12px}.profile-logout{padding:14px 10px 3px;font-size:12px}
        .profile-panel-heading{align-items:center;padding:23px 25px}.profile-panel-heading h2{font-size:23px}.profile-panel-heading > div > p:last-child:not(.overline){font-size:12px}
        .order-filters{padding:0 25px}.order-filters a{padding:9px 15px;font-size:13px}
        .order-history-list:has(.empty-profile){min-height:285px;align-content:center}.empty-profile{gap:4px;padding:28px 20px}.empty-profile h3{font-size:22px}.empty-profile p,.muted-copy{font-size:12px}.empty-profile .primary-button{margin-top:5px}
        .empty-profile>svg{width:42px;height:42px;margin-bottom:5px;padding:9px;border-radius:12px;color:#08796f;background:#e7f4f2;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        .profile-panel .order-history-list{padding:0 25px}.profile-main .account-details strong{font-size:12px}.profile-main .account-details small{font-size:10px}
        .profile-identity{position:relative}.profile-avatar-wrap{position:relative;width:78px;height:78px;margin-bottom:13px}.profile-avatar-wrap .profile-avatar{width:100%;height:100%;margin:0;overflow:hidden}.profile-avatar-wrap img{width:100%;height:100%;object-fit:cover;border-radius:50%}
        .avatar-edit{position:absolute;right:-3px;bottom:-3px;display:grid;width:29px;height:29px;place-items:center;border:2px solid white;border-radius:50%;color:white;background:#00796f;cursor:pointer}.avatar-edit svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.avatar-edit input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}
        .profile-location{display:flex;align-items:center;justify-content:center;gap:5px;margin-top:7px;color:#68766f;font-size:11px}.profile-location svg{width:13px;height:13px;fill:none;stroke:#00796f;stroke-width:1.8}
        .order-filters{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 18px}.order-filters a{padding:8px 13px;border:1px solid #e2e9e4;border-radius:20px;color:#52625b;background:#fff;font-size:12px;font-weight:650}.order-filters a.active{border-color:#00796f;color:#fff;background:#00796f}
        .address-list{display:grid;gap:12px;margin-bottom:25px}.saved-address{padding:17px;border:1px solid #e3e9e5;border-radius:12px;background:#fff}.saved-address-head{display:flex;align-items:center;justify-content:space-between;gap:12px}.saved-address-head h3{margin:0;font-size:15px}.primary-address-badge{padding:4px 8px;border-radius:14px;color:#00796f;background:#e7f6f2;font-size:10px;font-weight:700}.saved-address p{margin:8px 0;color:#52625b;font-size:12px;line-height:1.5}.saved-address small{color:#78847d}.saved-address-actions{display:flex;gap:12px;margin-top:12px}.saved-address-actions button{padding:0;border:0;color:#00796f;background:none;font:inherit;font-size:11px;font-weight:700;cursor:pointer}.saved-address-actions .delete-address{color:#a34f49}.address-form{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding-top:18px;border-top:1px solid #edf0ee}.address-form label{display:grid;gap:6px;color:#46534c;font-size:12px;font-weight:650}.address-form label.wide{grid-column:1/-1}.address-form input,.address-form textarea{width:100%;padding:10px 12px;border:1px solid #dfe6e2;border-radius:8px;font:inherit;font-size:13px}.address-form textarea{min-height:85px;resize:vertical}.address-form .checkbox-field{display:flex;align-items:center;gap:8px}.address-form .checkbox-field input{width:auto}.address-form button{grid-column:1/-1;justify-self:start}.empty-address{padding:20px;border-radius:10px;color:#65736c;background:#f7faf8;font-size:12px}
        .address-list{padding:0 25px;margin:0 0 22px}.saved-address{padding:18px 20px;border-color:#e3eae5;border-radius:12px}.saved-address-head h3{font-size:15px}.saved-address p{margin:10px 0 7px;font-size:13px}.saved-address small{font-size:12px}.saved-address-actions{padding-top:3px}.address-form-heading{margin:0 25px;padding:18px 22px 0;border:1px solid #e3eae5;border-bottom:0;border-radius:13px 13px 0 0;background:#fbfcfb}.address-form-heading strong{display:block;color:#23352d;font-size:16px}.address-form-heading span{display:block;margin-top:4px;color:#738078;font-size:12px}
        .address-form{grid-template-columns:repeat(2,minmax(0,1fr));column-gap:18px;row-gap:16px;margin:0 25px 25px;padding:16px 22px 22px;border:1px solid #e3eae5;border-top:0;border-radius:0 0 13px 13px;background:#fbfcfb}.address-form label{gap:7px;color:#394a41;font-size:12px;font-weight:650}.address-form input,.address-form textarea{min-height:44px;padding:11px 13px;border-color:#dce5df;border-radius:9px;background:white;font-size:13px}.address-form textarea{min-height:112px;line-height:1.5}.address-form .checkbox-field{min-height:40px;color:#53635a}.address-form button{grid-column:2;justify-self:end;min-width:150px}
        .profile-panel-heading{gap:16px}.profile-panel-heading .text-action{flex:0 0 auto}
        .order-history-list{gap:14px}.order-history-card{padding:19px;border-color:#e2e9e4;border-radius:13px}
        .order-history-top{align-items:flex-start}.order-history-top small{font-size:11px}.order-history-top h3{margin-top:5px;font-size:18px;line-height:1.3}
        .order-history-top .status-pill{min-height:28px;padding:0 11px;border-radius:999px;font-size:11px}
        .order-history-card>p{margin:12px 0 5px;font-size:13px;line-height:1.5}.order-history-card .order-details{font-size:11px;line-height:1.6}
        .order-history-bottom{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:10px 16px;margin-top:14px;padding-top:13px;font-size:12px;line-height:1.5}
        .order-history-bottom>span:first-child{grid-column:1;grid-row:1}.order-history-bottom>strong{grid-column:2;grid-row:1;margin:0;text-align:right;font-size:13px}
        .order-history-bottom>.payment-note{grid-column:1;grid-row:2}.order-history-bottom>.order-detail-link{grid-column:2;grid-row:2;justify-self:end;font-size:12px}
        @media(max-width:700px){.profile-panel-heading{align-items:flex-start}.order-history-card{padding:15px}.order-history-bottom{grid-template-columns:minmax(0,1fr) auto;gap:8px 10px}}
        @media(max-width:440px){.order-history-bottom{grid-template-columns:1fr}.order-history-bottom>span:first-child,.order-history-bottom>strong,.order-history-bottom>.payment-note,.order-history-bottom>.order-detail-link{grid-column:1;grid-row:auto}.order-history-bottom>strong{text-align:left}.order-history-bottom>.order-detail-link{justify-self:start}}
        @media(max-width:800px){.profile-layout{grid-template-columns:220px minmax(0,1fr)}.profile-panel-heading{align-items:flex-start}.profile-nav-inner{flex-wrap:wrap;gap:12px;padding:10px 0}.profile-nav-links{order:3;width:100%;overflow-x:auto}.profile-nav-links a{padding:9px 13px}.profile-nav-user{margin-left:auto}}
        @media(max-width:560px){.profile-nav-inner{width:calc(100% - 30px)}.profile-nav .customer-brand{font-size:19px}.profile-nav .profile-brand-logo{width:36px;height:38px}.profile-nav-user{gap:7px;padding:4px 9px 4px 4px;font-size:12px}.profile-nav-user .nav-user-avatar{width:32px;height:32px}.profile-nav-links a{font-size:11px}.profile-main{width:calc(100% - 30px);padding-top:18px}.profile-layout{grid-template-columns:1fr}.profile-heading h1{font-size:30px}.profile-panel-heading{flex-direction:column;align-items:flex-start;padding:18px}.order-filters{padding:0 18px}.profile-panel .order-history-list{padding:0 18px}.profile-main .text-action{min-height:38px;font-size:12px}.address-list{padding:0 18px}.address-form-heading{margin:0 18px;padding:16px 16px 0}.address-form{grid-template-columns:1fr;margin:0 18px 18px;padding:14px 16px 18px}.address-form label.wide,.address-form button{grid-column:1;justify-self:stretch}.customer-footer{justify-content:flex-start}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>

<body class="customer-page">
    <header class="profile-nav">
        <div class="profile-nav-inner">
            <a class="customer-brand" href="{{ route('dashboard') }}"><img class="profile-brand-logo" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a>
            <nav class="profile-nav-links" aria-label="Navigasi utama"><a href="{{ route('dashboard') }}">Dashboard</a><a href="{{ route('laundries.discover') }}">Pesan Laundry</a><a href="{{ route('profile', ['tab' => 'orders']) }}" class="{{ $activeTab === 'orders' ? 'active' : '' }}">Riwayat</a></nav>
            <a class="profile-nav-user" href="{{ route('profile') }}"><span class="nav-user-avatar">@if ($user->profile_photo_path)<img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="">@else{{ strtoupper(substr($user->name, 0, 1)) }}@endif</span><span>{{ $user->name }}</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg></a>
        </div>
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
            <p>Kelola informasi akun, pesanan, dan saldo Washly.</p>
        </section>
        <div class="profile-layout">
            <aside class="profile-sidebar">
                <div class="profile-identity"><div class="profile-avatar-wrap">@if ($user->profile_photo_path)<img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto profil {{ $user->name }}">@else<span class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>@endif<form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">@csrf<label class="avatar-edit" title="Ubah foto profil"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h3l1.5-2h7L17 7h3v12H4z"/><circle cx="12" cy="13" r="3.5"/></svg><input type="file" name="photo" accept="image/*" aria-label="Ubah foto profil" onchange="this.form.submit()"></label></form></div><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small><span class="profile-location"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $primaryAddress->area ?? $user->kecamatan ?? $user->domisili ?? \Illuminate\Support\Str::limit($user->address ?: 'Alamat belum ditambahkan', 32) }}</span></div>
                <nav class="profile-tabs" aria-label="Menu profil"><a href="{{ route('profile', ['tab' => 'account']) }}" class="{{ $activeTab === 'account' ? 'active' : '' }}">Informasi akun</a><a href="{{ route('profile', ['tab' => 'orders']) }}" class="{{ $activeTab === 'orders' ? 'active' : '' }}">Riwayat transaksi</a><a href="{{ route('profile', ['tab' => 'wallet']) }}" class="{{ $activeTab === 'wallet' ? 'active' : '' }}">Saldo & top-up</a><a href="{{ route('profile', ['tab' => 'addresses']) }}" class="{{ $activeTab === 'addresses' ? 'active' : '' }}">Daftar Alamat Saya</a></nav>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="profile-logout" type="submit">Keluar dari akun <span>→</span></button></form>
            </aside>

            <div class="profile-content">
                @if ($activeTab === 'orders')
                    <section class="profile-panel">
                        <div class="profile-panel-heading"><div><p class="overline">PESANANMU</p><h2>Riwayat transaksi</h2></div><a class="text-action" href="{{ route('laundries.discover') }}">Pesan Laundry Sekarang</a></div>
                        <nav class="order-filters" aria-label="Filter status pesanan"><a href="{{ route('profile', ['tab' => 'orders', 'status' => 'all']) }}" class="{{ $statusFilter === 'all' ? 'active' : '' }}">Semua</a><a href="{{ route('profile', ['tab' => 'orders', 'status' => 'active']) }}" class="{{ $statusFilter === 'active' ? 'active' : '' }}">Berjalan</a><a href="{{ route('profile', ['tab' => 'orders', 'status' => 'completed']) }}" class="{{ $statusFilter === 'completed' ? 'active' : '' }}">Selesai</a><a href="{{ route('profile', ['tab' => 'orders', 'status' => 'cancelled']) }}" class="{{ $statusFilter === 'cancelled' ? 'active' : '' }}">Dibatalkan</a></nav>
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
                                            class="payment-note">{{ $transaction->payment_status === 'unpaid' && $transaction->payment_method === 'wallet' ? 'Menunggu konfirmasi owner' : ucfirst($transaction->payment_status) }} ·
                                            {{ match ($transaction->payment_method) { 'cash' => 'Tunai', 'wallet' => 'Saldo Washly', 'qris' => 'QRIS', default => 'Belum dipilih' } }}</span><a
                                            class="order-detail-link"
                                            href="{{ route('orders.show', $transaction->id) }}">Detail</a>
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
                                <div class="empty-profile"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M4 8h16"/><circle cx="12" cy="14" r="4"/></svg><h3>Belum ada pesanan</h3><p>Pesanan laundry yang kamu buat akan muncul di sini.</p><a class="primary-button" href="{{ route('laundries.discover') }}">Pesan Laundry Sekarang</a></div>
                            @endforelse
                        </div>
                        @if ($transactions->hasPages())
                            <div class="profile-pagination">{{ $transactions->links() }}</div>
                        @endif
                    </section>
                @elseif ($activeTab === 'addresses')
                    <section class="profile-panel"><div class="profile-panel-heading"><div><p class="overline">LOKASI JEMPUT</p><h2>Daftar Alamat Saya</h2><p>Simpan alamat rumah, kos, atau kantor untuk pesanan laundry.</p></div></div>
                        <div class="address-list">
                            @forelse ($addresses as $address)
                                <article class="saved-address"><div class="saved-address-head"><h3>{{ $address->label }}</h3>@if ($address->is_primary)<span class="primary-address-badge">Alamat utama</span>@endif</div><p>{{ $address->address }}</p><small>{{ $address->recipient_name }} · {{ $address->recipient_phone }}@if ($address->area) · {{ $address->area }}@endif</small><div class="saved-address-actions">@unless ($address->is_primary)<form action="{{ route('profile.addresses.primary', $address->id) }}" method="POST">@csrf @method('PUT')<button type="submit">Jadikan alamat utama</button></form>@endunless<form action="{{ route('profile.addresses.delete', $address->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">@csrf @method('DELETE')<button class="delete-address" type="submit">Hapus alamat</button></form></div></article>
                            @empty
                                <div class="empty-address">Belum ada alamat tersimpan. Tambahkan alamat pertama untuk memudahkan penjemputan.</div>
                            @endforelse
                        </div>
                        <div class="address-form-heading"><strong>Tambah alamat baru</strong><span>Isi detail lokasi penjemputan dan penerima.</span></div>
                        <form class="address-form" action="{{ route('profile.addresses.store') }}" method="POST">@csrf
                            <label>Label alamat<input name="label" maxlength="80" placeholder="Rumah, Kos, atau Kantor" required></label>
                            <label>Nama penerima<input name="recipient_name" maxlength="120" value="{{ $user->name }}" required></label>
                            <label>Nomor telepon penerima<input name="recipient_phone" maxlength="30" value="{{ $user->phone }}" required></label>
                            <label>Area singkat<input name="area" maxlength="160" placeholder="Cileungsi, Bogor"></label>
                            <label class="wide">Alamat lengkap<textarea name="address" maxlength="2000" placeholder="Nama jalan, nomor rumah, RT/RW, patokan..." required></textarea></label>
                            <label class="checkbox-field wide"><input type="checkbox" name="is_primary" value="1"> Jadikan alamat utama</label>
                            <button class="primary-button" type="submit">Simpan Alamat</button>
                        </form>
                    </section>
                @elseif ($activeTab === 'wallet')
                    <section class="profile-panel wallet-panel">
                        <div class="wallet-card">
                            <div>
                                <p class="overline">SALDO WASHLY</p><small>Saldo tersedia</small><strong>Rp
                                    {{ number_format($balance, 0, ',', '.') }}</strong>
                            </div><span class="wallet-mark">W</span>
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

    <footer class="customer-footer"><a class="customer-brand" href="{{ route('dashboard') }}"><img class="profile-brand-logo" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a><span>Jasa laundry lokal dalam satu tempat.</span><a href="{{ route('dashboard') }}">Beranda</a></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>

</html>

