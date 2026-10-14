<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail pesanan · Laundryku</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
</head>
<body class="customer-page">
    <header class="customer-nav">
        <div class="nav-user-actions"><a class="nav-profile-link" href="{{ route('profile') }}"><span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="nav-user-copy"><small>Akun</small><strong>{{ auth()->user()->name }}</strong></span></a><a class="nav-back-link" href="{{ route('profile', ['tab' => 'orders']) }}">← Riwayat pesanan</a></div>
        <a class="customer-brand" href="{{ route('dashboard') }}"><span class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a>
    </header>

    @php
        $progressSteps = ['pending' => 'Pesanan diterima', 'processing' => 'Sedang dicuci', 'completed' => 'Selesai'];
        $progressOrder = array_keys($progressSteps);
        $currentProgress = array_search($order->status, $progressOrder, true);
    @endphp

    <main class="order-detail-main">
        @if (session('success'))<div class="customer-notice">{{ session('success') }}</div>@endif
        <div class="order-detail-header"><div><p class="overline">PESANAN #{{ $order->id }}</p><h1>{{ $order->store_name }}</h1><p>{{ $order->laundry_address }}</p></div><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></div>

        <div class="order-detail-grid">
            <section class="order-detail-main-card">
                <div class="order-progress-heading"><div><p class="overline">STATUS CUCIAN</p><h2>Proses pesanan</h2></div><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></div>
                @if ($order->status === 'cancelled')
                    <div class="cancelled-message"><span>!</span><div><strong>Pesanan dibatalkan</strong><p>Hubungi laundry jika kamu membutuhkan informasi lebih lanjut.</p></div></div>
                @else
                    <div class="order-timeline">
                        @foreach ($progressSteps as $step => $label)
                            @php $stepIndex = array_search($step, $progressOrder, true); @endphp
                            <div class="timeline-step {{ $currentProgress !== false && $stepIndex <= $currentProgress ? 'step-done' : '' }} {{ $order->status === 'cancelled' ? '' : '' }}"><span class="timeline-marker">{{ $stepIndex < ($currentProgress === false ? 0 : $currentProgress) ? '✓' : $stepIndex + 1 }}</span><div><strong>{{ $label }}</strong><small>{{ $step === 'pending' ? 'Menunggu laundry mulai mengerjakan' : ($step === 'processing' ? 'Laundry sedang menangani cucianmu' : 'Pesanan selesai dikerjakan') }}</small></div></div>
                        @endforeach
                    </div>
                @endif

                <div class="order-section-divider"></div>
                <div class="order-detail-heading"><div><p class="overline">RINCIAN CUCIAN</p><h2>{{ $order->service_name }}</h2></div></div>
                <p class="order-detail-description">{{ $order->order_details ?: 'Tidak ada catatan tambahan.' }}</p>
                <div class="order-detail-facts"><span><small>JUMLAH</small><strong>{{ $order->quantity }} paket / kg</strong></span><span><small>DIBUAT</small><strong>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</strong></span><span><small>TELEPON LAUNDRY</small><strong>{{ $order->store_phone }}</strong></span></div>
            </section>

            <aside class="order-payment-card">
                <p class="overline">PEMBAYARAN</p><h2>Ringkasan biaya</h2>
                <div class="payment-total-row"><span>Total pesanan</span><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
                <div class="payment-detail-row"><span>Metode</span><strong>{{ $order->payment_method ? strtoupper($order->payment_method) : 'Belum dipilih' }}</strong></div>
                <div class="payment-detail-row"><span>Status</span><span class="status-pill status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></div>

                @if ($order->payment_status === 'unpaid' && $order->payment_method === 'qris')
                    <div class="payment-instruction"><button class="primary-button payment-submit" type="button" data-show-payment>Bayar sekarang <span>→</span></button><div class="payment-code-details" data-payment-details hidden><strong>QRIS simulasi</strong><p>Pindai/tinjau kode demo lalu konfirmasi. Kode ini tidak memproses uang nyata.</p><div class="simulated-payment order-qr"><canvas class="fake-qr" width="168" height="168" data-fake-qr="{{ $order->payment_reference }}" aria-label="QRIS simulasi pesanan"></canvas><div><code>{{ $order->payment_reference }}</code></div></div><form action="{{ route('orders.confirm-qris', $order->id) }}" method="POST" data-payment-confirm>@csrf<button class="primary-button payment-submit" type="submit">Saya sudah membayar</button></form></div></div>
                @elseif ($order->payment_status === 'unpaid')
                    <div class="payment-instruction"><strong>Menunggu pembayaran</strong><p>Hubungi laundry untuk mendapatkan instruksi pembayaran.</p></div>
                @else
                    <div class="payment-paid"><span>✓</span><div><strong>Pembayaran diterima</strong><small>{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') : 'Dibayar dengan saldo' }}</small></div></div>
                @endif
            </aside>
        </div>

        <section class="order-chat-panel" data-user-chat data-messages-url="{{ route('orders.messages', $order->id) }}" data-order-id="{{ $order->id }}" data-laundry-name="{{ $order->store_name }}">
            <div class="chat-panel-heading"><div><p class="overline">BANTUAN PESANAN</p><h2>Chat dengan {{ $order->store_name }}</h2></div><span class="chat-live"><i></i> Terhubung</span></div>
            <div class="order-chat-messages" id="userOrderChatMessages" aria-live="polite">
                @forelse ($messages as $message)
                    <article class="order-chat-message {{ $message->sender === 'customer' ? 'is-customer' : 'is-owner' }}" data-message-id="{{ $message->id }}"><strong>{{ $message->sender === 'customer' ? 'Saya' : $order->store_name }}</strong><p>{{ $message->message }}</p><small>{{ \Carbon\Carbon::parse($message->created_at)->format('d M, H:i') }}</small></article>
                @empty
                    <p class="order-chat-empty" data-chat-empty>Tanyakan status, waktu selesai, atau detail cucianmu kepada laundry.</p>
                @endforelse
            </div>
            <form class="order-chat-compose" action="{{ route('orders.messages.store', $order->id) }}" method="POST">@csrf<label class="sr-only" for="customerOrderMessage">Pesan untuk laundry</label><input id="customerOrderMessage" name="message" maxlength="2000" placeholder="Tulis pesan untuk laundry..." required><button class="primary-button" type="submit">Kirim <span>→</span></button></form>
        </section>

        <a class="order-back-link" href="{{ route('profile', ['tab' => 'orders']) }}">← Kembali ke riwayat transaksi</a>
    </main>

    <footer class="customer-footer"><a class="customer-brand" href="{{ route('dashboard') }}"><span class="brand-symbol">L</span><span>Laundry<span>ku</span></span></a><span>Jasa laundry lokal dalam satu tempat.</span><a href="{{ route('profile') }}">Profil saya</a></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
</body>
</html>