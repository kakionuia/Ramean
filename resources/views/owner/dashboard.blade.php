<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ucfirst($section) }} · {{ $laundry->store_name }}</title>
    <link rel="stylesheet" href="{{ asset('owner/owner.css') }}">
</head>
<body>
@php
    $sections = [
        'dashboard' => ['Dashboard', '⌂'],
        'transactions' => ['Riwayat transaksi', '↕'],
        'services' => ['Produk & jasa', '◈'],
        'finance' => ['Keuangan', 'Rp'],
        'reviews' => ['Ulasan pelanggan', '☆'],
        'chat' => ['Chat pelanggan', '✉'],
        'settings' => ['Pengaturan', '⚙'],
    ];
    $titles = [
        'dashboard' => 'Dashboard',
        'transactions' => 'Riwayat transaksi pelanggan',
        'transaction-detail' => 'Detail pesanan pelanggan',
        'services' => 'Produk dan jasa',
        'finance' => 'Keuangan laundry',
        'reviews' => 'Ulasan pelanggan',
        'chat' => 'Pesan pelanggan',
        'settings' => 'Pengaturan laundry',
    ];
@endphp

<div class="owner-shell">
    <aside class="sidebar" id="ownerSidebar">
        <a class="brand" href="{{ route('owner.dashboard') }}">
            <span class="brand-mark">L</span>
            <span><strong>{{ $laundry->store_name }}</strong><small>OWNER WORKSPACE</small></span>
        </a>
        <div class="sidebar-label">MENU UTAMA</div>
        <nav class="side-nav" aria-label="Navigasi owner">
            @foreach ($sections as $key => [$label, $icon])
                <a href="{{ route('owner.' . $key) }}" class="nav-link {{ $section === $key || ($section === 'transaction-detail' && $key === 'transactions') ? 'active' : '' }}">
                    <span class="nav-icon" aria-hidden="true">{{ $icon }}</span>
                    <span>{{ $label }}</span>
                    @if ($key === 'reviews' && isset($stats['unanswered_reviews']) && $stats['unanswered_reviews'] > 0)
                        <span class="nav-count">{{ $stats['unanswered_reviews'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
        <div class="sidebar-bottom">
            <div class="owner-mini">
                <span class="avatar">{{ strtoupper(substr($owner->name, 0, 1)) }}</span>
                <span><strong>{{ $owner->name }}</strong><small>Pemilik laundry</small></span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout-button" type="submit"><span aria-hidden="true">↪</span> Keluar</button>
            </form>
        </div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <button class="menu-toggle" type="button" id="menuToggle" aria-label="Buka menu">☰</button>
            <div>
                <p class="eyebrow">WORKSPACE OWNER</p>
                <h1>{{ $titles[$section] ?? 'Dashboard' }}</h1>
            </div>
            <div class="topbar-right">
                <span class="status-dot"></span>
                <span>{{ $laundry->store_name }}</span>
            </div>
        </header>

        <div class="page-content">
            @if (session('success'))
                <div class="notice notice-success" role="status">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="notice notice-error" role="alert">
                    <strong>Periksa kembali data:</strong>
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @if ($section === 'dashboard')
                <section class="welcome-row">
                    <div><p class="eyebrow">RINGKASAN HARI INI</p><h2>Halo, {{ $owner->name }}</h2><p>Ini ringkasan operasional {{ $laundry->store_name }}.</p></div>
                    <a class="button button-dark" href="{{ route('owner.services') }}">Kelola layanan <span>→</span></a>
                </section>
                <section class="stats-grid">
                    <article class="stat-card"><span class="stat-icon icon-green">◈</span><span class="stat-label">Layanan aktif</span><strong>{{ $stats['services'] }}</strong><small>Produk dan jasa tersedia</small></article>
                    <article class="stat-card"><span class="stat-icon icon-orange">↕</span><span class="stat-label">Pesanan berjalan</span><strong>{{ $stats['open_transactions'] }}</strong><small>Menunggu atau diproses</small></article>
                    <article class="stat-card"><span class="stat-icon icon-blue">Rp</span><span class="stat-label">Pemasukan bulan ini</span><strong>Rp {{ number_format($stats['income'], 0, ',', '.') }}</strong><small>Dari transaksi lunas</small></article>
                    <article class="stat-card"><span class="stat-icon icon-pink">☆</span><span class="stat-label">Ulasan perlu dibalas</span><strong>{{ $stats['unanswered_reviews'] }}</strong><small>Masukan pelanggan</small></article>
                </section>
                <section class="panel">
                    <div class="panel-heading"><div><h2>Transaksi terbaru</h2><p>Aktivitas pesanan pelanggan terakhir.</p></div><a class="text-link" href="{{ route('owner.transactions') }}">Lihat semua →</a></div>
                    <div class="table-wrap"><table><thead><tr><th>Pelanggan</th><th>Layanan</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>
                    @forelse ($transactions as $transaction)
                        <tr><td class="cell-strong">{{ $transaction->customer_name }}</td><td>{{ $transaction->service_name }}</td><td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td><td><span class="badge badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></td><td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}</td><td><a class="text-link" href="{{ route('owner.transactions.show', $transaction->id) }}">Detail →</a></td></tr>
                    @empty
                        <tr><td colspan="6" class="empty-cell">Belum ada transaksi. Pesanan pelanggan akan tampil di sini.</td></tr>
                    @endforelse
                    </tbody></table></div>
                </section>

            @elseif ($section === 'transactions')
                <section class="panel">
                    <div class="panel-heading"><div><h2>Semua transaksi</h2><p>Riwayat pesanan untuk laundry Anda.</p></div></div>
                    <div class="table-wrap"><table><thead><tr><th>Pelanggan</th><th>Layanan</th><th>Penyerahan / berat</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead><tbody>
                    @forelse ($transactions as $transaction)
                        <tr><td class="cell-strong">{{ $transaction->customer_name }}</td><td>{{ $transaction->service_name }}</td><td>{{ $transaction->delivery_method === 'pickup' ? 'Jemput' : 'Drop-off' }} · {{ $transaction->weight_kg !== null ? number_format($transaction->weight_kg, 2, ',', '.') . ' kg' : 'Belum timbang' }}</td><td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td><td><span class="badge badge-{{ $transaction->payment_status }}">{{ ucfirst($transaction->payment_status) }}</span></td><td><span class="badge badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></td><td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}</td><td><a class="text-link" href="{{ route('owner.transactions.show', $transaction->id) }}">Detail →</a></td></tr>
                    @empty
                        <tr><td colspan="8" class="empty-cell">Belum ada riwayat transaksi pelanggan.</td></tr>
                    @endforelse
                    </tbody></table></div>
                    @if ($transactions->hasPages())<div class="pagination">{{ $transactions->links() }}</div>@endif
                </section>

            @elseif ($section === 'transaction-detail')
                @php
                    $processLabels = ['pending' => 'Menunggu laundry', 'processing' => 'Sedang dicuci', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                    $progressIndex = array_search($order->status, ['pending', 'processing', 'completed'], true);
                @endphp
                <a class="text-link back-to-list" href="{{ route('owner.transactions') }}">← Kembali ke transaksi</a>
                <div class="transaction-detail-grid">
                    <section class="panel transaction-detail-panel">
                        <div class="panel-heading"><div><p class="eyebrow">PESANAN #{{ $order->id }}</p><h2>{{ $order->customer_name }}</h2><p>{{ $order->customer_email ?: 'Email tidak tersedia' }} · {{ $order->customer_phone ?: 'Telepon tidak tersedia' }}</p></div><span class="badge badge-{{ $order->status }}">{{ $processLabels[$order->status] ?? ucfirst($order->status) }}</span></div>
                        <div class="owner-order-progress">
                            @foreach (['pending' => 'Pesanan diterima', 'processing' => 'Sedang dicuci', 'completed' => 'Selesai'] as $step => $label)
                                @php $stepIndex = array_search($step, ['pending', 'processing', 'completed'], true); @endphp
                                <div class="owner-progress-step {{ $order->status !== 'cancelled' && $stepIndex <= ($progressIndex === false ? -1 : $progressIndex) ? 'is-done' : '' }}"><span>{{ $stepIndex + 1 }}</span><strong>{{ $label }}</strong></div>
                            @endforeach
                        </div>
                        <div class="owner-order-details"><div><small>PAKET LAYANAN</small><strong>{{ $order->service_name }}</strong></div><div><small>RINCIAN</small><strong>{{ $order->order_details ?: 'Tidak ada catatan.' }}</strong></div><div><small>TARIF</small><strong>Rp {{ number_format($order->service_rate, 0, ',', '.') }} / kg</strong></div><div><small>PENYERAHAN</small><strong>{{ $order->delivery_method === 'pickup' ? 'Antar-jemput' : 'Drop-off' }} · {{ $order->delivery_status === 'received' ? 'Diterima laundry' : ($order->delivery_status === 'picked_up' ? 'Sudah dijemput' : 'Menunggu') }}</strong></div><div><small>WAKTU PESAN</small><strong>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</strong></div></div>
                        @if (!$order->weighed_at && $order->status === 'pending')
                            <div class="delivery-action-panel"><div><p class="eyebrow">PENERIMAAN CUCIAN</p><strong>{{ $order->delivery_method === 'pickup' ? 'Pickup fee Rp ' . number_format($order->pickup_fee, 0, ',', '.') : 'Drop-off langsung ke laundry' }}</strong></div>
                                @if ($order->delivery_method === 'pickup' && $order->delivery_status === 'waiting')<form action="{{ route('owner.transactions.delivery-status', $order->id) }}" method="POST">@csrf<input type="hidden" name="delivery_status" value="picked_up"><button class="button button-light" type="submit">Tandai sudah dijemput</button></form>@endif
                                @if (($order->delivery_method === 'dropoff' && $order->delivery_status === 'waiting') || ($order->delivery_method === 'pickup' && $order->delivery_status === 'picked_up'))<form action="{{ route('owner.transactions.delivery-status', $order->id) }}" method="POST">@csrf<input type="hidden" name="delivery_status" value="received"><button class="button button-dark" type="submit">Konfirmasi cucian diterima</button></form>@endif
                            </div>
                            @if ($order->delivery_status === 'received')<form class="weigh-form" action="{{ route('owner.transactions.weigh', $order->id) }}" method="POST" data-weigh-form data-rate="{{ $order->service_rate }}" data-pickup-fee="{{ $order->pickup_fee }}">@csrf<label>Berat aktual cucian (kg)<input name="weight_kg" type="number" min="0.01" max="1000" step="0.01" placeholder="Misal 3,5 kg" required></label><div class="weigh-math"><span>Tarif Rp {{ number_format($order->service_rate, 0, ',', '.') }}/kg + pickup Rp {{ number_format($order->pickup_fee, 0, ',', '.') }}</span><strong>Tagihan sementara <b data-weigh-total>Masukkan berat</b></strong></div><button class="button button-dark" type="submit">Simpan berat & buat tagihan</button></form>@endif
                        @elseif ($order->weighed_at)
                            <div class="final-weight-invoice"><p class="eyebrow">HASIL TIMBANG</p><strong>{{ number_format($order->weight_kg, 2, ',', '.') }} kg</strong><div><span>{{ number_format($order->weight_kg, 2, ',', '.') }} kg × Rp {{ number_format($order->service_rate, 0, ',', '.') }}/kg</span><b>Rp {{ number_format($order->weight_kg * $order->service_rate, 0, ',', '.') }}</b></div><div><span>Biaya antar-jemput</span><b>Rp {{ number_format($order->pickup_fee, 0, ',', '.') }}</b></div><div class="final-total"><span>Total tagihan</span><b>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</b></div><small>{{ \Carbon\Carbon::parse($order->weighed_at)->format('d M Y, H:i') }}</small></div>
                        @endif
                    </section>
                    <aside class="panel owner-payment-status"><p class="eyebrow">STATUS PEMBAYARAN</p><h2>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h2><span class="badge badge-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }} · {{ strtoupper($order->payment_method ?: '—') }}</span>
                        @if (!$order->weighed_at)<p class="unpaid-owner-note">Tagihan belum dibuat. Terima dan timbang cucian untuk menghitung biaya final.</p>@elseif ($order->payment_status !== 'paid')<p class="unpaid-owner-note">Menunggu user membayar tagihan hasil timbang.</p>@else<p class="paid-owner-note">Pembayaran diterima{{ $order->paid_at ? ' · ' . \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') : '' }}</p>@endif
                        @if ($order->weighed_at && $order->status === 'pending' && $order->payment_status === 'paid')<form action="{{ route('owner.transactions.status', $order->id) }}" method="POST">@csrf<input type="hidden" name="status" value="processing"><button class="button button-dark status-action" type="submit">Mulai cucian</button></form>@elseif ($order->weighed_at && $order->status === 'processing' && $order->payment_status === 'paid')<form action="{{ route('owner.transactions.status', $order->id) }}" method="POST">@csrf<input type="hidden" name="status" value="completed"><button class="button button-dark status-action" type="submit">Tandai selesai</button></form>@endif
                        @if ($order->status === 'completed' && $order->payment_status === 'paid' && $order->delivery_status === 'received')<form action="{{ route('owner.transactions.delivery-status', $order->id) }}" method="POST" class="return-garment-form">@csrf<input type="hidden" name="delivery_status" value="returned"><button class="button button-dark status-action" type="submit">Tandai sudah dikembalikan / siap diambil</button></form>@elseif ($order->delivery_status === 'returned')<p class="paid-owner-note">Baju sudah dikembalikan / siap diambil pelanggan.</p>@endif
                    </aside>
                </div>

            @elseif ($section === 'services')
                <section class="panel">
                    <div class="panel-heading"><div><h2>Daftar layanan</h2><p>Atur nama layanan, harga, dan estimasi waktu pengerjaan.</p></div><button class="button button-dark" type="button" id="addServiceButton">＋ Tambah layanan</button></div>
                    <div class="table-wrap"><table><thead><tr><th>Nama layanan</th><th>Deskripsi</th><th>Durasi</th><th>Harga / promo</th><th>Status</th><th></th></tr></thead><tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td class="cell-strong">{{ $service->name }}</td><td>{{ $service->description ?: '—' }}</td><td>{{ $service->duration_hours }} jam</td><td>Rp {{ number_format($service->price, 0, ',', '.') }} @if ($service->original_price && $service->original_price > $service->price)<span class="badge badge-pending">{{ $service->promo_label ?: 'Promo' }}</span>@endif</td><td><span class="badge {{ $service->is_active ? 'badge-paid' : 'badge-cancelled' }}">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="actions"><button class="icon-button edit-service" type="button" aria-label="Edit layanan" data-id="{{ $service->id }}" data-name="{{ $service->name }}" data-description="{{ $service->description }}" data-price="{{ $service->price }}" data-duration="{{ $service->duration_hours }}" data-active="{{ $service->is_active ? '1' : '0' }}" data-promo-label="{{ $service->promo_label }}" data-original-price="{{ $service->original_price }}" data-promo-until="{{ $service->promo_until }}" data-update-url="{{ route('owner.services.update', $service->id) }}">Edit</button>
                                <form action="{{ route('owner.services.delete', $service->id) }}" method="POST" class="inline-form delete-confirm">@csrf @method('DELETE')<button class="icon-button danger-text" type="submit">Hapus</button></form></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-cell">Belum ada produk atau jasa. Tambahkan layanan pertama Anda.</td></tr>
                    @endforelse
                    </tbody></table></div>
                </section>
                <dialog class="form-dialog" id="serviceDialog">
                    <form id="serviceForm" action="{{ route('owner.services.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="serviceMethod" value="POST">
                        <div class="dialog-heading"><div><p class="eyebrow">KATALOG</p><h2 id="serviceDialogTitle">Tambah layanan</h2></div><button type="button" class="close-dialog" data-close-dialog aria-label="Tutup">×</button></div>
                        <label>Nama layanan<input name="name" id="serviceName" required maxlength="120" placeholder="Contoh: Cuci kering kiloan"></label>
                        <label>Deskripsi<textarea name="description" id="serviceDescription" rows="3" placeholder="Ringkasan layanan"></textarea></label>
                        <div class="form-grid"><label>Harga (Rp)<input name="price" id="servicePrice" type="number" min="0" step="500" required></label><label>Durasi (jam)<input name="duration_hours" id="serviceDuration" type="number" min="1" max="720" value="24" required></label></div>
                        <div class="form-grid"><label>Harga sebelum promo (opsional)<input name="original_price" id="serviceOriginalPrice" type="number" min="0" step="500" placeholder="Isi jika sedang promo"></label><label>Label promo<input name="promo_label" id="servicePromoLabel" maxlength="50" placeholder="Contoh: Hemat 20%"></label></div>
                        <label>Berlaku sampai (opsional)<input name="promo_until" id="servicePromoUntil" type="date"></label>
                        <label class="check-label"><input name="is_active" id="serviceActive" type="checkbox" value="1" checked> Tampilkan sebagai layanan aktif</label>
                        <div class="dialog-actions"><button type="button" class="button button-light" data-close-dialog>Batal</button><button class="button button-dark" type="submit">Simpan layanan</button></div>
                    </form>
                </dialog>

            @elseif ($section === 'finance')
                <section class="stats-grid finance-stats"><article class="stat-card"><span class="stat-label">Pemasukan terbayar</span><strong>Rp {{ number_format($income, 0, ',', '.') }}</strong><small>Total transaksi lunas</small></article><article class="stat-card"><span class="stat-label">Pengeluaran tercatat</span><strong>Rp {{ number_format($expensesTotal, 0, ',', '.') }}</strong><small>Biaya operasional</small></article><article class="stat-card"><span class="stat-label">Selisih kas</span><strong>Rp {{ number_format($balance, 0, ',', '.') }}</strong><small>Pemasukan dikurangi pengeluaran</small></article></section>
                <div class="split-grid">
                    <section class="panel"><div class="panel-heading"><div><h2>Catat pengeluaran</h2><p>Simpan biaya operasional laundry.</p></div></div>
                        <form class="stack-form" action="{{ route('owner.expenses.store') }}" method="POST">@csrf
                            <label>Keperluan<input name="description" required maxlength="160" placeholder="Contoh: deterjen"></label>
                            <div class="form-grid"><label>Kategori<select name="category" required><option>Operasional</option><option>Perlengkapan</option><option>Gaji</option><option>Listrik dan air</option><option>Lainnya</option></select></label><label>Jumlah (Rp)<input name="amount" type="number" min="1" step="500" required></label></div>
                            <label>Tanggal<input name="spent_at" type="date" value="{{ now()->toDateString() }}" required></label>
                            <button class="button button-dark" type="submit">Simpan pengeluaran</button>
                        </form>
                    </section>
                    <section class="panel"><div class="panel-heading"><div><h2>Pemasukan terbaru</h2><p>Transaksi yang telah dibayar pelanggan.</p></div></div>
                        <div class="compact-list">@forelse ($paidRows as $row)<div class="compact-row"><span><strong>{{ $row->customer_name }}</strong><small>{{ $row->service_name }} · {{ \Carbon\Carbon::parse($row->paid_at)->format('d M Y') }}</small></span><b>+ Rp {{ number_format($row->total_amount, 0, ',', '.') }}</b></div>@empty<p class="empty-copy">Belum ada pemasukan tercatat.</p>@endforelse</div>
                    </section>
                </div>
                <section class="panel"><div class="panel-heading"><div><h2>Riwayat pengeluaran</h2><p>Catatan biaya terbaru.</p></div></div><div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Keperluan</th><th>Kategori</th><th>Jumlah</th></tr></thead><tbody>@forelse ($expenseRows as $expense)<tr><td>{{ \Carbon\Carbon::parse($expense->spent_at)->format('d M Y') }}</td><td class="cell-strong">{{ $expense->description }}</td><td>{{ $expense->category }}</td><td class="amount-negative">− Rp {{ number_format($expense->amount, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="4" class="empty-cell">Belum ada pengeluaran dicatat.</td></tr>@endforelse</tbody></table></div>@if ($expenseRows->hasPages())<div class="pagination">{{ $expenseRows->links() }}</div>@endif</section>

            @elseif ($section === 'reviews')
                <section class="review-list">
                    @forelse ($reviews as $review)
                        <article class="panel review-item"><div class="review-top"><div><h2>{{ $review->customer_name }}</h2><p>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y, H:i') }}</p></div><span class="rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }} <b>{{ $review->rating }}/5</b></span></div><p class="review-comment">{{ $review->comment }}</p>
                            @if ($review->owner_reply)<div class="owner-reply"><small>Balasan Anda</small><p>{{ $review->owner_reply }}</p></div>@else<form class="reply-form" action="{{ route('owner.reviews.reply', $review->id) }}" method="POST">@csrf<textarea name="owner_reply" required maxlength="2000" rows="2" placeholder="Tulis balasan untuk pelanggan..."></textarea><button class="button button-dark" type="submit">Kirim balasan</button></form>@endif
                            <form action="{{ route('owner.reviews.delete', $review->id) }}" method="POST" class="delete-confirm review-delete">@csrf @method('DELETE')<button class="text-link danger-text" type="submit">Hapus ulasan</button></form>
                        </article>
                    @empty
                        <section class="panel empty-panel"><span class="empty-symbol">☆</span><h2>Belum ada ulasan</h2><p>Ulasan pelanggan akan muncul di halaman ini.</p></section>
                    @endforelse
                    @if ($reviews->hasPages())<div class="pagination">{{ $reviews->links() }}</div>@endif
                </section>

            @elseif ($section === 'chat')
                <section class="chat-layout" data-chat-root data-messages-url="{{ route('owner.chat.messages') }}" data-reply-url="{{ route('owner.chat.reply') }}" data-customer="{{ $selectedCustomer }}" data-user-id="{{ $selectedUserId ?? '' }}">
                    <aside class="chat-contacts"><div class="contact-heading"><h2>Pelanggan</h2><span>{{ $customers->count() }}</span></div>
                        @forelse ($customers as $customer)
                            <a class="contact-link {{ $selectedCustomer === $customer->customer_name && (string) $selectedUserId === (string) $customer->user_id ? 'selected' : '' }}" href="{{ route('owner.chat', ['customer' => $customer->customer_name, 'user_id' => $customer->user_id]) }}"><span class="avatar avatar-small">{{ strtoupper(substr($customer->customer_name, 0, 1)) }}</span><span><strong>{{ $customer->customer_name }}</strong><small>{{ $customer->user_id ? 'Pelanggan terdaftar' : 'Thread lama' }}</small></span></a>
                        @empty
                            <p class="empty-copy contact-empty">Belum ada pesan dari pelanggan.</p>
                        @endforelse
                    </aside>
                    <div class="chat-window">
                        @if ($selectedCustomer)
                            <div class="chat-header"><span class="avatar avatar-small">{{ strtoupper(substr($selectedCustomer, 0, 1)) }}</span><div><strong>{{ $selectedCustomer }}</strong><small><span class="status-dot"></span> Percakapan pelanggan</small></div><span class="live-label">Pembaruan otomatis</span></div>
                            <div class="messages" id="chatMessages" aria-live="polite">
                                @foreach ($messages as $message)
                                    <div class="message {{ $message->sender === 'owner' ? 'message-owner' : 'message-customer' }}" data-message-id="{{ $message->id }}"><p>{{ $message->message }}</p><small>{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small></div>
                                @endforeach
                            </div>
                            <form class="chat-compose" id="chatReplyForm"><input name="message" id="chatMessageInput" autocomplete="off" maxlength="2000" placeholder="Tulis pesan..." required><button class="button button-dark" type="submit">Kirim <span>→</span></button></form>
                        @else
                            <div class="chat-empty"><span class="empty-symbol">✉</span><h2>Pilih percakapan</h2><p>Percakapan pelanggan akan tampil di daftar sebelah kiri.</p></div>
                        @endif
                    </div>
                </section>

            @elseif ($section === 'settings')
                <section class="panel settings-panel"><div class="panel-heading"><div><h2>Informasi laundry</h2><p>Informasi ini ditampilkan kepada pelanggan.</p></div></div>
                    <form class="settings-form" action="{{ route('owner.settings.update') }}" method="POST">@csrf @method('PUT')
                        <div class="settings-fields"><label>Nama laundry<input name="store_name" value="{{ old('store_name', $laundry->store_name) }}" required maxlength="255"></label><label>Nomor telepon<input name="store_phone" value="{{ old('store_phone', $laundry->store_phone) }}" required maxlength="30"></label><label>Biaya antar-jemput (Rp)<input name="pickup_fee" type="number" min="0" max="1000000" step="500" value="{{ old('pickup_fee', $laundry->pickup_fee ?? 5000) }}" required></label><label class="full-field">Alamat<textarea name="address" rows="2" required>{{ old('address', $laundry->address) }}</textarea></label><label class="full-field">Deskripsi<textarea name="description" rows="3">{{ old('description', $laundry->description) }}</textarea></label></div>
                        <div class="schedule-heading"><div><h2>Jadwal operasional</h2><p>Atur jam buka untuk setiap hari.</p></div></div>
                        <div class="schedule-list">
                            @foreach ($days as $day => $dayName)
                                @php $dayHour = $hours->get($day); @endphp
                                <div class="schedule-row" data-schedule-row>
                                    <strong>{{ $dayName }}</strong>
                                    <label class="time-field"><span class="sr-only">Jam buka {{ strtolower($dayName) }}</span><input name="hours[{{ $day }}][opens_at]" type="time" value="{{ $dayHour?->opens_at ? substr($dayHour->opens_at, 0, 5) : '08:00' }}" data-time-input></label>
                                    <span class="time-separator">sampai</span>
                                    <label class="time-field"><span class="sr-only">Jam tutup {{ strtolower($dayName) }}</span><input name="hours[{{ $day }}][closes_at]" type="time" value="{{ $dayHour?->closes_at ? substr($dayHour->closes_at, 0, 5) : '17:00' }}" data-time-input></label>
                                    <label class="closed-toggle"><input name="hours[{{ $day }}][is_closed]" value="1" type="checkbox" data-closed-toggle @checked($dayHour?->is_closed)> Tutup</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="settings-actions"><button class="button button-dark" type="submit">Simpan pengaturan</button></div>
                    </form>
                </section>
            @endif
        </div>
    </main>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>
<script src="{{ asset('owner/owner.js') }}" defer></script>
</body>
</html>