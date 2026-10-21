<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Detail pesanan - Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/general.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />
</head>

<body class="customer-page order-page">
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
                    class="active"
                    href="{{ route('profile', ['tab' => 'orders']) }}"
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
            <div class="order-feedback" role="status">
                <span class="order-feedback-icon" aria-hidden="true">✓</span>
                <div>
                    <strong>Berhasil</strong>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif
        <div class="order-detail-header">
            <div>
                <p class="overline">PESANAN #{{ $order->id }}</p>
                <h1>{{ $order->store_name }}</h1>
                <p>{{ $order->laundry_address }}</p>
            </div>
            <span
                class="status-pill status-{{ $order->status }}"
                >{{ ucfirst($order->status) }}</span
            >
        </div>

        <div class="order-detail-grid">
            <section class="order-detail-main-card">
                <div class="order-progress-heading">
                    <div>
                        <p class="overline">STATUS CUCIAN</p>
                        <h2>Proses pesanan</h2>
                    </div>
                    <span
                        class="status-pill status-{{ $order->status }}"
                        >{{ ucfirst($order->status) }}</span
                    >
                </div>
                @if ($order->status === 'cancelled')
                    <div class="cancelled-message">
                        <span>!</span>
                        <div>
                            <strong>Pesanan dibatalkan</strong>
                            <p>Hubungi laundry jika kamu membutuhkan informasi lebih lanjut.</p>
                        </div>
                    </div>
                @else
                    <div class="order-timeline">
                        @foreach ($progressSteps as $step => $label)
                            @php $stepIndex = array_search($step, $progressOrder, true); @endphp
                            <div
                                class="timeline-step {{ $currentProgress !== false && $stepIndex <= $currentProgress ? 'step-done' : '' }} {{ $order->status === 'cancelled' ? '' : '' }}"
                            >
                                <span
                                    class="timeline-marker"
                                    >{{ $stepIndex < ($currentProgress === false ? 0 : $currentProgress) ? "\u{2713}" : $stepIndex + 1 }}</span
                                >
                                <div>
                                    <strong>{{ $label }}</strong
                                    ><small
                                        >{{ $step === 'pending' ? 'Menunggu laundry mulai mengerjakan' : ($step === 'processing' ? 'Laundry sedang menangani cucianmu' : 'Pesanan selesai dikerjakan') }}</small
                                    >
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
                        <h2>
                            {{ $order->delivery_method === 'pickup' ? 'Antar-jemput' : 'Drop-off' }}
                        </h2>
                    </div>
                    <span
                        class="status-pill status-{{ $order->delivery_status }}"
                        >{{ $deliveryLabels[$order->delivery_status] ?? ucfirst($order->delivery_status) }}</span
                    >
                </div>
                @if ($order->delivery_status === 'ready')
                    <div class="ready-pickup-notice">
                        <div>
                            <strong>Cucianmu siap diambil</strong>
                            <p>Setelah mengambil dan menerima cucian, konfirmasi melalui tombol ini.</p>
                        </div>
                        <form
                            action="{{ route('orders.confirm-received', $order->id) }}"
                            method="POST"
                        >
                            @csrf
                            <button class="primary-button" type="submit">
                                Saya sudah menerima cucian
                            </button>
                        </form>
                    </div>
                @endif
                @if ($order->delivery_method === 'pickup')
                    <p class="delivery-fee-note">
                        Biaya jemput: Rp {{ number_format($order->pickup_fee, 0, ',', '.') }}
                    </p>
                @endif
                <div class="order-detail-facts">
                    <span
                        ><small>BERAT CUCIAN</small
                        ><strong
                            >{{ $order->weight_kg !== null ? number_format($order->weight_kg, 2, ',', '.') . ' kg' : 'Menunggu ditimbang laundry' }}</strong
                        ></span
                    ><span
                        ><small>TARIF PAKET</small
                        ><strong
                            >Rp {{ number_format($order->service_rate ?? 0, 0, ',', '.') }} /
                            kg</strong
                        ></span
                    ><span
                        ><small>DIBUAT</small
                        ><strong
                            >{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</strong
                        ></span
                    ><span
                        ><small>TELEPON LAUNDRY</small
                        ><strong>{{ $order->store_phone }}</strong></span
                    >
                </div>
            </section>

            <aside class="order-payment-card">
                <p class="overline">PEMBAYARAN</p>
                <h2>Ringkasan biaya</h2>
                @if ($order->weighed_at)
                    <div class="invoice-breakdown">
                        <div>
                            <span
                                >{{ number_format($order->weight_kg, 2, ',', '.') }} kg
                                &times; Rp {{ number_format($order->service_rate, 0, ',', '.') }} /
                                kg</span
                            ><strong
                                >Rp {{ number_format($order->weight_kg * $order->service_rate, 0, ',', '.') }}</strong
                            >
                        </div>
                        <div>
                            <span>Biaya jemput</span
                            ><strong
                                >Rp {{ number_format($order->pickup_fee, 0, ',', '.') }}</strong
                            >
                        </div>
                    </div>
                @endif
                <div class="payment-total-row">
                    <span
                        >{{ $order->weighed_at ? 'Total tagihan akhir' : 'Estimasi tagihan' }}</span
                    ><strong
                        >{{ $order->weighed_at ? 'Rp ' . number_format($order->total_amount, 0, ',', '.') : 'Menunggu timbang' }}</strong
                    >
                </div>
                <div class="payment-detail-row">
                    <span>Metode</span
                    ><strong
                        >{{ match ($order->payment_method) { 'cash' => 'Tunai di outlet', 'wallet' => 'Saldo Washly', 'qris' => 'QRIS', default => 'Belum dipilih' } }}</strong
                    >
                </div>
                <div class="payment-detail-row">
                    <span>Status</span
                    ><span
                        class="status-pill status-{{ $order->payment_status }}"
                        >{{ $order->payment_status === 'unpaid' && $order->payment_method === 'wallet' ? 'Menunggu konfirmasi owner' : ucfirst($order->payment_status) }}</span
                    >
                </div>

                @if (! $order->weighed_at)
                    <div class="payment-instruction">
                        <strong>Tagihan belum tersedia</strong>
                        <p>Laundry akan menerima dan menimbang cucianmu dahulu. Setelah berat serta total biaya masuk, opsi pembayaran akan muncul di sini.</p>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && ! $order->payment_method)
                    <div class="payment-instruction">
                        <strong>Pilih pembayaran</strong>
                        <p>Berat sudah dikonfirmasi laundry. Pilih cara pembayaran untuk menyelesaikan pesanan.</p>
                        @if ($walletBalance >= $order->total_amount)
                            <form
                                action="{{ route('orders.pay', $order->id) }}"
                                method="POST"
                                data-payment-confirm
                                data-confirm-message="Ajukan pembayaran dari saldo? Saldo baru dipotong setelah owner mengonfirmasi."
                            >
                                @csrf
                                <input
                                    type="hidden"
                                    name="payment_method"
                                    value="wallet"
                                /><button
                                    class="primary-button payment-submit"
                                    type="submit"
                                >
                                    Bayar dari saldo &middot; Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </button>
                            </form>
                        @else
                            <p class="wallet-warning">Saldo Rp {{ number_format($walletBalance, 0, ',', '.') }} belum cukup. <a href="{{ route('profile', ['tab' => 'wallet']) }}">Top-up saldo</a> atau pilih QRIS atau tunai di outlet.</p>
                        @endif
                        <form
                            action="{{ route('orders.pay', $order->id) }}"
                            method="POST"
                        >
                            @csrf
                            <input
                                type="hidden"
                                name="payment_method"
                                value="qris"
                            /><button
                                class="button button-light payment-submit"
                                type="submit"
                            >
                                Bayar dengan QRIS
                            </button>
                        </form>
                        <form
                            action="{{ route('orders.pay', $order->id) }}"
                            method="POST"
                        >
                            @csrf
                            <input
                                type="hidden"
                                name="payment_method"
                                value="cash"
                            /><button
                                class="button button-light payment-submit"
                                type="submit"
                            >
                                Bayar tunai di outlet
                            </button>
                        </form>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && $order->payment_method === 'qris')
                    <div class="payment-instruction">
                        <button
                            class="primary-button payment-submit"
                            type="button"
                            data-show-payment
                        >
                            Bayar sekarang <span>&rarr;</span>
                        </button>
                        <div
                            class="payment-code-details"
                            data-payment-details
                            hidden
                        >
                            <strong>QRIS simulasi</strong>
                            <p>Pindai/tinjau kode demo lalu konfirmasi. Kode ini tidak memproses uang nyata.</p>
                            <div class="simulated-payment order-qr">
                                <canvas
                                    class="fake-qr"
                                    width="168"
                                    height="168"
                                    data-fake-qr="{{ $order->payment_reference }}"
                                    aria-label="QRIS simulasi pesanan"
                                ></canvas>
                                <div>
                                    <code>{{ $order->payment_reference }}</code>
                                </div>
                            </div>
                            <form
                                action="{{ route('orders.confirm-qris', $order->id) }}"
                                method="POST"
                                data-payment-confirm
                            >
                                @csrf
                                <button
                                    class="primary-button payment-submit"
                                    type="submit"
                                >
                                    Saya sudah membayar
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && $order->payment_method === 'cash')
                    <div class="payment-instruction">
                        <strong>Menunggu pembayaran tunai</strong>
                        <p>Bayar invoice ini langsung ke laundry. Setelah menerima uang, owner akan mencatat pembayaran di website.</p>
                    </div>
                @elseif ($order->payment_status === 'unpaid' && $order->payment_method === 'wallet')
                    <div class="payment-instruction">
                        <strong>Menunggu konfirmasi owner</strong>
                        <p>Permintaan pembayaran saldo sudah dikirim. Saldo akan dipotong setelah owner mengonfirmasi pembayaran.</p>
                    </div>
                @elseif ($order->payment_status === 'unpaid')
                    <div class="payment-instruction">
                        <strong>Menunggu pembayaran</strong>
                        <p>Hubungi laundry untuk mendapatkan instruksi pembayaran.</p>
                    </div>
                @else
                    <div class="payment-paid">
                        <span>&#10003;</span>
                        <div>
                            <strong>Pembayaran diterima</strong
                            ><small
                                >{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') : 'Dibayar dengan saldo' }}</small
                            >
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
                    <div class="review-thanks">
                        <span
                            class="review-thanks-stars"
                            aria-label="{{ $review->rating }} dari 5 bintang"
                            >{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span
                        >
                        <div>
                            <strong>{{ $review->rating }}/5</strong>
                            <p>{{ $review->comment ?: 'Kamu memberi rating tanpa komentar.' }}</p>
                        </div>
                    </div>
                    <details
                        class="review-edit"
                        @if ($errors->has('rating') || $errors->has('comment')) open @endif
                    >
                        <summary>Edit rating</summary>
                @else
                    <p class="overline">BAGIKAN PENGALAMANMU</p>
                    <h2 id="reviewHeading">Bagaimana pengalaman laundrymu?</h2>
                    <p>Ratingmu membantu pelanggan lain memilih laundry yang tepat.</p>
                @endif
                @if ($errors->has('rating') || $errors->has('comment'))
                    <p class="review-error">{{ $errors->first('rating') ?: $errors->first('comment') }}</p>
                @endif
                <form
                    class="review-form"
                    action="{{ $review ? route('orders.review.update', $order->id) : route('orders.review.store', $order->id) }}"
                    method="POST"
                >
                    @csrf
                    @if ($review)
                        @method ('PUT')
                    @endif
                    <div
                        class="review-stars"
                        role="radiogroup"
                        aria-label="Pilih rating dari 1 sampai 5 bintang"
                    >
                        @for ($score = 5; $score >= 1; $score--)
                            <input
                                id="reviewRating{{ $score }}"
                                type="radio"
                                name="rating"
                                value="{{ $score }}"
                                required
                                @checked ((int) old('rating', $review->rating ?? 0) === $score)
                            />
                            <label
                                for="reviewRating{{ $score }}"
                                title="{{ $score }} dari 5 bintang"
                                aria-label="{{ $score }} dari 5 bintang"
                                >★</label
                            >
                        @endfor
                    </div>
                    <label for="reviewComment">Cerita singkat (opsional)</label>
                    <textarea
                        id="reviewComment"
                        name="comment"
                        maxlength="2000"
                        placeholder="Ceritakan pengalamanmu..."
                        >{{ old('comment', $review->comment ?? '') }}</textarea
                    >
                    <button class="primary-button" type="submit">
                        {{ $review ? 'Simpan perubahan' : 'Kirim ulasan' }}
                    </button>
                </form>
                @if ($review)
                    </details>
                @endif
            </section>
        @endif

        <section
            class="order-chat-panel"
            data-user-chat
            data-messages-url="{{ route('orders.messages', $order->id) }}"
            data-order-id="{{ $order->id }}"
            data-laundry-name="{{ $order->store_name }}"
        >
            <div class="chat-panel-heading">
                <div>
                    <p class="overline">BANTUAN PESANAN</p>
                    <h2>Chat dengan {{ $order->store_name }}</h2>
                </div>
                <span class="chat-live"><i></i> Terhubung</span>
            </div>
            <div
                class="order-chat-messages"
                id="userOrderChatMessages"
                aria-live="polite"
            >
                @forelse ($messages as $message)
                    <article
                        class="order-chat-message {{ $message->sender === 'customer' ? 'is-customer' : 'is-owner' }} {{ $message->sender === 'admin' ? 'is-admin' : '' }}"
                        data-message-id="{{ $message->id }}"
                    >
                        <strong
                            >{{ $message->sender === 'customer' ? 'Saya' : ($message->sender === 'admin' ? 'Admin Washly' : $order->store_name) }}</strong
                        >
                        <p>{{ $message->message }}</p>
                        <small
                            >{{ \Carbon\Carbon::parse($message->created_at)->format('d M, H:i') }}</small
                        >
                    </article>
                @empty
                    <p class="order-chat-empty" data-chat-empty>Tanyakan status, waktu selesai, atau detail cucianmu kepada laundry.</p>
                @endforelse
            </div>
            <form
                class="order-chat-compose"
                action="{{ route('orders.messages.store', $order->id) }}"
                method="POST"
            >
                @csrf
                <label class="sr-only" for="customerOrderMessage"
                    >Pesan untuk laundry</label
                ><input
                    id="customerOrderMessage"
                    name="message"
                    maxlength="2000"
                    placeholder="Tulis pesan untuk laundry..."
                    required
                /><button class="primary-button" type="submit">
                    Kirim <span>&rarr;</span>
                </button>
            </form>
        </section>

        <a
            class="order-back-link"
            href="{{ route('profile', ['tab' => 'orders']) }}"
            >&larr; Kembali ke riwayat transaksi</a
        >
    </main>

    <footer class="customer-footer">
        <a class="customer-brand" href="{{ route('dashboard') }}"
            ><img
                class="profile-brand-logo"
                src="{{ asset('images/washly-logo.png') }}"
                alt=""
            />Washly</a
        ><span>Jasa laundry lokal dalam satu tempat.</span
        ><a href="{{ route('profile') }}">Profil saya</a>
    </footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
