<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail pesanan - Washly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
    <style>
        body.order-page{display:flex;min-height:100vh;flex-direction:column;background:#f7f8f5;color:#24342d;font-family:"DM Sans","Avenir Next",Avenir,"Segoe UI",sans-serif;font-size:14px}
        .order-page .profile-nav{position:sticky;top:0;z-index:30;width:100%;border-bottom:1px solid #e7ece8;background:#fff}.order-page .profile-nav-inner{display:flex;width:min(100% - 40px,1180px);min-height:76px;align-items:center;gap:32px;margin:auto}
        .order-page .customer-brand{display:inline-flex;align-items:center;gap:9px;color:#08796f;font-size:22px;font-weight:750;letter-spacing:-.04em}.order-page .profile-brand-logo{width:42px;height:44px;object-fit:contain}
        .order-page .profile-nav-links{display:flex;align-items:center;gap:5px;padding:5px;border-radius:14px;background:#f2f2ff}.order-page .profile-nav-links a{padding:10px 16px;border-radius:10px;color:#52605d;font-size:13px;font-weight:650;white-space:nowrap}.order-page .profile-nav-links a:hover{color:#00796f;background:#e4f4f1}.order-page .profile-nav-links a.active{color:#fff;background:#00796f}
        .order-page .profile-nav-user{display:flex;align-items:center;gap:10px;margin-left:auto;padding:6px 12px 6px 6px;border:1px solid #e7ece8;border-radius:30px;color:#25332e;background:#fff;font-size:13px;font-weight:700}.order-page .nav-user-avatar{display:grid;width:36px;height:36px;place-items:center;overflow:hidden;border-radius:50%;color:#15564e;background:#e1f0e7}.order-page .nav-user-avatar img{width:100%;height:100%;object-fit:cover}
        .order-page .order-detail-main{width:min(100% - 40px,1180px);flex:1;margin:0 auto;padding:28px 0 52px}.order-page .order-detail-header h1,.order-page .order-progress-heading h2,.order-page .order-detail-heading h2,.order-page .order-payment-card h2,.order-page .payment-total-row strong,.order-page .chat-panel-heading h2{font-family:"DM Sans","Avenir Next",Avenir,"Segoe UI",sans-serif;font-weight:700;letter-spacing:-.04em;color:#24342d}.order-page .order-detail-header h1{font-size:32px}.order-page .order-detail-header>div>p:last-child{font-size:13px}
        .order-page .order-detail-grid{gap:18px}.order-page .order-detail-main-card,.order-page .order-payment-card,.order-page .order-chat-panel{border-color:#e2e9e4;border-radius:16px;box-shadow:0 6px 20px #24352b08}.order-page .order-detail-main-card,.order-page .order-payment-card{padding:24px}.order-page .order-progress-heading h2,.order-page .order-detail-heading h2,.order-page .order-payment-card h2{font-size:21px}
        .order-page .primary-button{border-radius:9px;background:#08796f}.order-page .primary-button:hover{background:#00645d}.order-page .button.button-light{border-radius:9px;color:#08796f;background:#edf7f3}.order-page .order-back-link{color:#08796f;font-size:12px}
        .order-page .overline{font-size:10px;font-weight:700;letter-spacing:.14em}
        .order-page .status-pill{font-size:11px;font-weight:700}.order-page .timeline-step strong{font-size:13px;font-weight:700}.order-page .timeline-step small{font-size:11px}
        .order-page .order-detail-description{font-size:12px;line-height:1.65}.order-page .order-detail-facts small{font-size:10px}.order-page .order-detail-facts strong{font-size:12px;line-height:1.5}.order-page .order-back-link{font-size:13px}
        .order-page .payment-total-row,.order-page .payment-detail-row{font-size:12px}.order-page .payment-detail-row strong{font-size:12px}.order-page .payment-instruction>strong{font-size:13px}.order-page .payment-instruction>p{font-size:12px;line-height:1.6}.order-page .payment-paid strong{font-size:12px}.order-page .payment-paid small{font-size:10px}
        .order-page .order-feedback{display:flex;align-items:center;gap:13px;margin:0 0 25px;padding:15px 18px;border:1px solid #cfe5d5;border-radius:14px;color:#315e41;background:linear-gradient(110deg,#f0f8f0,#f7fbf7);box-shadow:0 5px 16px #244e3d0b}
        .order-page .order-feedback-icon{display:grid;width:34px;height:34px;flex:none;place-items:center;border-radius:50%;color:#fff;background:#568668;font-size:16px;font-weight:700}
        .order-page .order-feedback strong{display:block;font-size:13px;font-weight:700}.order-page .order-feedback p{margin:2px 0 0;color:#607967;font-size:12px;line-height:1.45}
        .order-page .order-feedback-error{border-color:#f0d8cf;color:#8e4f41;background:#fff7f4}.order-page .order-feedback-error .order-feedback-icon{background:#bd6c5d}.order-page .order-feedback-error p{color:#85665e}
        .order-page .ready-pickup-notice{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:18px;padding:16px;border:1px solid #cfe5d7;border-radius:12px;background:#edf7ef}.order-page .ready-pickup-notice p{margin:5px 0 0;color:#65766b;font-size:13px;line-height:1.5}.order-page .ready-pickup-notice strong{color:#285f43}.order-page .ready-pickup-notice form{flex:none}.order-page .ready-pickup-notice .primary-button{padding:11px 15px;font-size:13px}
        .order-page .order-review-card{margin-top:18px;padding:23px 24px;border:1px solid #e2e9e4;border-radius:16px;background:#fff;box-shadow:0 6px 20px #24352b08}.order-page .order-review-card h2{margin:0;color:#24342d;font-size:20px;font-weight:700;letter-spacing:-.03em}.order-page .order-review-card>p{margin:6px 0 17px;color:#718078;font-size:13px;line-height:1.5}.order-page .review-stars{display:flex;flex-direction:row-reverse;justify-content:flex-end;gap:5px;margin:0 0 15px}.order-page .review-stars input{position:absolute;width:1px;height:1px;opacity:0}.order-page .review-stars label{color:#d2dcd4;font-size:34px;line-height:1;cursor:pointer;transition:color .15s ease,transform .15s ease}.order-page .review-stars input:checked~label,.order-page .review-stars label:hover,.order-page .review-stars label:hover~label{color:#e4ad3d}.order-page .review-stars label:hover{transform:translateY(-2px)}.order-page .review-stars input:focus-visible+label{outline:2px solid #337b70;outline-offset:2px;border-radius:4px}.order-page .review-form label[for="reviewComment"]{display:block;margin-bottom:7px;color:#485a4f;font-size:12px;font-weight:700}.order-page .review-form textarea{display:block;width:100%;min-height:82px;padding:11px 13px;border:1px solid #dce5df;border-radius:10px;color:#24342d;font:inherit;font-size:13px;resize:vertical}.order-page .review-form textarea:focus{border-color:#76a991;outline:3px solid #e4f1e8}.order-page .review-form .primary-button{margin-top:12px;padding:11px 16px;font-size:13px}.order-page .review-error{margin:0 0 10px;color:#a44e44;font-size:12px}.order-page .review-thanks{display:flex;align-items:center;gap:12px;padding:13px 15px;border-radius:11px;color:#315e41;background:#f0f8f0}.order-page .review-thanks .review-thanks-stars{color:#d99d27;font-size:19px;letter-spacing:2px}.order-page .review-thanks p{margin:3px 0 0;color:#607967;font-size:12px}.order-page .review-edit{margin-top:12px}.order-page .review-edit summary{width:max-content;color:#337b70;font-size:13px;font-weight:700;cursor:pointer}.order-page .review-edit[open] summary{margin-bottom:16px}
        .order-page .order-chat-panel{margin-top:18px}.order-page .chat-panel-heading{padding:20px 22px}.order-page .chat-panel-heading h2{font-size:20px}.order-page .order-chat-messages{background:#f8faf8}.order-page .order-chat-compose input{border-radius:9px;font-size:13px}.order-page .order-chat-message strong{font-size:11px}.order-page .order-chat-message p{font-size:12px}.order-page .order-chat-message small{font-size:9px}.order-page .order-chat-empty{font-size:12px}
        .order-page .customer-footer{width:100%;margin-top:auto}.order-page .customer-footer .customer-brand{font-size:18px}.order-page .customer-footer .profile-brand-logo{width:34px;height:36px}
        @media(max-width:800px){.order-page .profile-nav-inner{width:calc(100% - 32px);flex-wrap:wrap;gap:12px;padding:10px 0}.order-page .profile-nav-links{order:3;width:100%;overflow-x:auto}.order-page .profile-nav-links a{padding:9px 13px}}
        @media(max-width:620px){.order-page .profile-nav-inner{width:calc(100% - 30px)}.order-page .customer-brand{font-size:19px}.order-page .profile-brand-logo{width:36px;height:38px}.order-page .profile-nav-user{gap:7px;padding:4px 9px 4px 4px;font-size:12px}.order-page .nav-user-avatar{width:32px;height:32px}.order-page .profile-nav-links a{font-size:11px}.order-page .order-detail-main{width:calc(100% - 30px);padding-top:20px}.order-page .order-detail-header h1{font-size:27px}.order-page .order-detail-main-card,.order-page .order-payment-card{padding:17px}.order-page .chat-panel-heading{padding:16px}.order-page .customer-footer{align-items:flex-start;flex-direction:column;gap:8px}.order-page .ready-pickup-notice{align-items:stretch;flex-direction:column}.order-page .ready-pickup-notice .primary-button{width:100%}.order-page .order-feedback{margin-bottom:20px;padding:13px}.order-page .order-feedback-icon{width:31px;height:31px}.order-page .order-review-card{padding:19px 17px}.order-page .review-form .primary-button{width:100%}}
    </style>
</head>

<body class="customer-page order-page">
    <header class="profile-nav">
        <div class="profile-nav-inner">
            <a class="customer-brand" href="{{ route('dashboard') }}"><img class="profile-brand-logo" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a>
            <nav class="profile-nav-links" aria-label="Navigasi utama"><a href="{{ route('dashboard') }}">Dashboard</a><a href="{{ route('laundries.discover') }}">Pesan Laundry</a><a class="active" href="{{ route('profile', ['tab' => 'orders']) }}">Riwayat</a></nav>
            <a class="profile-nav-user" href="{{ route('profile') }}"><span class="nav-user-avatar">@if (auth()->user()->profile_photo_path)<img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="">@else{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}@endif</span><span>{{ auth()->user()->name }}</span></a>
        </div>
    </header>
    @php
        $progressSteps = ['pending' => 'Pesanan diterima', 'processing' => 'Sedang dicuci', 'completed' => 'Selesai'];
        $progressOrder = array_keys($progressSteps);
        $currentProgress = array_search($order->status, $progressOrder, true);
        $deliveryLabels = [
            'waiting' => $order->delivery_method === 'pickup' ? 'Menunggu dijemput' : 'Menunggu diantar ke laundry',
            'picked_up' => 'Sudah dijemput kurir',
            'received' => 'Diterima laundry',
            'ready' => 'Siap diambil',
            'returned' => 'Diterima pelanggan',
        ];
    @endphp

    <main class="order-detail-main">
        @if (session('success'))
            <div class="order-feedback" role="status"><span class="order-feedback-icon" aria-hidden="true">✓</span><div><strong>Berhasil</strong><p>{{ session('success') }}</p></div></div>
        @endif
        <div class="order-detail-header">
            <div>
                <p class="overline">PESANAN #{{ $order->id }}</p>
                <h1>{{ $order->store_name }}</h1>
                <p>{{ $order->laundry_address }}</p>
            </div><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        </div>

        <div class="order-detail-grid">
            <section class="order-detail-main-card">
                <div class="order-progress-heading">
                    <div>
                        <p class="overline">STATUS CUCIAN</p>
                        <h2>Proses pesanan</h2>
                    </div><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>
                @if ($order->status === 'cancelled')
                    <div class="cancelled-message"><span>!</span>
                        <div><strong>Pesanan dibatalkan</strong>
                            <p>Hubungi laundry jika kamu membutuhkan informasi lebih lanjut.</p>
                        </div>
                    </div>
                @else
                    <div class="order-timeline">
                        @foreach ($progressSteps as $step => $label)
                            @php $stepIndex = array_search($step, $progressOrder, true); @endphp
                            <div
                                class="timeline-step {{ $currentProgress !== false && $stepIndex <= $currentProgress ? 'step-done' : '' }} {{ $order->status === 'cancelled' ? '' : '' }}">
                                <span
                                    class="timeline-marker">{{ $stepIndex < ($currentProgress === false ? 0 : $currentProgress) ? "\u{2713}" : $stepIndex + 1 }}</span>
                                <div>
                                    <strong>{{ $label }}</strong><small>{{ $step === 'pending' ? 'Menunggu laundry mulai mengerjakan' : ($step === 'processing' ? 'Laundry sedang menangani cucianmu' : 'Pesanan selesai dikerjakan') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="order-section-divider"></div>
                <div class="order-detail-heading">
                    <div>
                        <p class="overline">RINCIAN CUCIAN</p>
                        <h2>{{ $order->service_name }}</h2>
                    </div>
                </div>
                <p class="order-detail-description">{{ $order->order_details ?: 'Tidak ada catatan tambahan.' }}</p>
                <div class="order-section-divider"></div>
                <div class="delivery-progress">
                    <div>
                        <p class="overline">PENYERAHAN</p>
                        <h2>{{ $order->delivery_method === 'pickup' ? 'Antar-jemput' : 'Drop-off' }}</h2>
                    </div><span
                        class="status-pill status-{{ $order->delivery_status }}">{{ $deliveryLabels[$order->delivery_status] ?? ucfirst($order->delivery_status) }}</span>
                </div>
                @if ($order->delivery_status === 'ready')
                <div class="ready-pickup-notice"><div><strong>Cucianmu siap diambil</strong><p>Setelah mengambil dan menerima cucian, konfirmasi melalui tombol ini.</p></div><form action="{{ route('orders.confirm-received', $order->id) }}" method="POST">@csrf<button class="primary-button" type="submit">Saya sudah menerima cucian</button></form></div>
                @endif
                @if ($order->delivery_method === 'pickup')
                    <p class="delivery-fee-note">Biaya jemput: Rp {{ number_format($order->pickup_fee, 0, ',', '.') }}
                    </p>
                @endif
                <div class="order-detail-facts"><span><small>BERAT
                            CUCIAN</small><strong>{{ $order->weight_kg !== null ? number_format($order->weight_kg, 2, ',', '.') . ' kg' : 'Menunggu ditimbang laundry' }}</strong></span><span><small>TARIF
                            PAKET</small><strong>Rp {{ number_format($order->service_rate ?? 0, 0, ',', '.') }} /
                            kg</strong></span><span><small>DIBUAT</small><strong>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</strong></span><span><small>TELEPON
                            LAUNDRY</small><strong>{{ $order->store_phone }}</strong></span></div>
            </section>

            <aside class="order-payment-card">
                <p class="overline">PEMBAYARAN</p>
                <h2>Ringkasan biaya</h2>
                @if ($order->weighed_at)
                    <div class="invoice-breakdown">
                        <div><span>{{ number_format($order->weight_kg, 2, ',', '.') }} kg &times; Rp
                                {{ number_format($order->service_rate, 0, ',', '.') }} / kg</span><strong>Rp
                                {{ number_format($order->weight_kg * $order->service_rate, 0, ',', '.') }}</strong>
                        </div>
                        <div><span>Biaya jemput</span><strong>Rp
                                {{ number_format($order->pickup_fee, 0, ',', '.') }}</strong></div>
                    </div>
                @endif
                <div class="payment-total-row">
                    <span>{{ $order->weighed_at ? 'Total tagihan akhir' : 'Estimasi tagihan' }}</span><strong>{{ $order->weighed_at ? 'Rp ' . number_format($order->total_amount, 0, ',', '.') : 'Menunggu timbang' }}</strong>
                </div>
                <div class="payment-detail-row">
                    <span>Metode</span><strong>{{ match ($order->payment_method) { 'cash' => 'Tunai di outlet', 'wallet' => 'Saldo Washly', 'qris' => 'QRIS', default => 'Belum dipilih' } }}</strong>
                </div>
                <div class="payment-detail-row"><span>Status</span><span
                        class="status-pill status-{{ $order->payment_status }}">{{ $order->payment_status === 'unpaid' && $order->payment_method === 'wallet' ? 'Menunggu konfirmasi owner' : ucfirst($order->payment_status) }}</span>
                </div>

                @if (!$order->weighed_at)
                    <div class="payment-instruction"><strong>Tagihan belum tersedia</strong>
                        <p>Laundry akan menerima dan menimbang cucianmu dahulu. Setelah berat serta total biaya masuk,
                            opsi pembayaran akan muncul di sini.</p>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && !$order->payment_method)
                    <div class="payment-instruction"><strong>Pilih pembayaran</strong>
                        <p>Berat sudah dikonfirmasi laundry. Pilih cara pembayaran untuk menyelesaikan pesanan.</p>
                        @if ($walletBalance >= $order->total_amount)
                            <form action="{{ route('orders.pay', $order->id) }}" method="POST" data-payment-confirm data-confirm-message="Ajukan pembayaran dari saldo? Saldo baru dipotong setelah owner mengonfirmasi.">@csrf<input
                                    type="hidden" name="payment_method" value="wallet"><button
                                    class="primary-button payment-submit" type="submit">Bayar dari saldo &middot; Rp
                                {{ number_format($order->total_amount, 0, ',', '.') }}</button></form>@else<p
                                class="wallet-warning">Saldo Rp {{ number_format($walletBalance, 0, ',', '.') }} belum
                                cukup. <a href="{{ route('profile', ['tab' => 'wallet']) }}">Top-up saldo</a> atau
                                pilih QRIS atau tunai di outlet.</p>
                        @endif
                        <form action="{{ route('orders.pay', $order->id) }}" method="POST">@csrf<input type="hidden"
                                name="payment_method" value="qris"><button class="button button-light payment-submit"
                                type="submit">Bayar dengan QRIS</button></form>
                        <form action="{{ route('orders.pay', $order->id) }}" method="POST">@csrf<input type="hidden"
                                name="payment_method" value="cash"><button class="button button-light payment-submit"
                                type="submit">Bayar tunai di outlet</button></form>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && $order->payment_method === 'qris')
                    <div class="payment-instruction"><button class="primary-button payment-submit" type="button"
                            data-show-payment>Bayar sekarang <span>&rarr;</span></button>
                        <div class="payment-code-details" data-payment-details hidden><strong>QRIS simulasi</strong>
                            <p>Pindai/tinjau kode demo lalu konfirmasi. Kode ini tidak memproses uang nyata.</p>
                            <div class="simulated-payment order-qr"><canvas class="fake-qr" width="168"
                                    height="168" data-fake-qr="{{ $order->payment_reference }}"
                                    aria-label="QRIS simulasi pesanan"></canvas>
                                <div><code>{{ $order->payment_reference }}</code></div>
                            </div>
                            <form action="{{ route('orders.confirm-qris', $order->id) }}" method="POST"
                                data-payment-confirm>@csrf<button class="primary-button payment-submit"
                                    type="submit">Saya sudah membayar</button></form>
                        </div>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && $order->payment_method === 'cash')
                    <div class="payment-instruction"><strong>Menunggu pembayaran tunai</strong>
                        <p>Bayar invoice ini langsung ke laundry. Setelah menerima uang, owner akan mencatat pembayaran di website.</p>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && $order->payment_method === 'wallet')
                    <div class="payment-instruction"><strong>Menunggu konfirmasi owner</strong>
                        <p>Permintaan pembayaran saldo sudah dikirim. Saldo akan dipotong setelah owner mengonfirmasi pembayaran.</p>
                    </div>
                @elseif ($order->payment_status === 'unpaid')
                    <div class="payment-instruction"><strong>Menunggu pembayaran</strong>
                        <p>Hubungi laundry untuk mendapatkan instruksi pembayaran.</p>
                    </div>
                @else
                    <div class="payment-paid"><span>&#10003;</span>
                        <div><strong>Pembayaran
                                diterima</strong><small>{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') : 'Dibayar dengan saldo' }}</small>
                        </div>
                    </div>
                @endif
            </aside>
        </div>

        @if ($order->status === 'completed' && $order->payment_status === 'paid' && $order->delivery_status === 'returned')
            <section class="order-review-card" aria-labelledby="reviewHeading">
                @if ($review)
                    <p class="overline">ULASAN PESANAN</p>
                    <h2 id="reviewHeading">Ulasanmu</h2>
                    <div class="review-thanks"><span class="review-thanks-stars" aria-label="{{ $review->rating }} dari 5 bintang">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span><div><strong>{{ $review->rating }}/5</strong><p>{{ $review->comment ?: 'Kamu memberi rating tanpa komentar.' }}</p></div></div>
                    <details class="review-edit" @if ($errors->has('rating') || $errors->has('comment')) open @endif>
                        <summary>Edit rating</summary>
                @else
                    <p class="overline">BAGIKAN PENGALAMANMU</p>
                    <h2 id="reviewHeading">Bagaimana pengalaman laundrymu?</h2>
                    <p>Ratingmu membantu pelanggan lain memilih laundry yang tepat.</p>
                @endif
                    @if ($errors->has('rating') || $errors->has('comment'))
                        <p class="review-error">{{ $errors->first('rating') ?: $errors->first('comment') }}</p>
                    @endif
                    <form class="review-form" action="{{ $review ? route('orders.review.update', $order->id) : route('orders.review.store', $order->id) }}" method="POST">
                        @csrf
                        @if ($review)
                            @method('PUT')
                        @endif
                        <div class="review-stars" role="radiogroup" aria-label="Pilih rating dari 1 sampai 5 bintang">
                            @for ($score = 5; $score >= 1; $score--)
                                <input id="reviewRating{{ $score }}" type="radio" name="rating" value="{{ $score }}" required @checked((int) old('rating', $review->rating ?? 0) === $score)>
                                <label for="reviewRating{{ $score }}" title="{{ $score }} dari 5 bintang" aria-label="{{ $score }} dari 5 bintang">★</label>
                            @endfor
                        </div>
                        <label for="reviewComment">Cerita singkat (opsional)</label>
                        <textarea id="reviewComment" name="comment" maxlength="2000" placeholder="Ceritakan pengalamanmu...">{{ old('comment', $review->comment ?? '') }}</textarea>
                        <button class="primary-button" type="submit">{{ $review ? 'Simpan perubahan' : 'Kirim ulasan' }}</button>
                    </form>
                    @if ($review)
                        </details>
                    @endif
            </section>
        @endif

        <section class="order-chat-panel" data-user-chat data-messages-url="{{ route('orders.messages', $order->id) }}"
            data-order-id="{{ $order->id }}" data-laundry-name="{{ $order->store_name }}">
            <div class="chat-panel-heading">
                <div>
                    <p class="overline">BANTUAN PESANAN</p>
                    <h2>Chat dengan {{ $order->store_name }}</h2>
                </div><span class="chat-live"><i></i> Terhubung</span>
            </div>
            <div class="order-chat-messages" id="userOrderChatMessages" aria-live="polite">
                @forelse ($messages as $message)
                    <article class="order-chat-message {{ $message->sender === 'customer' ? 'is-customer' : 'is-owner' }} {{ $message->sender === 'admin' ? 'is-admin' : '' }}" data-message-id="{{ $message->id }}"><strong>{{ $message->sender === 'customer' ? 'Saya' : ($message->sender === 'admin' ? 'Admin Washly' : $order->store_name) }}</strong><p>{{ $message->message }}</p><small>{{ \Carbon\Carbon::parse($message->created_at)->format('d M, H:i') }}</small></article>
                @empty
                    <p class="order-chat-empty" data-chat-empty>Tanyakan status, waktu selesai, atau detail cucianmu
                        kepada laundry.</p>
                @endforelse
            </div>
            <form class="order-chat-compose" action="{{ route('orders.messages.store', $order->id) }}" method="POST">
                @csrf<label class="sr-only" for="customerOrderMessage">Pesan untuk laundry</label><input
                    id="customerOrderMessage" name="message" maxlength="2000"
                    placeholder="Tulis pesan untuk laundry..." required><button class="primary-button"
                    type="submit">Kirim <span>&rarr;</span></button></form>
        </section>

        <a class="order-back-link" href="{{ route('profile', ['tab' => 'orders']) }}">&larr; Kembali ke riwayat transaksi</a>
    </main>

    <footer class="customer-footer"><a class="customer-brand" href="{{ route('dashboard') }}"><img class="profile-brand-logo" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a><span>Jasa laundry lokal dalam satu tempat.</span><a href="{{ route('profile') }}">Profil saya</a></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>

</html>

