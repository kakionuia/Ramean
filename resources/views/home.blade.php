<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Dashboard · Washly</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap">

    <link rel="stylesheet" href="{{ asset('user/user.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-v2.css') }}">
</head>

<body class="customer-page wd-body">

@php
    // Ikon garis sederhana (gaya Feather)
    $icons = [
        'shirt'   => '<path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/>',
        'drop'    => '<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>',
        'truck'   => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'check'   => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'clock'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'wind'    => '<path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/>',
        'layers'  => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
    ];
    $ico = fn ($n) => '<svg class="wd-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$icons[$n].'</svg>';
@endphp

    <x-navbar-inside />

    <main class="wd-main">

        @if (session('success'))
            <div class="wd-notice wd-notice--ok" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="wd-notice wd-notice--err" role="alert">{{ $errors->first() }}</div>
        @endif

        {{-- Hero --}}
        <section class="wd-hero">
            <div class="wd-hero__copy">
                <h1>Halo, {{ Str::before(auth()->user()->name, ' ') }}.<br>Cucianmu kami yang urus.</h1>
                <p>Pesan, pantau, dan terima cucian bersih tanpa antre di outlet.</p>
                <div class="wd-hero__actions">
                    <a class="wd-btn wd-btn--white" href="{{ route('laundries.discover') }}">Pesan laundry</a>
                    @if ($activeOrder)
                        <a class="wd-btn wd-btn--ghost" href="{{ route('orders.show', $activeOrder->id) }}">Lacak pesanan</a>
                    @endif
                </div>
            </div>

            <svg class="wd-hero__art" viewBox="0 0 320 260" aria-hidden="true">
                <circle cx="48" cy="60" r="16" class="b1"/><circle cx="84" cy="30" r="8" class="b1"/>
                <circle cx="270" cy="50" r="12" class="b1"/><circle cx="296" cy="96" r="6" class="b1"/>
                <circle cx="30" cy="150" r="7" class="b1"/>
                <rect x="88" y="34" width="150" height="196" rx="20" fill="#fff"/>
                <rect x="88" y="34" width="150" height="40" rx="20" fill="#d8ece6"/>
                <rect x="88" y="58" width="150" height="16" fill="#d8ece6"/>
                <circle cx="112" cy="54" r="6" fill="#00796b"/><circle cx="132" cy="54" r="6" fill="#9ccfc3"/>
                <rect x="196" y="48" width="28" height="12" rx="6" fill="#fff"/>
                <circle cx="163" cy="146" r="56" fill="#0e3a34"/>
                <circle cx="163" cy="146" r="46" fill="#5fd1bd"/>
                <path d="M117 146c12-10 23-10 35 0s23 10 35 0 17-6 23-3a46 46 0 0 1-93 3z" fill="#e8f7f3"/>
                <circle cx="148" cy="130" r="5" fill="#fff" opacity=".7"/><circle cx="178" cy="160" r="7" fill="#fff" opacity=".6"/>
                <rect x="104" y="214" width="30" height="8" rx="4" fill="#d8ece6"/>
            </svg>
            <svg class="wd-hero__wave" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true"><path d="M0 20c100 20 200 20 300 0s200-20 300 0 200 20 300 0 200-20 300 0v20H0z"/></svg>
        </section>

        {{-- Cucian siap --}}
        @if ($readyOrders->isNotEmpty())
            @php $readyOrder = $readyOrders->first(); @endphp
            <aside class="wd-ready">
                <span class="wd-ready__icon">{!! $ico('check') !!}</span>
                <p>
                    <strong>Cucianmu di {{ $readyOrder->store_name }} sudah selesai.</strong><br>
                    Ambil di outlet atau tunggu pengantaran.
                </p>
                <a class="wd-btn wd-btn--dark" href="{{ route('orders.show', $readyOrder->id) }}">Lihat pesanan</a>
            </aside>
        @endif

        {{-- Statistik --}}
        <section class="wd-stats" aria-label="Ringkasan pesanan">
            <article class="wd-stat wd-stat--a">
                <span class="wd-stat__icon">{!! $ico('clock') !!}</span>
                <div><strong>{{ $orderStats->active ?? 0 }}</strong><span>Pesanan aktif</span></div>
            </article>
            <article class="wd-stat wd-stat--b">
                <span class="wd-stat__icon">{!! $ico('drop') !!}</span>
                <div><strong>{{ $orderStats->processing ?? 0 }}</strong><span>Sedang dicuci</span></div>
            </article>
            <article class="wd-stat wd-stat--c">
                <span class="wd-stat__icon">{!! $ico('check') !!}</span>
                <div><strong>{{ $orderStats->completed ?? 0 }}</strong><span>Pesanan selesai</span></div>
            </article>
        </section>

        {{-- Pesanan aktif --}}
        <section class="wd-card wd-focus" aria-label="Pesanan aktif">
            @if ($activeOrder)
                @php
                    $currentStep = match(true) {
                        $activeOrder->status === 'completed' => 3,
                        $activeOrder->weighed_at || $activeOrder->status === 'processing' || in_array($activeOrder->delivery_status, ['picked_up', 'received'], true) => 2,
                        default => 1,
                    };
                    $allDone = $activeOrder->status === 'completed' && $activeOrder->delivery_status === 'returned';
                    $steps = [['Jemput pakaian', 'shirt'], ['Proses mencuci', 'drop'], ['Pengantaran', 'truck']];
                @endphp

                <div class="wd-focus__head">
                    <div>
                        <h2>{{ $activeOrder->service_name }}</h2>
                        <p>{{ $activeOrder->store_name }} · #WSH-{{ str_pad((string) $activeOrder->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="wd-focus__side">
                        <span class="wd-status wd-status--{{ $activeOrder->status }}">
                            @switch($activeOrder->status)
                                @case('processing') Diproses @break
                                @case('completed') Selesai @break
                                @case('pending') Menunggu @break
                                @default {{ ucfirst($activeOrder->status) }}
                            @endswitch
                        </span>
                        <a class="wd-link" href="{{ route('orders.show', $activeOrder->id) }}">Detail dan lacak</a>
                    </div>
                </div>

                <ol class="wd-track">
                    @foreach ($steps as $i => [$label, $icon])
                        @php
                            $n = $i + 1;
                            $isDone = $n < $currentStep || $allDone;
                            $isCurrent = $n === $currentStep && !$allDone;
                        @endphp
                        <li class="wd-track__step {{ $isDone ? 'is-done' : '' }} {{ $isCurrent ? 'is-current' : '' }}" @if ($isCurrent) aria-current="step" @endif>
                            <span class="wd-track__badge">{!! $ico($icon) !!}</span>
                            <span class="wd-track__label">{{ $label }}</span>
                        </li>
                    @endforeach
                </ol>
            @else
                <div class="wd-empty">
                    <span class="wd-empty__icon">{!! $ico('shirt') !!}</span>
                    <div>
                        <h2>Tidak ada pesanan berjalan</h2>
                        <p>Temukan outlet terdekat dan jadwalkan penjemputan.</p>
                    </div>
                    <a class="wd-btn wd-btn--dark" href="{{ route('laundries.discover') }}">Cari outlet</a>
                </div>
            @endif
        </section>

        {{-- Layanan cepat --}}
        <section class="wd-quick" aria-label="Pilih layanan">
            <h2>Mau cuci apa hari ini?</h2>
            <div class="wd-quick__grid">
                <a href="{{ route('laundries.discover') }}" class="wd-tile wd-tile--a">{!! $ico('shirt') !!}<strong>Cuci kiloan</strong><span>Harian, per kilogram</span></a>
                <a href="{{ route('laundries.discover') }}" class="wd-tile wd-tile--b">{!! $ico('wind') !!}<strong>Cuci + setrika</strong><span>Siap pakai</span></a>
                <a href="{{ route('laundries.discover') }}" class="wd-tile wd-tile--c">{!! $ico('layers') !!}<strong>Bedcover & selimut</strong><span>Barang besar</span></a>
                <a href="{{ route('laundries.discover') }}" class="wd-tile wd-tile--d">{!! $ico('truck') !!}<strong>Antar-jemput</strong><span>Tanpa ke outlet</span></a>
            </div>
        </section>

        <div class="wd-lower">
            {{-- Riwayat --}}
            <section class="wd-card wd-history" id="pesan">
                <div class="wd-history__head">
                    <h2>Riwayat pesanan</h2>
                    <a class="wd-link" href="{{ route('profile', ['tab' => 'orders']) }}">Lihat semua</a>
                </div>

                <div class="wd-table-wrap">
                    <table class="wd-table">
                        <thead>
                            <tr>
                                <th scope="col">No. pesanan</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Layanan</th>
                                <th scope="col" class="num">Total</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="wd-table__id">#WSH-{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i') }}</td>
                                    <td>{{ $order->service_name }}</td>
                                    <td class="num">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="wd-status wd-status--{{ $order->status }}">
                                            @switch($order->status)
                                                @case('completed') Selesai @break
                                                @case('processing') Diproses @break
                                                @case('pending') Menunggu @break
                                                @case('cancelled') Dibatalkan @break
                                                @default {{ ucfirst($order->status) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="end"><a class="wd-link" href="{{ route('orders.show', $order->id) }}">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td class="wd-table__empty" colspan="6">Belum ada pesanan. Pesanan pertamamu akan muncul di sini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Promo / tips --}}
            <aside class="wd-promo">
                <svg class="wd-promo__bubbles" viewBox="0 0 200 120" aria-hidden="true"><circle cx="160" cy="30" r="26"/><circle cx="188" cy="76" r="12"/><circle cx="130" cy="92" r="8"/></svg>
                @if ($homePromo)
                    <span class="wd-promo__tag">{{ $homePromo->promo_label ?: 'Promo terbatas' }}</span>
                    <h2>{{ $homePromo->name }}</h2>
                    <p>{{ $homePromo->store_name }}</p>
                    <p class="wd-promo__price">
                        <strong>Rp {{ number_format($homePromo->price, 0, ',', '.') }}</strong>
                        <del>Rp {{ number_format($homePromo->original_price, 0, ',', '.') }}</del>
                    </p>
                    <a class="wd-btn wd-btn--white" href="{{ route('laundry.show', $homePromo->laundry_id) }}?service={{ $homePromo->service_id }}#booking-form">Ambil promo</a>
                @else
                    <span class="wd-promo__tag">Tips</span>
                    <h2>Pisahkan putih dan berwarna</h2>
                    <p>Warna pakaian lebih awet dan tidak berpindah ke kain lain saat dicuci.</p>
                @endif
            </aside>
        </div>

        <p class="wd-help">Ada kendala dengan pesanan? Hubungi laundry langsung dari halaman detail pesanan.</p>
    </main>

    <footer class="wd-footer">
        <a class="wd-footer__brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/washly-logo.png') }}" alt="" width="24" height="24" />
            Washly
        </a>
        <span>© {{ date('Y') }} Washly</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="wd-link wd-link--btn">Keluar</button>
        </form>
    </footer>

    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>