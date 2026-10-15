<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin · Laundryku</title>
    <link rel="stylesheet" href="{{ asset('owner/owner.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/admin.css') }}">
</head>

<body class="admin-body">
    @php
        $sections = [
            'dashboard' => ['Ringkasan', '⌂'],
            'applications' => ['Pengajuan owner', '✓'],
            'accounts' => ['Pengguna & owner', '♙'],
            'catalog' => ['Katalog layanan', '◈'],
            'transactions' => ['Transaksi', '↕'],
            'reviews' => ['Ulasan', '☆'],
            'messages' => ['Pesan pelanggan', '✉'],
            'schedules' => ['Jadwal laundry', '◷'],
            'topups' => ['Permintaan top-up', 'Rp'],
        ];
        $titles = [
            'dashboard' => 'Ringkasan platform',
            'applications' => 'Persetujuan owner',
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
            <a class="brand" href="{{ route('admin.dashboard') }}"><span
                    class="brand-mark">L</span><span><strong>Laundryku Admin</strong><small>PLATFORM
                        CONTROL</small></span></a>
            <div class="sidebar-label">PENGELOLAAN</div>
            <nav class="side-nav" aria-label="Navigasi admin">
                @foreach ($sections as $key => [$label, $icon])
                    <a href="{{ route('admin.' . $key) }}"
                        class="nav-link {{ $section === $key ? 'active' : '' }}"><span class="nav-icon"
                            aria-hidden="true">{{ $icon }}</span><span>{{ $label }}</span>
                        @if ($key === 'applications' && isset($stats['pending_owners']) && $stats['pending_owners'] > 0)
                            <span class="nav-count">{{ $stats['pending_owners'] }}</span>
                        @endif
                        @if ($key === 'topups' && isset($stats['pending_topups']) && $stats['pending_topups'] > 0)
                            <span class="nav-count">{{ $stats['pending_topups'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
            <div class="sidebar-bottom">
                <div class="owner-mini"><span
                        class="avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</span><span><strong>{{ $admin->name }}</strong><small>Administrator</small></span>
                </div>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="logout-button"
                        type="submit"><span>↪</span> Keluar</button></form>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar"><button class="menu-toggle" id="adminMenuToggle" type="button"
                    aria-label="Buka menu">☰</button>
                <div>
                    <p class="eyebrow">ADMINISTRATOR</p>
                    <h1>{{ $titles[$section] ?? 'Panel admin' }}</h1>
                </div>
                <div class="topbar-right"><span class="admin-mark">A</span><span>Kontrol platform</span></div>
            </header>
            <div class="page-content">
                @if (session('success'))
                    <div class="notice notice-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="notice notice-error" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($section === 'dashboard')
                    <section class="admin-welcome">
                        <div>
                            <p class="eyebrow">PUSAT KENDALI</p>
                            <h2>Selamat datang, {{ $admin->name }}</h2>
                            <p>Pantau akun dan aktivitas laundry dalam satu tempat.</p>
                        </div><a href="{{ route('admin.applications') }}" class="button button-dark">Tinjau pengajuan
                            <span>→</span></a>
                    </section>
                    <section class="stats-grid admin-stats">
                        <article class="stat-card"><span class="stat-icon icon-orange">✓</span><span
                                class="stat-label">Owner
                                menunggu</span><strong>{{ $stats['pending_owners'] }}</strong><small>Permohonan perlu
                                ditinjau</small></article>
                        <article class="stat-card"><span class="stat-icon icon-green">◈</span><span
                                class="stat-label">Owner
                                aktif</span><strong>{{ $stats['owners'] }}</strong><small>Laundry terdaftar</small>
                        </article>
                        <article class="stat-card"><span class="stat-icon icon-blue">♙</span><span
                                class="stat-label">Pengguna</span><strong>{{ $stats['customers'] }}</strong><small>Akun
                                pelanggan</small></article>
                        <article class="stat-card"><span class="stat-icon icon-pink">↕</span><span
                                class="stat-label">Transaksi</span><strong>{{ $stats['transactions'] }}</strong><small>Semua
                                pesanan masuk</small></article>
                    </section>
                    <section class="admin-dashboard-grid">
                        <section class="panel">
                            <div class="panel-heading">
                                <div>
                                    <h2>Pengajuan owner terbaru</h2>
                                    <p>Periksa informasi dan setujui akun laundry.</p>
                                </div><a class="text-link" href="{{ route('admin.applications') }}">Semua pengajuan
                                    →</a>
                            </div>
                            <div class="application-stack">
                                @forelse ($applications as $application)
                                    <article class="application-row">
                                        <div class="application-person"><span
                                                class="avatar avatar-small">{{ strtoupper(substr($application->name, 0, 1)) }}</span><span><strong>{{ $application->name }}</strong><small>{{ $application->email }}
                                                    · {{ $application->phone }}</small></span></div>
                                        <div class="application-actions">
                                            <form action="{{ route('admin.applications.approve', $application->id) }}"
                                                method="POST">@csrf<button class="small-action action-approve"
                                                    type="submit">Setujui</button></form>
                                            <form action="{{ route('admin.applications.reject', $application->id) }}"
                                                method="POST" data-admin-confirm="Tolak permohonan owner ini?">
                                                @csrf<button class="small-action action-reject"
                                                    type="submit">Tolak</button></form>
                                        </div>
                                </article>@empty<p class="empty-copy">Tidak ada permohonan yang menunggu.</p>
                                @endforelse
                            </div>
                        </section>
                        <section class="panel admin-quick-panel">
                            <div class="panel-heading">
                                <div>
                                    <h2>Perlu perhatian</h2>
                                    <p>Item yang menunggu tindakan admin.</p>
                                </div>
                            </div><a href="{{ route('admin.topups') }}"><span
                                    class="quick-icon">Rp</span><span><strong>{{ $stats['pending_topups'] }} permintaan
                                        top-up</strong><small>Konfirmasi untuk memperbarui
                                        saldo</small></span><b>→</b></a><a href="{{ route('admin.reviews') }}"><span
                                    class="quick-icon quick-review">☆</span><span><strong>{{ $stats['reviews'] }}
                                        ulasan tercatat</strong><small>Tinjau konten yang
                                        dilaporkan</small></span><b>→</b></a>
                        </section>
                    </section>
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Aktivitas transaksi</h2>
                                <p>Pesanan terbaru dari seluruh laundry.</p>
                            </div><a class="text-link" href="{{ route('admin.transactions') }}">Pantau semua →</a>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Laundry</th>
                                        <th>Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestTransactions as $transaction)
                                        <tr>
                                            <td class="cell-strong">{{ $transaction->store_name }}</td>
                                            <td>{{ $transaction->customer_name }}</td>
                                            <td>{{ $transaction->service_name }}</td>
                                            <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                            <td><span
                                                    class="badge badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
                                            </td>
                                    </tr>@empty<tr>
                                            <td colspan="6" class="empty-cell">Belum ada transaksi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                @elseif ($section === 'applications')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Permohonan akun owner</h2>
                                <p>Owner baru dapat login setelah disetujui admin.</p>
                            </div><span class="admin-total">{{ $applications->total() }} menunggu</span>
                        </div>
                        <div class="application-list">
                            @forelse ($applications as $application)
                                <article class="owner-application">
                                    <div class="application-header"><span
                                            class="avatar">{{ strtoupper(substr($application->name, 0, 1)) }}</span>
                                        <div>
                                            <h2>{{ $application->name }}</h2>
                                            <p>{{ $application->email }} · {{ $application->phone }}</p>
                                        </div><span class="status-pill status-pending">Menunggu</span>
                                    </div>
                                    <div class="application-info"><span><small>NAMA
                                                USAHA</small><strong>{{ $application->store_name }}</strong></span><span><small>ALAMAT
                                                USAHA</small><strong>{{ $application->business_address }}</strong></span><span><small>TELEPON
                                                USAHA</small><strong>{{ $application->store_phone }}</strong></span><span><small>TANGGAL
                                                AJUKAN</small><strong>{{ \Carbon\Carbon::parse($application->created_at)->format('d M Y, H:i') }}</strong></span>
                                    </div>
                                    <div class="application-footer">
                                        <p>{{ $application->description ?: 'Tidak ada deskripsi usaha.' }}</p>
                                        <div class="application-actions">
                                            <form action="{{ route('admin.applications.approve', $application->id) }}"
                                                method="POST">@csrf<button class="button button-dark"
                                                    type="submit">Setujui owner</button></form>
                                            <form action="{{ route('admin.applications.reject', $application->id) }}"
                                                method="POST"
                                                data-admin-confirm="Tolak dan nonaktifkan permohonan owner ini?">
                                                @csrf<button class="button button-outline-danger" type="submit">Tolak
                                                    permohonan</button></form>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="empty-admin"><span>✓</span>
                                    <h2>Semua pengajuan sudah ditinjau</h2>
                                    <p>Permohonan owner baru akan muncul di sini.</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($applications->hasPages())
                            <div class="pagination">{{ $applications->links() }}</div>
                        @endif
                    </section>
                @elseif ($section === 'accounts')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Daftar akun</h2>
                                <p>Edit data, ubah peran/status, atau hapus user dan owner.</p>
                            </div>
                        </div>
                        <form class="admin-filters" action="{{ route('admin.accounts') }}" method="GET"><input
                                name="q" value="{{ $search }}"
                                placeholder="Cari nama, email, telepon..."><select name="role">
                                <option value="">Semua peran</option>
                                <option value="user" @selected($roleFilter === 'user')>Pengguna</option>
                                <option value="owner" @selected($roleFilter === 'owner')>Owner</option>
                            </select><button class="button button-dark" type="submit">Cari</button></form>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email / telepon</th>
                                        <th>Peran</th>
                                        <th>Status</th>
                                        <th>Terdaftar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($accounts as $account)
                                        <tr>
                                            <td class="cell-strong">{{ $account->name }}</td>
                                            <td>{{ $account->email }}<small
                                                    class="cell-subline">{{ $account->phone ?: '—' }}</small></td>
                                            <td><span
                                                    class="badge {{ $account->role === 'owner' ? 'badge-processing' : 'badge-completed' }}">{{ ucfirst($account->role) }}</span>
                                            </td>
                                            <td><span
                                                    class="badge {{ $account->status === 'active' ? 'badge-paid' : ($account->status === 'pending' ? 'badge-pending' : 'badge-cancelled') }}">{{ ucfirst($account->status) }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($account->created_at)->format('d M Y') }}</td>
                                            <td class="actions"><button class="icon-button edit-account"
                                                    type="button" data-name="{{ $account->name }}"
                                                    data-email="{{ $account->email }}"
                                                    data-phone="{{ $account->phone }}"
                                                    data-address="{{ $account->address }}"
                                                    data-role="{{ $account->role }}"
                                                    data-status="{{ $account->status }}"
                                                    data-update-url="{{ route('admin.accounts.update', $account->id) }}">Edit</button>
                                                <form class="inline-form"
                                                    action="{{ route('admin.accounts.delete', $account->id) }}"
                                                    method="POST"
                                                    data-admin-confirm="Hapus akun {{ $account->name }} dan data laundrianya?">
                                                    @csrf @method('DELETE')<button class="icon-button danger-text"
                                                        type="submit">Hapus</button></form>
                                            </td>
                                        </tr>
                                    @empty<tr>
                                            <td class="empty-cell" colspan="6">Tidak ada akun yang cocok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($accounts->hasPages())
                            <div class="pagination">{{ $accounts->links() }}</div>
                        @endif
                    </section>
                    <dialog class="form-dialog" id="accountDialog">
                        <form id="accountForm" method="POST">@csrf @method('PUT')<div class="dialog-heading">
                                <div>
                                    <p class="eyebrow">MANAJEMEN AKUN</p>
                                    <h2>Edit akun</h2>
                                </div><button type="button" class="close-dialog" data-close-account
                                    aria-label="Tutup">×</button>
                            </div><label>Nama<input name="name" id="accountName" required
                                    maxlength="255"></label><label>Email<input name="email" id="accountEmail"
                                    type="email" required></label><label>Telepon<input name="phone"
                                    id="accountPhone" maxlength="30"></label><label>Alamat
                                <textarea name="address" id="accountAddress" rows="2"></textarea>
                            </label>
                            <div class="form-grid"><label>Peran<select name="role" id="accountRole">
                                        <option value="user">Pengguna</option>
                                        <option value="owner">Owner</option>
                                    </select></label><label>Status<select name="status" id="accountStatus">
                                        <option value="pending">Menunggu</option>
                                        <option value="active">Aktif</option>
                                        <option value="suspended">Ditangguhkan</option>
                                    </select></label></div>
                            <div class="dialog-actions"><button type="button" class="button button-light"
                                    data-close-account>Batal</button><button class="button button-dark"
                                    type="submit">Simpan perubahan</button></div>
                        </form>
                    </dialog>
                @elseif ($section === 'catalog')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Semua layanan owner</h2>
                                <p>Tinjau harga, promo, dan status layanan. Layanan bermasalah dapat dihapus.</p>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Laundry</th>
                                        <th>Layanan</th>
                                        <th>Harga</th>
                                        <th>Promo</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($services as $service)
                                        <tr>
                                            <td class="cell-strong">{{ $service->store_name }}</td>
                                            <td>{{ $service->name }}<small
                                                    class="cell-subline">{{ $service->description }}</small></td>
                                            <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                            <td>{{ $service->original_price ? ($service->promo_label ?: 'Promo') . ' · Rp ' . number_format($service->original_price, 0, ',', '.') : '—' }}
                                            </td>
                                            <td>{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                            <td>
                                                <form action="{{ route('admin.catalog.delete', $service->id) }}"
                                                    method="POST"
                                                    data-admin-confirm="Hapus layanan ini dari katalog?">@csrf
                                                    @method('DELETE')<button class="icon-button danger-text"
                                                        type="submit">Hapus</button></form>
                                            </td>
                                    </tr>@empty<tr>
                                            <td class="empty-cell" colspan="6">Belum ada layanan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($services->hasPages())
                            <div class="pagination">{{ $services->links() }}</div>
                        @endif
                    </section>
                @elseif ($section === 'transactions')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Semua transaksi pelanggan</h2>
                                <p>Pemantauan pesanan dari setiap laundry.</p>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Laundry</th>
                                        <th>Pelanggan</th>
                                        <th>Layanan / detail</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Pembayaran</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $transaction)
                                        <tr>
                                            <td class="cell-strong">{{ $transaction->store_name }}</td>
                                            <td>{{ $transaction->customer_name }}</td>
                                            <td>{{ $transaction->service_name }}<small
                                                    class="cell-subline">{{ $transaction->order_details }}</small>
                                            </td>
                                            <td>{{ $transaction->quantity }}</td>
                                            <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                            <td>{{ ucfirst($transaction->payment_status) }}</td>
                                            <td><span
                                                    class="badge badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
                                            </td>
                                    </tr>@empty<tr>
                                            <td class="empty-cell" colspan="8">Belum ada transaksi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($transactions->hasPages())
                            <div class="pagination">{{ $transactions->links() }}</div>
                        @endif
                    </section>
                @elseif ($section === 'reviews')
                    <section class="admin-review-list">
                        @forelse ($reviews as $review)
                            <article class="panel admin-review">
                                <div class="admin-review-head">
                                    <div>
                                        <h2>{{ $review->store_name }} <span>· {{ $review->customer_name }}</span></h2>
                                        <small>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y, H:i') }}</small>
                                    </div><span
                                        class="rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                        <b>{{ $review->rating }}/5</b></span>
                                </div>
                                <p>{{ $review->comment }}</p>
                                @if ($review->owner_reply)
                                    <div class="admin-owner-reply"><small>Balasan owner</small>
                                        <p>{{ $review->owner_reply }}</p>
                                    </div>
                                @endif
                                <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST"
                                    data-admin-confirm="Hapus ulasan ini? Aksi ini tidak dapat dibatalkan.">
                                    @csrf @method('DELETE')<button class="icon-button danger-text"
                                        type="submit">Hapus ulasan</button></form>
                        </article>@empty<div class="panel empty-admin"><span>☆</span>
                                <h2>Belum ada ulasan</h2>
                                <p>Ulasan laundry akan muncul di sini.</p>
                            </div>
                            @endforelse @if ($reviews->hasPages())
                                <div class="pagination">{{ $reviews->links() }}</div>
                            @endif
                    </section>
                @elseif ($section === 'messages')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Pesan lintas laundry</h2>
                                <p>Tinjau percakapan pelanggan dan respon dari owner.</p>
                            </div>
                        </div>
                        <div class="admin-message-list">
                            @forelse ($messages as $message)
                                <article class="admin-message">
                                    <div class="admin-message-head">
                                        <div><strong>{{ $message->store_name }}</strong><small>{{ $message->customer_name }}
                                                ·
                                                {{ \Carbon\Carbon::parse($message->created_at)->format('d M Y, H:i') }}</small>
                                        </div><span
                                            class="badge {{ $message->sender === 'owner' ? 'badge-processing' : 'badge-completed' }}">{{ $message->sender === 'owner' ? 'Balasan owner' : 'Pelanggan' }}</span>
                                    </div>
                                    <p>{{ $message->message }}</p>
                            </article>@empty<p class="empty-cell">Belum ada pesan.</p>
                            @endforelse
                        </div>
                        @if ($messages->hasPages())
                            <div class="pagination">{{ $messages->links() }}</div>
                        @endif
                    </section>
                @elseif ($section === 'schedules')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Jadwal operasional</h2>
                                <p>Tinjau jam buka yang diatur owner.</p>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Laundry</th>
                                        <th>Hari</th>
                                        <th>Jam operasional</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($schedules as $schedule)
                                        <tr>
                                            <td class="cell-strong">{{ $schedule->store_name }}</td>
                                            <td>{{ ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$schedule->day_of_week] ?? '—' }}
                                            </td>
                                            <td>{{ $schedule->is_closed ? '—' : substr($schedule->opens_at, 0, 5) . '–' . substr($schedule->closes_at, 0, 5) }}
                                            </td>
                                            <td>{{ $schedule->is_closed ? 'Tutup' : 'Buka' }}</td>
                                    </tr>@empty<tr>
                                            <td class="empty-cell" colspan="4">Owner belum mengatur jadwal
                                                operasional.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($schedules->hasPages())
                            <div class="pagination">{{ $schedules->links() }}</div>
                        @endif
                    </section>
                @elseif ($section === 'topups')
                    <section class="panel">
                        <div class="panel-heading">
                            <div>
                                <h2>Permintaan top-up saldo</h2>
                                <p>Setujui setelah pembayaran diverifikasi. Persetujuan memperbarui saldo user.</p>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>Nominal</th>
                                        <th>Metode / referensi</th>
                                        <th>Status</th>
                                        <th>Diajukan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topups as $topup)
                                        <tr>
                                            <td class="cell-strong">{{ $topup->name }}<small
                                                    class="cell-subline">{{ $topup->email }}</small></td>
                                            <td>Rp {{ number_format($topup->amount, 0, ',', '.') }}</td>
                                            <td>{{ strtoupper($topup->payment_method) }}<small
                                                    class="cell-subline">{{ $topup->payment_reference ?: '—' }}</small>
                                            </td>
                                            <td><span
                                                    class="badge badge-{{ $topup->status === 'approved' ? 'paid' : ($topup->status === 'pending' ? 'pending' : 'cancelled') }}">{{ ucfirst($topup->status) }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($topup->created_at)->format('d M Y, H:i') }}
                                            </td>
                                            <td>
                                                @if ($topup->status === 'pending')
                                                    <div class="application-actions">
                                                        <form
                                                            action="{{ route('admin.topups.approve', $topup->id) }}"
                                                            method="POST"
                                                            data-admin-confirm="Konfirmasi pembayaran dan tambahkan saldo user?">
                                                            @csrf<button class="small-action action-approve"
                                                                type="submit">Setujui</button></form>
                                                        <form action="{{ route('admin.topups.reject', $topup->id) }}"
                                                            method="POST"
                                                            data-admin-confirm="Tolak permintaan top-up ini?">
                                                            @csrf<button class="small-action action-reject"
                                                                type="submit">Tolak</button></form>
                                                </div>@else—
                                                @endif
                                            </td>
                                    </tr>@empty<tr>
                                            <td class="empty-cell" colspan="6">Belum ada permintaan top-up.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($topups->hasPages())
                            <div class="pagination">{{ $topups->links() }}</div>
                        @endif
                    </section>
                @endif
            </div>
        </main>
    </div>
    <div class="sidebar-scrim" id="adminSidebarScrim"></div>
    <script src="{{ asset('admin/admin.js') }}" defer></script>
</body>

</html>
