<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin · Washly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('owner/owner.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>

<body class="admin-body">
@php
    $sections = [
        'dashboard' => ['Ringkasan', '<svg viewBox="0 0 24 24" fill="none"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>'],
        'applications' => ['Pengajuan owner', '<svg viewBox="0 0 24 24" fill="none"><path d="M8 4.5h8a2 2 0 0 1 2 2v13H6v-13a2 2 0 0 1 2-2Z"/><path d="M9 4.5V3h6v1.5M9 12l2 2 4-4"/></svg>'],
        'application-history' => ['Riwayat pengajuan', '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2M5 5l2 2"/></svg>'],
        'accounts' => ['Pengguna & owner', '<svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 5.8M17 14a4.5 4.5 0 0 1 3.5 4.4"/></svg>'],
        'catalog' => ['Katalog layanan', '<svg viewBox="0 0 24 24" fill="none"><rect x="4" y="3.5" width="16" height="17" rx="2.5"/><path d="M4 8h16"/><circle cx="12" cy="14" r="3.5"/><path d="M8 6h.01M11 6h.01"/></svg>'],
        'transactions' => ['Transaksi', '<svg viewBox="0 0 24 24" fill="none"><path d="M4 7h15l-3-3M20 17H5l3 3"/><path d="M19 7v4M5 17v-4"/></svg>'],
        'reviews' => ['Ulasan', '<svg viewBox="0 0 24 24" fill="none"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/></svg>'],
        'messages' => ['Pesan pelanggan', '<svg viewBox="0 0 24 24" fill="none"><path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2v-5.2a7.5 7.5 0 1 1 17-4.3Z"/><path d="M8 11h8M8 14h5"/></svg>'],
        'schedules' => ['Jadwal laundry', '<svg viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M7.5 3v4M16.5 3v4M3.5 9.5h17M8 13h3M8 16.5h2"/></svg>'],
        'topups' => ['Permintaan top-up', '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="15" rx="2.5"/><path d="M3.5 9h17M8 14h4M17 13v5M14.5 15.5h5"/></svg>'],
    ];
    $titles = [
        'dashboard' => 'Ringkasan platform',
        'applications' => 'Persetujuan owner',
        'application-history' => 'Riwayat pengajuan owner',
        'accounts' => 'Pengguna dan owner',
        'catalog' => 'Katalog layanan laundry',
        'transactions' => 'Pemantauan transaksi',
        'reviews' => 'Moderasi ulasan',
        'messages' => 'Pesan pelanggan',
        'schedules' => 'Jadwal operasional',
        'topups' => 'Permintaan top-up saldo',
    ];
@endphp

<div class="owner-shell admin-shell">
    <aside class="sidebar" id="adminSidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}"><img class="admin-brand-logo" src="{{ asset('images/washly-logo.png') }}" alt=""><span><strong>Washly Admin</strong><small>PLATFORM CONTROL</small></span></a>
        <div class="sidebar-label">PENGELOLAAN</div>
        <nav class="side-nav" aria-label="Navigasi admin">
            @foreach ($sections as $key => [$label, $icon])
                <a href="{{ route('admin.' . $key) }}" class="nav-link {{ $section === $key ? 'active' : '' }}"><span class="nav-icon" aria-hidden="true">{!! $icon !!}</span><span>{{ $label }}</span>
                    @if ($key === 'applications' && $navCounts['pending_owners'] > 0)<span class="nav-count">{{ $navCounts['pending_owners'] }}</span>@endif
                    @if ($key === 'topups' && $navCounts['pending_topups'] > 0)<span class="nav-count">{{ $navCounts['pending_topups'] }}</span>@endif
                </a>
            @endforeach
        </nav>
        <div class="sidebar-bottom"><a class="owner-mini admin-profile-shortcut" href="{{ route('admin.profile') }}"><span class="avatar">@if($admin->profile_photo_path)<img src="{{ asset('storage/' . $admin->profile_photo_path) }}" alt="">@else{{ strtoupper(substr($admin->name, 0, 1)) }}@endif</span><span><strong>{{ $admin->name }}</strong><small>Administrator · Lihat profil</small></span></a><form action="{{ route('admin.logout') }}" method="POST">@csrf<button class="logout-button" type="submit"><span>↪</span> Keluar</button></form></div>
    </aside>

    <main class="main-area">
        <header class="topbar"><button class="menu-toggle" id="adminMenuToggle" type="button" aria-label="Buka menu">☰</button><div><p class="eyebrow">ADMINISTRATOR</p><h1>{{ $titles[$section] ?? 'Panel admin' }}</h1></div><div class="topbar-right"><span class="admin-mark">@if($admin->profile_photo_path)<img src="{{ asset('storage/' . $admin->profile_photo_path) }}" alt="">@else{{ strtoupper(substr($admin->name, 0, 1)) }}@endif</span><span>{{ $admin->name }}</span></div></header>
        <div class="page-content">
            @if (session('success'))<div class="notice notice-success" role="status">{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="notice notice-error" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            @if ($section === 'dashboard')
                <section class="admin-welcome"><div><p class="eyebrow">PUSAT KENDALI</p><h2>Selamat datang, {{ $admin->name }}</h2><p>Pantau akun dan aktivitas laundry dalam satu tempat.</p></div><a href="{{ route('admin.applications') }}" class="button button-dark">Tinjau pengajuan <span>→</span></a></section>
                <section class="stats-grid admin-stats">
                    <article class="stat-card"><span class="stat-icon icon-orange">✓</span><span class="stat-label">Owner menunggu</span><strong>{{ $stats['pending_owners'] }}</strong><small>Permohonan perlu ditinjau</small></article>
                    <article class="stat-card"><span class="stat-icon icon-green">◈</span><span class="stat-label">Owner aktif</span><strong>{{ $stats['owners'] }}</strong><small>Laundry terdaftar</small></article>
                    <article class="stat-card"><span class="stat-icon icon-blue">♙</span><span class="stat-label">Pengguna</span><strong>{{ $stats['customers'] }}</strong><small>Akun pelanggan</small></article>
                    <article class="stat-card"><span class="stat-icon icon-pink">↕</span><span class="stat-label">Transaksi</span><strong>{{ $stats['transactions'] }}</strong><small>Semua pesanan masuk</small></article>
                </section>
                <section class="panel admin-trend-panel" aria-labelledby="transaction-trend-title">
                    <div class="panel-heading"><div><h2 id="transaction-trend-title">Tren transaksi</h2><p>Pesanan baru dalam tujuh hari terakhir.</p></div><span class="trend-total"><strong>{{ $transactionTrend->sum('count') }}</strong><small>transaksi</small></span></div>
                    <div class="trend-chart-wrap">
                        <svg class="trend-chart" viewBox="0 0 420 112" role="img" aria-label="Grafik jumlah transaksi harian selama tujuh hari terakhir">
                            <defs><linearGradient id="trendFill" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#008b7e" stop-opacity=".2"/><stop offset="100%" stop-color="#008b7e" stop-opacity="0"/></linearGradient></defs>
                            <line class="trend-gridline" x1="20" y1="28" x2="398" y2="28"/><line class="trend-gridline" x1="20" y1="56" x2="398" y2="56"/><line class="trend-gridline" x1="20" y1="84" x2="398" y2="84"/>
                            <path d="{{ $trendArea }}" fill="url(#trendFill)"/>
                            <polyline class="trend-line" points="{{ $trendLine }}"/>
                            @foreach ($trendPoints as $point)
                                <circle class="trend-point" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3.5"><title>{{ $point['count'] }} transaksi pada {{ $point['label'] }}</title></circle>
                                <text class="trend-label" x="{{ $point['x'] }}" y="105" text-anchor="middle">{{ $point['label'] }}</text>
                            @endforeach
                        </svg>
                    </div>
                </section>
                <section class="admin-dashboard-grid">
                    <section class="panel"><div class="panel-heading"><div><h2>Pengajuan owner terbaru</h2><p>Periksa informasi dan setujui akun laundry.</p></div><a class="text-link" href="{{ route('admin.applications') }}">Semua pengajuan →</a></div>
                        <div class="application-stack">@forelse ($applications as $application)<article class="application-row"><div class="application-person"><span class="avatar avatar-small">{{ strtoupper(substr($application->name, 0, 1)) }}</span><span><strong>{{ $application->name }}</strong><small>{{ $application->email }} · {{ $application->phone }}</small></span></div><div class="application-actions"><form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" data-admin-confirm="Setujui pengajuan {{ $application->name }}? Akun owner akan diaktifkan.">@csrf<button class="small-action action-approve" type="submit">Setujui</button></form><form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" data-admin-confirm="Tolak permohonan owner ini?">@csrf<button class="small-action action-reject" type="submit">Tolak</button></form></div></article>@empty<p class="empty-copy">Tidak ada permohonan yang menunggu.</p>@endforelse</div>
                    </section>
                    <section class="panel admin-quick-panel"><div class="panel-heading"><div><h2>Perlu perhatian</h2><p>Item yang menunggu tindakan admin.</p></div></div><a href="{{ route('admin.topups') }}"><span class="quick-icon">Rp</span><span><strong>{{ $stats['pending_topups'] }} permintaan top-up</strong><small>Konfirmasi untuk memperbarui saldo</small></span><b>→</b></a><a href="{{ route('admin.reviews') }}"><span class="quick-icon quick-review">☆</span><span><strong>{{ $stats['reviews'] }} ulasan tercatat</strong><small>Tinjau konten yang dilaporkan</small></span><b>→</b></a></section>
                </section>
                <section class="panel"><div class="panel-heading"><div><h2>Aktivitas transaksi</h2><p>Pesanan terbaru dari seluruh laundry.</p></div><a class="text-link" href="{{ route('admin.transactions') }}">Pantau semua →</a></div><div class="table-wrap"><table><thead><tr><th>Laundry</th><th>Pelanggan</th><th>Layanan</th><th>Total</th><th>Status</th><th>Waktu</th></tr></thead><tbody id="adminLiveTransactions" data-live-url="{{ route('admin.transactions.latest') }}">@forelse ($latestTransactions as $transaction)<tr><td class="cell-strong">{{ $transaction->store_name }}</td><td>{{ $transaction->customer_name }}</td><td>{{ $transaction->service_name }}</td><td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td><td><span class="badge badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></td><td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}</td></tr>@empty<tr><td colspan="6" class="empty-cell">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div></section>

            @elseif ($section === 'applications')
                <section class="panel">
                    <div class="panel-heading"><div><h2>Pengajuan owner</h2><p>Periksa identitas pemilik dan informasi outlet sebelum memberi persetujuan.</p></div><span class="admin-total">{{ $applications->total() }} menunggu</span></div>
                    <div class="table-wrap"><table class="application-table"><thead><tr><th>Nama pemilik</th><th>Nama laundry</th><th>Alamat / kota</th><th>WhatsApp</th><th>Berkas</th><th>Aksi</th></tr></thead><tbody>
                        @forelse ($applications as $application)
                            @php
                                $whatsapp = preg_replace('/\D+/', '', (string) $application->phone);
                                $whatsapp = str_starts_with($whatsapp, '0') ? '62' . substr($whatsapp, 1) : (str_starts_with($whatsapp, '62') ? $whatsapp : '62' . $whatsapp);
                            @endphp
                            <tr>
                                <td class="cell-strong">{{ $application->name }}<small class="cell-subline">{{ $application->email }}</small></td>
                                <td class="cell-strong">{{ $application->store_name ?: 'Nama laundry belum tersedia' }}<small class="cell-subline">{{ $application->store_phone ? 'Outlet: ' . $application->store_phone : 'Nomor outlet belum dicantumkan' }}</small></td>
                                <td class="application-address">{{ $application->business_address ?: 'Alamat belum tersedia' }}<small class="cell-subline">{{ $application->domisili ?: ($application->sub_domisili ?: 'Kota belum dicantumkan') }}</small></td>
                                <td>@if ($application->phone)<a class="application-whatsapp" href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">{{ $application->phone }}</a>@else—@endif</td>
                                <td>@if ($application->verification_document_path)<a class="icon-button" href="{{ route('admin.applications.document', $application->id) }}">Unduh berkas</a>@else<span class="application-no-document">Tidak ada</span>@endif</td>
                                <td><div class="application-actions"><form action="{{ route('admin.applications.approve', $application->id) }}" method="POST" data-admin-confirm="Setujui pengajuan {{ $application->name }}? Akun owner akan diaktifkan.">@csrf<button class="small-action action-approve" type="submit">Setujui</button></form><form action="{{ route('admin.applications.reject', $application->id) }}" method="POST" data-admin-confirm="Tolak dan nonaktifkan pengajuan owner ini?">@csrf<button class="small-action action-reject" type="submit">Tolak</button></form></div></td>
                            </tr>
                        @empty
                            <tr><td class="empty-cell" colspan="6">Tidak ada pengajuan owner yang menunggu persetujuan.</td></tr>
                        @endforelse
                    </tbody></table></div>
                    @if ($applications->hasPages())<div class="pagination">{{ $applications->links() }}</div>@endif
                </section>

            @elseif ($section === 'application-history')
                <section class="panel">
                    <div class="panel-heading"><div><h2>Riwayat pengajuan owner</h2><p>Pengajuan yang sudah ditinjau, beserta status akun owner dan outletnya.</p></div><span class="admin-total">{{ $applicationHistory->total() }} data</span></div>
                    <div class="admin-filters">
                        <a class="button {{ $historyFilter === 'all' ? 'button-dark' : 'button-light' }}" href="{{ route('admin.application-history', ['status' => 'all']) }}">Semua</a>
                        <a class="button {{ $historyFilter === 'approved' ? 'button-dark' : 'button-light' }}" href="{{ route('admin.application-history', ['status' => 'approved']) }}">Disetujui</a>
                        <a class="button {{ $historyFilter === 'rejected' ? 'button-dark' : 'button-light' }}" href="{{ route('admin.application-history', ['status' => 'rejected']) }}">Ditolak</a>
                        <a class="button {{ $historyFilter === 'suspended' ? 'button-dark' : 'button-light' }}" href="{{ route('admin.application-history', ['status' => 'suspended']) }}">Di-suspend</a>
                        <a class="button {{ $historyFilter === 'inactive' ? 'button-dark' : 'button-light' }}" href="{{ route('admin.application-history', ['status' => 'inactive']) }}">Nonaktif</a>
                    </div>
                    <div class="table-wrap"><table class="application-table"><thead><tr><th>Nama pemilik</th><th>Nama laundry</th><th>Alamat</th><th>WhatsApp</th><th>Status</th><th>Tanggal daftar</th></tr></thead><tbody>
                        @forelse ($applicationHistory as $application)
                            <tr>
                                <td class="cell-strong">{{ $application->name }}<small class="cell-subline">{{ $application->email }}</small></td>
                                <td class="cell-strong">{{ $application->store_name ?: 'Nama laundry belum tersedia' }}<small class="cell-subline">{{ $application->store_phone ?: 'Nomor outlet belum dicantumkan' }}</small></td>
                                <td class="application-address">{{ $application->business_address ?: 'Alamat belum tersedia' }}</td>
                                <td>{{ $application->phone ?: '—' }}</td>
                                <td><span class="badge {{ $application->status === 'active' ? 'badge-paid' : ($application->status === 'suspended' ? 'badge-pending' : 'badge-cancelled') }}">{{ ['active' => 'Disetujui', 'rejected' => 'Ditolak', 'suspended' => 'Di-suspend', 'inactive' => 'Nonaktif'][$application->status] ?? ucfirst($application->status) }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($application->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td class="empty-cell" colspan="6">Belum ada pengajuan owner yang selesai ditinjau.</td></tr>
                        @endforelse
                    </tbody></table></div>
                    @if ($applicationHistory->hasPages())<div class="pagination">{{ $applicationHistory->links() }}</div>@endif
                </section>

            @elseif ($section === 'accounts')
                <section class="panel"><div class="panel-heading"><div><h2>Daftar akun</h2><p>Pantau pelanggan dan mitra, ubah status, atau buat akun owner.</p></div><button class="button button-dark" id="addOwnerAccount" type="button">Buat akun owner</button></div><form class="admin-filters" action="{{ route('admin.accounts') }}" method="GET"><input name="q" value="{{ $search }}" placeholder="Cari nama, email, telepon..."><select name="role"><option value="">Semua peran</option><option value="user" @selected($roleFilter === 'user')>Pengguna</option><option value="owner" @selected($roleFilter === 'owner')>Owner</option></select><button class="button button-dark" type="submit">Cari</button></form><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Email / telepon</th><th>Peran</th><th>Status</th><th>Terdaftar</th><th>Aksi</th></tr></thead><tbody>
                    @forelse ($accounts as $account)
                        <tr><td class="cell-strong">{{ $account->name }}@if($account->role === 'owner' && $account->store_name)<small class="cell-subline">{{ $account->store_name }}</small>@endif</td><td>{{ $account->email }}<small class="cell-subline">{{ $account->phone ?: '—' }}</small></td><td><span class="badge {{ $account->role === 'owner' ? 'badge-processing' : 'badge-completed' }}">{{ ucfirst($account->role) }}</span></td><td><span class="badge {{ $account->status === 'active' ? 'badge-paid' : ($account->status === 'pending' ? 'badge-pending' : 'badge-cancelled') }}">{{ ucfirst($account->status) }}</span></td><td>{{ \Carbon\Carbon::parse($account->created_at)->format('d M Y') }}</td><td class="actions"><button class="icon-button edit-account" type="button" data-name="{{ $account->name }}" data-email="{{ $account->email }}" data-phone="{{ $account->phone }}" data-address="{{ $account->default_address ?: $account->address }}" data-role="{{ $account->role }}" data-status="{{ $account->status }}" data-email-verified="{{ $account->email_verified_at ? '1' : '0' }}" data-phone-verified="{{ $account->phone_verified_at ? '1' : '0' }}" data-store-name="{{ $account->store_name }}" data-store-phone="{{ $account->store_phone }}" data-outlet-address="{{ $account->outlet_address }}" data-service-city="{{ $account->service_city }}" data-service-district="{{ $account->service_district }}" data-latitude="{{ $account->latitude }}" data-longitude="{{ $account->longitude }}" data-operational-status="{{ $account->operational_status ?: 'open' }}" data-pickup-available="{{ $account->pickup_available ? '1' : '0' }}" data-pickup-fee="{{ $account->pickup_fee ?? 0 }}" data-update-url="{{ route('admin.accounts.update', $account->id) }}">Edit</button>@if($account->status !== 'inactive')<form class="inline-form" action="{{ route('admin.accounts.delete', $account->id) }}" method="POST" data-admin-confirm="Nonaktifkan akun {{ $account->name }}? Data outlet dan riwayat transaksi akan tetap tersimpan.">@csrf @method('DELETE')<button class="icon-button danger-text" type="submit">Nonaktifkan</button></form>@endif</td></tr>
                    @empty<tr><td class="empty-cell" colspan="6">Tidak ada akun yang cocok.</td></tr>@endforelse
                    </tbody></table></div>@if ($accounts->hasPages())<div class="pagination">{{ $accounts->links() }}</div>@endif</section>
                <dialog class="form-dialog" id="accountDialog"><form id="accountForm" method="POST">@csrf @method('PUT')<div class="dialog-heading"><div><p class="eyebrow">MANAJEMEN AKUN</p><h2 id="accountDialogTitle">Edit akun</h2></div><button type="button" class="close-dialog" data-close-account aria-label="Tutup">×</button></div><label>Nama<input name="name" id="accountName" required maxlength="255"></label><label>Email<input name="email" id="accountEmail" type="email" required></label><label>Telepon<input name="phone" id="accountPhone" maxlength="30"></label><label>Status akun<select name="status" id="accountStatus"></select></label><div class="form-grid"><label class="check-label"><input name="email_verified" id="accountEmailVerified" type="checkbox" value="1"> Email terverifikasi</label><label class="check-label"><input name="phone_verified" id="accountPhoneVerified" type="checkbox" value="1"> Nomor HP terverifikasi</label></div><div class="account-role-fields" id="customerAccountFields"><label>Alamat default<textarea name="address" id="accountAddress" rows="2"></textarea></label></div><div class="account-role-fields" id="ownerAccountFields"><p class="eyebrow">INFORMASI OUTLET</p><label>Nama outlet<input name="store_name" id="accountStoreName" maxlength="255"></label><label>Alamat outlet<textarea name="outlet_address" id="accountOutletAddress" rows="2"></textarea></label><div class="form-grid"><label>Nomor kontak outlet<input name="store_phone" id="accountStorePhone" maxlength="30"></label><label>Area layanan (kota)<input name="service_city" id="accountServiceCity" maxlength="120"></label></div><div class="form-grid"><label>Kecamatan<input name="service_district" id="accountServiceDistrict" maxlength="120"></label><label>Status operasional<select name="operational_status" id="accountOperationalStatus"><option value="open">Buka</option><option value="closed">Tutup sementara</option><option value="banned">Banned</option></select></label></div><div class="form-grid"><label>Latitude<input name="latitude" id="accountLatitude" type="number" step="any" min="-90" max="90"></label><label>Longitude<input name="longitude" id="accountLongitude" type="number" step="any" min="-180" max="180"></label></div><div class="form-grid"><label>Biaya antar-jemput (Rp)<input name="pickup_fee" id="accountPickupFee" type="number" min="0" step="500"></label><label class="check-label"><input name="pickup_available" id="accountPickupAvailable" type="checkbox" value="1"> Melayani antar-jemput</label></div></div><details class="password-reset"><summary>Reset password (opsional)</summary><div class="form-grid"><label>Password baru<input name="new_password" id="accountNewPassword" type="password" minlength="8" autocomplete="new-password"></label><label>Konfirmasi password<input name="new_password_confirmation" id="accountNewPasswordConfirmation" type="password" minlength="8" autocomplete="new-password"></label></div></details><div class="dialog-actions"><button type="button" class="button button-light" data-close-account>Batal</button><button class="button button-dark" type="submit">Simpan perubahan</button></div></form></dialog>
                <dialog class="form-dialog" id="ownerAccountDialog"><form action="{{ route('admin.accounts.owners.store') }}" method="POST" autocomplete="off">@csrf<div class="dialog-heading"><div><p class="eyebrow">AKUN MITRA</p><h2>Buat akun owner</h2></div><button type="button" class="close-dialog" data-close-owner-account aria-label="Tutup">×</button></div><p class="owner-account-note">Akun akan langsung aktif dan dapat digunakan untuk masuk ke dashboard owner.</p><label>Nama pemilik<input name="name" autocomplete="off" required maxlength="255"></label><div class="form-grid"><label>Email<input name="email" type="email" autocomplete="off" required maxlength="255"></label><label>Nomor WhatsApp<input name="phone" type="tel" autocomplete="off" required maxlength="20"></label></div><label>Nama outlet laundry<input name="store_name" autocomplete="off" required maxlength="255"></label><label>Alamat outlet<textarea name="address" autocomplete="off" rows="3" required maxlength="255"></textarea></label><div class="form-grid"><label>Password<input name="password" type="password" autocomplete="new-password" minlength="8" required></label><label>Konfirmasi password<input name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></label></div><div class="dialog-actions"><button type="button" class="button button-light" data-close-owner-account>Batal</button><button class="button button-dark" type="submit">Buat owner</button></div></form></dialog>

            @elseif ($section === 'catalog')
                <section class="panel">
                    <div class="panel-heading"><div><h2>Semua layanan owner</h2><p>Kelola layanan, harga, promo, dan status dari seluruh laundry.</p></div><button class="button button-dark" type="button" id="addCatalogService">Tambah layanan</button></div>
                    <div class="table-wrap"><table><thead><tr><th>Laundry</th><th>Layanan</th><th>Harga</th><th>Promo</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td class="cell-strong">{{ $service->store_name }}</td>
                                <td>{{ $service->name }}<small class="cell-subline">{{ $service->description ?: 'Tanpa deskripsi' }}</small></td>
                                <td>Rp {{ number_format($service->price, 0, ',', '.') }}<small class="cell-subline">{{ $service->duration_hours }} jam</small></td>
                                <td>{{ $service->original_price ? ($service->promo_label ?: 'Promo') . ' · Rp ' . number_format($service->original_price, 0, ',', '.') : '—' }}</td>
                                <td><span class="badge {{ $service->is_active ? 'badge-paid' : 'badge-cancelled' }}">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="actions"><button class="icon-button edit-catalog-service" type="button" data-laundry="{{ $service->laundry_id }}" data-name="{{ $service->name }}" data-description="{{ $service->description }}" data-price="{{ $service->price }}" data-duration="{{ $service->duration_hours }}" data-active="{{ $service->is_active ? '1' : '0' }}" data-promo-label="{{ $service->promo_label }}" data-original-price="{{ $service->original_price }}" data-promo-until="{{ $service->promo_until }}" data-update-url="{{ route('admin.catalog.update', $service->id) }}">Edit</button><form action="{{ route('admin.catalog.delete', $service->id) }}" method="POST" data-admin-confirm="Hapus layanan ini dari katalog?">@csrf @method('DELETE')<button class="icon-button danger-text" type="submit">Hapus</button></form></td>
                            </tr>
                        @empty
                            <tr><td class="empty-cell" colspan="6">Belum ada layanan di katalog.</td></tr>
                        @endforelse
                    </tbody></table></div>
                    @if ($services->hasPages())<div class="pagination">{{ $services->links() }}</div>@endif
                </section>
                <dialog class="form-dialog" id="catalogServiceDialog">
                    <form id="catalogServiceForm" action="{{ route('admin.catalog.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="catalogServiceMethod" value="POST">
                        <div class="dialog-heading"><div><p class="eyebrow">KATALOG WASHLY</p><h2 id="catalogServiceTitle">Tambah layanan</h2></div><button type="button" class="close-dialog" data-close-catalog-service aria-label="Tutup">×</button></div>
                        <label>Laundry<select name="laundry_id" id="catalogLaundry" required><option value="">Pilih laundry</option>@foreach ($laundries as $laundry)<option value="{{ $laundry->id }}">{{ $laundry->store_name }}</option>@endforeach</select></label>
                        <label>Nama layanan<input name="name" id="catalogServiceName" required maxlength="120" placeholder="Contoh: Cuci kering kiloan"></label>
                        <label>Deskripsi<textarea name="description" id="catalogServiceDescription" rows="3" maxlength="1000" placeholder="Ringkasan layanan"></textarea></label>
                        <div class="form-grid"><label>Harga (Rp)<input name="price" id="catalogServicePrice" type="number" min="0" step="500" required></label><label>Durasi (jam)<input name="duration_hours" id="catalogServiceDuration" type="number" min="1" max="720" value="24" required></label></div>
                        <div class="form-grid"><label>Harga sebelum promo<input name="original_price" id="catalogServiceOriginalPrice" type="number" min="0" step="500" placeholder="Opsional"></label><label>Label promo<input name="promo_label" id="catalogServicePromoLabel" maxlength="50" placeholder="Contoh: Hemat 20%"></label></div>
                        <label>Berlaku sampai<input name="promo_until" id="catalogServicePromoUntil" type="date"></label>
                        <label class="check-label"><input name="is_active" id="catalogServiceActive" type="checkbox" value="1" checked> Tampilkan sebagai layanan aktif</label>
                        <div class="dialog-actions"><button type="button" class="button button-light" data-close-catalog-service>Batal</button><button class="button button-dark" type="submit">Simpan layanan</button></div>
                    </form>
                </dialog>

            @elseif ($section === 'transactions')
                <section class="admin-stats report-summary">
                    <article class="stat-card"><span class="stat-label">Jumlah transaksi</span><strong>{{ number_format($transactionSummary->transaction_count, 0, ',', '.') }}</strong><small>Sesuai filter laporan</small></article>
                    <article class="stat-card"><span class="stat-label">Nilai transaksi lunas</span><strong>Rp {{ number_format($transactionSummary->paid_total, 0, ',', '.') }}</strong><small>Total pembayaran berstatus lunas</small></article>
                    <article class="stat-card"><span class="stat-label">Belum dibayar</span><strong>Rp {{ number_format($transactionSummary->unpaid_total, 0, ',', '.') }}</strong><small>Nilai pesanan yang belum lunas</small></article>
                </section>
                <section class="panel">
                    <div class="panel-heading"><div><h2>Laporan transaksi seluruh laundry</h2><p>Ringkasan nilai kotor berdasarkan data pesanan, tanpa perhitungan komisi platform.</p></div></div>
                    <form class="admin-filters transaction-filters" action="{{ route('admin.transactions') }}" method="GET">
                        <label>Dari tanggal<input type="date" name="from" value="{{ $transactionFilters['from'] ?? '' }}"></label>
                        <label>Sampai tanggal<input type="date" name="until" value="{{ $transactionFilters['until'] ?? '' }}"></label>
                        <label>Laundry<select name="laundry_id"><option value="">Semua laundry</option>@foreach ($laundries as $laundry)<option value="{{ $laundry->id }}" @selected((string) ($transactionFilters['laundry_id'] ?? '') === (string) $laundry->id)>{{ $laundry->store_name }}</option>@endforeach</select></label>
                        <label>Status pesanan<select name="status"><option value="">Semua status</option>@foreach (['pending' => 'Menunggu', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)<option value="{{ $value }}" @selected(($transactionFilters['status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
                        <label>Status pembayaran<select name="payment_status"><option value="">Semua pembayaran</option>@foreach (['unpaid' => 'Belum dibayar', 'paid' => 'Lunas', 'refunded' => 'Dikembalikan'] as $value => $label)<option value="{{ $value }}" @selected(($transactionFilters['payment_status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
                        <button class="button button-dark" type="submit">Terapkan filter</button><a class="button button-light" href="{{ route('admin.transactions') }}">Reset</a>
                    </form>
                    <div class="table-wrap"><table><thead><tr><th>Laundry</th><th>Pelanggan</th><th>Layanan / detail</th><th>Jumlah</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Tanggal</th></tr></thead><tbody>@forelse ($transactions as $transaction)<tr><td class="cell-strong">{{ $transaction->store_name }}</td><td>{{ $transaction->customer_name }}</td><td>{{ $transaction->service_name }}<small class="cell-subline">{{ $transaction->order_details }}</small></td><td>{{ $transaction->quantity }}</td><td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td><td>{{ ucfirst($transaction->payment_status) }}</td><td><span class="badge badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></td><td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}</td></tr>@empty<tr><td class="empty-cell" colspan="8">Tidak ada transaksi yang sesuai dengan filter.</td></tr>@endforelse</tbody></table></div>
                    @if ($transactions->hasPages())<div class="pagination">{{ $transactions->links() }}</div>@endif
                </section>

            @elseif ($section === 'reviews')
                <section class="admin-review-list">@forelse ($reviews as $review)<article class="panel admin-review"><div class="admin-review-head"><div><h2>{{ $review->store_name }} <span>· {{ $review->customer_name }}</span></h2><small>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y, H:i') }}</small></div><span class="rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }} <b>{{ $review->rating }}/5</b></span></div><p>{{ $review->comment }}</p>@if ($review->owner_reply)<div class="admin-owner-reply"><small>Balasan owner</small><p>{{ $review->owner_reply }}</p></div>@endif<form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" data-admin-confirm="Hapus ulasan ini? Aksi ini tidak dapat dibatalkan.">@csrf @method('DELETE')<button class="icon-button danger-text" type="submit">Hapus ulasan</button></form></article>@empty<div class="panel empty-admin"><span>☆</span><h2>Belum ada ulasan</h2><p>Ulasan laundry akan muncul di sini.</p></div>@endforelse @if ($reviews->hasPages())<div class="pagination">{{ $reviews->links() }}</div>@endif</section>

            @elseif ($section === 'messages')
                <section class="panel"><div class="panel-heading"><div><h2>Pesan pelanggan dan komplain</h2><p>Baca percakapan lintas laundry dan balas sebagai Admin Washly.</p></div><span class="admin-total">{{ $threads->total() }} percakapan</span></div>
                    <div class="admin-thread-list">
                        @forelse ($threads as $thread)
                            <article class="admin-thread"><header class="admin-thread-heading"><div><strong>{{ $thread->customer_name }}</strong><small>{{ $thread->store_name }}{{ $thread->email ? ' · ' . $thread->email : '' }}</small></div><span class="badge">{{ $thread->message_count }} pesan</span></header>
                                <div class="admin-thread-messages">@foreach ($thread->messages as $message)<div class="admin-thread-message {{ $message->sender === 'admin' ? 'from-admin' : ($message->sender === 'owner' ? 'from-owner' : 'from-customer') }}"><div><strong>{{ $message->sender === 'admin' ? 'Admin Washly' : ($message->sender === 'owner' ? $thread->store_name : $thread->customer_name) }}</strong><small>{{ \Carbon\Carbon::parse($message->created_at)->format('d M, H:i') }}</small></div><p>{{ $message->message }}</p></div>@endforeach</div>
                                <form class="admin-reply-form" action="{{ route('admin.messages.reply') }}" method="POST">@csrf<input type="hidden" name="laundry_id" value="{{ $thread->laundry_id }}"><input type="hidden" name="customer_name" value="{{ $thread->customer_name }}">@if ($thread->user_id)<input type="hidden" name="user_id" value="{{ $thread->user_id }}">@endif<textarea name="message" rows="2" maxlength="2000" placeholder="Balas sebagai Admin Washly..." required></textarea><button class="button button-dark" type="submit">Kirim balasan</button></form>
                            </article>
                        @empty
                            <div class="empty-admin"><span>✉</span><h2>Belum ada pesan</h2><p>Pesan dan komplain pelanggan akan muncul di sini.</p></div>
                        @endforelse
                    </div>
                    @if ($threads->hasPages())<div class="pagination">{{ $threads->links() }}</div>@endif
                </section>

            @elseif ($section === 'schedules')
                <section class="panel"><div class="panel-heading"><div><h2>Jadwal operasional</h2><p>Tinjau jam buka yang diatur owner.</p></div></div><div class="table-wrap"><table><thead><tr><th>Laundry</th><th>Hari</th><th>Jam operasional</th><th>Status</th></tr></thead><tbody>@forelse ($schedules as $schedule)<tr><td class="cell-strong">{{ $schedule->store_name }}</td><td>{{ ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][$schedule->day_of_week] ?? '—' }}</td><td>{{ $schedule->is_closed ? '—' : substr($schedule->opens_at, 0, 5) . '–' . substr($schedule->closes_at, 0, 5) }}</td><td>{{ $schedule->is_closed ? 'Tutup' : 'Buka' }}</td></tr>@empty<tr><td class="empty-cell" colspan="4">Owner belum mengatur jadwal operasional.</td></tr>@endforelse</tbody></table></div>@if ($schedules->hasPages())<div class="pagination">{{ $schedules->links() }}</div>@endif</section>

            @elseif ($section === 'topups')
                <section class="panel"><div class="panel-heading"><div><h2>Permintaan top-up saldo</h2><p>Setujui setelah pembayaran diverifikasi. Persetujuan memperbarui saldo user.</p></div></div><div class="table-wrap"><table><thead><tr><th>Pengguna</th><th>Nominal</th><th>Metode / referensi</th><th>Status</th><th>Diajukan</th><th>Aksi</th></tr></thead><tbody>@forelse ($topups as $topup)<tr><td class="cell-strong">{{ $topup->name }}<small class="cell-subline">{{ $topup->email }}</small></td><td>Rp {{ number_format($topup->amount, 0, ',', '.') }}</td><td>{{ strtoupper($topup->payment_method) }}<small class="cell-subline">{{ $topup->payment_reference ?: '—' }}</small></td><td><span class="badge badge-{{ $topup->status === 'approved' ? 'paid' : ($topup->status === 'pending' ? 'pending' : 'cancelled') }}">{{ ucfirst($topup->status) }}</span></td><td>{{ \Carbon\Carbon::parse($topup->created_at)->format('d M Y, H:i') }}</td><td>@if ($topup->status === 'pending')<div class="application-actions"><form action="{{ route('admin.topups.approve', $topup->id) }}" method="POST" data-admin-confirm="Konfirmasi pembayaran dan tambahkan saldo user?">@csrf<button class="small-action action-approve" type="submit">Setujui</button></form><form action="{{ route('admin.topups.reject', $topup->id) }}" method="POST" data-admin-confirm="Tolak permintaan top-up ini?">@csrf<button class="small-action action-reject" type="submit">Tolak</button></form></div>@else—@endif</td></tr>@empty<tr><td class="empty-cell" colspan="6">Belum ada permintaan top-up.</td></tr>@endforelse</tbody></table></div>@if ($topups->hasPages())<div class="pagination">{{ $topups->links() }}</div>@endif</section>
            @elseif ($section === 'profile')
                <section class="admin-profile-layout">
                    <article class="panel admin-profile-card">
                        <div class="panel-heading"><div><h2>Foto profil</h2><p>Gunakan foto yang mudah dikenali oleh tim dan mitra.</p></div></div>
                        <div class="admin-profile-photo-area"><div class="admin-profile-avatar">@if($admin->profile_photo_path)<img src="{{ asset('storage/' . $admin->profile_photo_path) }}" alt="Foto profil {{ $admin->name }}">@else{{ strtoupper(substr($admin->name, 0, 1)) }}@endif</div><div><strong>{{ $admin->name }}</strong><small>{{ $admin->email }}</small></div></div>
                        <form class="admin-profile-form" action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data">@csrf<label>Unggah foto baru<input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required><small>JPG, PNG, atau WebP · maksimal 2 MB</small></label>@error('photo')<small class="profile-error">{{ $message }}</small>@enderror<button class="button button-dark" type="submit">Simpan foto</button></form>
                    </article>
                    <article class="panel admin-profile-card">
                        <div class="panel-heading"><div><h2>Informasi admin</h2><p>Perbarui data akun yang digunakan untuk masuk ke panel.</p></div></div>
                        <form class="admin-profile-form" action="{{ route('admin.profile.update') }}" method="POST">@csrf @method('PUT')
                            <label>Nama lengkap<input type="text" name="name" value="{{ old('name', $admin->name) }}" required maxlength="255">@error('name')<small class="profile-error">{{ $message }}</small>@enderror</label>
                            <label>Email<input type="email" name="email" value="{{ old('email', $admin->email) }}" required maxlength="255">@error('email')<small class="profile-error">{{ $message }}</small>@enderror</label>
                            <label>Nomor telepon<input type="tel" name="phone" value="{{ old('phone', $admin->phone) }}" maxlength="30">@error('phone')<small class="profile-error">{{ $message }}</small>@enderror</label>
                            <hr><h3>Ubah password <small>Opsional</small></h3>
                            <label>Password saat ini<input type="password" name="current_password" autocomplete="current-password">@error('current_password')<small class="profile-error">{{ $message }}</small>@enderror</label>
                            <div class="form-grid"><label>Password baru<input type="password" name="password" minlength="8" autocomplete="new-password">@error('password')<small class="profile-error">{{ $message }}</small>@enderror</label><label>Konfirmasi password baru<input type="password" name="password_confirmation" minlength="8" autocomplete="new-password"></label></div>
                            <button class="button button-dark" type="submit">Simpan perubahan</button>
                        </form>
                    </article>
                </section>
            @endif
        </div>
    </main>
</div>
<div class="sidebar-scrim" id="adminSidebarScrim"></div>
<script src="{{ asset('admin/admin.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>

</html>

