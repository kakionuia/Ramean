<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $laundry->store_name }} · Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}" />
    <style>
        body.detail-page {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background: #f7f8f5;
            color: #24342d;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }
        .detail-page .profile-nav {
            position: sticky;
            top: 0;
            z-index: 30;
            width: 100%;
            border-bottom: 1px solid #e7ece8;
            background: #fff;
        }
        .detail-page .profile-nav-inner {
            display: flex;
            width: min(100% - 40px, 1180px);
            min-height: 76px;
            align-items: center;
            gap: 32px;
            margin: auto;
        }
        .detail-page .customer-brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #08796f;
            font-size: 22px;
            font-weight: 750;
            letter-spacing: -0.04em;
        }
        .detail-page .profile-brand-logo {
            width: 42px;
            height: 44px;
            object-fit: contain;
        }
        .detail-page .profile-nav-links {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px;
            border-radius: 14px;
            background: #f2f2ff;
        }
        .detail-page .profile-nav-links a {
            padding: 10px 16px;
            border-radius: 10px;
            color: #52605d;
            font-size: 13px;
            font-weight: 650;
            white-space: nowrap;
        }
        .detail-page .profile-nav-links a:hover {
            color: #00796f;
            background: #e4f4f1;
        }
        .detail-page .profile-nav-links a.active {
            color: #fff;
            background: #00796f;
        }
        .detail-page .profile-nav-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            padding: 6px 12px 6px 6px;
            border: 1px solid #e7ece8;
            border-radius: 30px;
            color: #25332e;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
        }
        .detail-page .nav-user-avatar {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            overflow: hidden;
            border-radius: 50%;
            color: #15564e;
            background: #e1f0e7;
        }
        .detail-page .nav-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .detail-page .detail-main {
            position: relative;
            width: min(100% - 40px, 1180px);
            flex: 1;
            margin: 0 auto;
            padding: 14px 0 48px;
        }
        .detail-page .breadcrumbs {
            min-height: 0;
            margin-bottom: 18px;
            font-size: 12px;
        }
        .detail-page .detail-back-link {
            position: fixed;
            top: 96px;
            left: clamp(14px, calc((100vw - 1180px) / 2 - 62px), 40px);
            z-index: 40;
            display: grid;
            width: 54px;
            height: 54px;
            place-items: center;
            border: 1px solid #08796f;
            border-radius: 14px;
            color: #fff;
            background: #08796f;
            box-shadow: 0 5px 16px #24352b18;
            transition:
                background 0.15s,
                border-color 0.15s,
                box-shadow 0.15s;
        }
        .detail-page .detail-back-link svg {
            display: block;
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .detail-page .detail-back-link:hover {
            border-color: #00645d;
            background: #00645d;
            box-shadow: 0 7px 18px #24352b20;
        }
        .detail-page .detail-back-link:focus-visible {
            outline: 3px solid #8ad5c5;
            outline-offset: 3px;
        }
        .detail-page .detail-cover {
            min-height: 300px;
            padding: 34px;
            border-radius: 18px;
            background-image:
                linear-gradient(90deg, rgba(15, 54, 46, 0.88), rgba(15, 54, 46, 0.12)), var(--laundry-image);
        }
        .detail-page .cover-kicker {
            font-size: 10px;
        }
        .detail-page .detail-cover h1 {
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            font-size: clamp(32px, 4vw, 46px);
            font-weight: 750;
            letter-spacing: -1.2px;
        }
        .detail-page .detail-cover p {
            font-size: 13px;
            line-height: 1.6;
        }
        .detail-page .detail-columns {
            grid-template-columns: minmax(250px, 0.72fr) minmax(0, 1.28fr);
            gap: 24px;
            padding-top: 24px;
        }
        .detail-page .detail-about,
        .detail-page .detail-services {
            align-self: start;
            padding: 23px;
            border: 1px solid #e2e9e4;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 20px #24352b08;
        }
        .detail-page .detail-about h2,
        .detail-page .detail-services .section-heading h2,
        .detail-page .booking-intro h2 {
            color: #24342d;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            font-size: 23px;
            font-weight: 750;
            letter-spacing: -0.55px;
        }
        .detail-page .detail-about h2 {
            max-width: none;
            margin: 9px 0;
        }
        .detail-page .detail-about > p:not(.overline) {
            font-size: 13px;
            line-height: 1.7;
        }
        .detail-page .detail-facts {
            gap: 15px;
            margin-top: 18px;
            padding-top: 17px;
        }
        .detail-page .detail-facts small {
            font-size: 10px;
        }
        .detail-page .detail-facts strong {
            font-size: 12px;
        }
        .detail-page .detail-service-list {
            gap: 12px;
        }
        .detail-page .detail-service-card {
            grid-template-columns: 76px minmax(0, 1fr) auto;
            gap: 14px;
            padding: 14px;
            border-color: #e2e9e4;
            border-radius: 12px;
        }
        .detail-page .service-card-photo {
            width: 76px;
            height: 76px;
            border-radius: 10px;
        }
        .detail-page .service-card-heading h3 {
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            font-weight: 700;
        }
        .detail-page .service-card-copy p {
            font-size: 11px;
            line-height: 1.5;
            white-space: normal;
        }
        .detail-page .service-card-copy > small {
            font-size: 10px;
        }
        .detail-page .service-price strong {
            color: #08796f;
            font-size: 13px;
        }
        .detail-page .primary-button {
            min-height: 40px;
            border-radius: 9px;
            background: #08796f;
            font-size: 12px;
        }
        .detail-page .primary-button:hover {
            background: #00645d;
        }
        .detail-page .booking-section {
            margin-top: 24px;
            padding: 25px;
            border: 1px solid #e2e9e4;
            border-radius: 16px;
            background: #fff;
        }
        .detail-page .booking-intro p:not(.overline) {
            max-width: 700px;
            color: #718078;
            font-size: 13px;
            line-height: 1.7;
        }
        .detail-page .customer-footer {
            width: 100%;
            margin-top: auto;
        }
        .detail-page .customer-footer .customer-brand {
            font-size: 18px;
        }
        .detail-page .customer-footer .profile-brand-logo {
            width: 34px;
            height: 36px;
        }
        @media (max-width: 800px) {
            .detail-page .profile-nav-inner {
                width: calc(100% - 32px);
                flex-wrap: wrap;
                gap: 12px;
                padding: 10px 0;
            }
            .detail-page .profile-nav-links {
                order: 3;
                width: 100%;
                overflow-x: auto;
            }
            .detail-page .profile-nav-links a {
                padding: 9px 13px;
            }
            .detail-page .detail-columns {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
        @media (max-width: 560px) {
            .detail-page .profile-nav-inner {
                width: calc(100% - 30px);
            }
            .detail-page .customer-brand {
                font-size: 19px;
            }
            .detail-page .profile-brand-logo {
                width: 36px;
                height: 38px;
            }
            .detail-page .profile-nav-user {
                gap: 7px;
                padding: 4px 9px 4px 4px;
                font-size: 12px;
            }
            .detail-page .nav-user-avatar {
                width: 32px;
                height: 32px;
            }
            .detail-page .profile-nav-links a {
                font-size: 11px;
            }
            .detail-page .detail-main {
                width: calc(100% - 30px);
                padding-top: 10px;
            }
            .detail-page .breadcrumbs {
                margin-bottom: 14px;
            }
            .detail-page .detail-back-link {
                top: 136px;
                left: 12px;
                width: 46px;
                height: 46px;
                border-radius: 12px;
            }
            .detail-page .detail-back-link svg {
                width: 20px;
                height: 20px;
            }
            .detail-page .detail-cover {
                min-height: 235px;
                padding: 23px;
            }
            .detail-page .detail-cover h1 {
                font-size: 32px;
            }
            .detail-page .detail-about,
            .detail-page .detail-services {
                padding: 18px;
            }
            .detail-page .detail-service-card {
                grid-template-columns: 56px minmax(0, 1fr);
                gap: 10px;
            }
            .detail-page .service-card-photo {
                width: 56px;
                height: 62px;
            }
            .detail-page .service-order-button {
                grid-column: 2;
                justify-self: start;
            }
            .detail-page .booking-section {
                padding: 18px;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />
</head>

<body class="customer-page detail-page">
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
                ><a class="active" href="{{ route('laundries.discover') }}"
                    >Pesan Laundry</a
                ><a href="{{ route('profile', ['tab' => 'orders']) }}"
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
    <main class="detail-main">
        <nav class="breadcrumbs" aria-label="Navigasi kembali">
            <a
                class="detail-back-link"
                href="{{ route('laundries.discover') }}"
                aria-label="Kembali ke Pesan Laundry"
                title="Kembali ke Pesan Laundry"
                ><svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19 12H5" />
                    <path d="m12 19-7-7 7-7" />
                </svg
            ></a>
        </nav>
        <section
            class="detail-cover"
            style="--laundry-image: url('{{ $laundry->logo ? asset('storage/' . $laundry->logo) : 'https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?auto=format&fit=crop&w=1600&q=85' }}')"
        >
            <span class="cover-kicker">LAUNDRY DI SEKITARMU</span>
            <h1>{{ $laundry->store_name }}</h1>
            <p>{{ $laundry->address }}</p>
        </section>

        @if (session('success'))
            <div class="customer-notice">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="customer-notice notice-error">
                {{ $errors->first() }}
            </div>
        @endif
        @if (($laundry->operational_status ?? 'open') !== 'open')
            <div class="customer-notice notice-error">
                Outlet sedang tutup sementara dan belum menerima pesanan.
            </div>
        @endif

        <div class="detail-columns">
            <section class="detail-about">
                <p class="overline">TENTANG TEMPAT INI</p>
                <h2>Rawat pakaianmu dengan lebih mudah.</h2>
                <p>{{ $laundry->description ?: 'Laundry lokal yang siap membantu kebutuhan perawatan pakaianmu.' }}</p>
                <div class="detail-facts">
                    <span
                        ><small>ALAMAT</small
                        ><strong>{{ $laundry->address }}</strong></span
                    ><span
                        ><small>TELEPON</small
                        ><strong>{{ $laundry->store_phone }}</strong></span
                    ><span
                        ><small>PEMILIK</small
                        ><strong>{{ $laundry->owner_name }}</strong></span
                    >
                </div>
            </section>
            <section class="detail-services" aria-labelledby="servicesHeading">
                <div class="section-heading">
                    <div>
                        <p class="overline">PILIHAN PERAWATAN</p>
                        <h2 id="servicesHeading">Layanan tersedia</h2>
                    </div>
                </div>
                <div class="detail-service-list">
                    @forelse ($services as $service)
                        <article class="detail-service-card">
                            <div
                                class="service-card-photo"
                                role="img"
                                aria-label="Pakaian bersih"
                            ></div>
                            <div class="service-card-copy">
                                <div class="service-card-heading">
                                    <h3>{{ $service->name }}</h3>
                                    @if (
                                        $service->original_price &&
                                            $service->original_price > $service->price &&
                                            (! $service->promo_until || $service->promo_until >= today()->toDateString()))
                                        <span
                                            class="package-tag"
                                            >{{ $service->promo_label ?: 'PROMO' }}</span
                                        >
                                    @endif
                                </div>
                                <span
                                    class="package-tag"
                                    >{{ $service->category }}</span
                                >
                                <p>{{ $service->description ?: 'Perawatan pakaian oleh ' . $laundry->store_name }}</p>
                                <small
                                    >Estimasi {{ $service->duration_hours }} jam</small
                                >
                                <div class="service-price">
                                    <strong
                                        >Rp {{ number_format($service->price, 0, ',', '.') }}
                                        <small
                                            >{{ $service->unit }}</small
                                        ></strong
                                    >
                                    @if ($service->original_price && $service->original_price > $service->price)
                                        <del
                                            >Rp {{ number_format($service->original_price, 0, ',', '.') }}</del
                                        >
                                    @endif
                                </div>
                            </div>
                            @if (($laundry->operational_status ?? 'open') === 'open')
                                <button
                                    class="primary-button service-order-button"
                                    type="button"
                                    data-order-service="{{ $service->id }}"
                                >
                                    Pesan
                                </button>
                            @else
                                <span class="package-tag">Sementara tutup</span>
                            @endif
                        </article>
                    @empty
                        <div class="empty-discovery">
                            <span>&#9672;</span>
                            <h3>Belum ada layanan aktif</h3>
                            <p>Laundry ini belum menambahkan layanan untuk dipesan.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        @if ($services->isNotEmpty())
            <section class="booking-section" id="booking-form" hidden>
                <div class="booking-intro">
                    <p class="overline">PESAN SEKARANG</p>
                    <h2>Detail cucianmu</h2>
                    <p>Pilih kebutuhan cucian dan cara penyerahan. Laundry akan menimbang cucian saat diterima sebelum tagihan dibuat.</p>
                </div>
                <form
                    class="booking-form"
                    action="{{ route('orders.store', $laundry->id) }}"
                    method="POST"
                >
                    @csrf
                    <label
                        >Paket layanan<select
                            name="service_id"
                            id="serviceSelect"
                            required
                        >
                            @foreach ($services as $service)
                                <option
                                    value="{{ $service->id }}"
                                    data-price="{{ $service->price }}"
                                    @selected ((string) request('service') === (string) $service->id)
                                >
                                    {{ $service->name }} &middot; Rp {{ number_format($service->price, 0, ',', '.') }} {{ $service->unit }}
                                </option>
                            @endforeach</select
                    ></label>
                    <div class="form-grid">
                        <label
                            >Jenis pakaian<select name="garment_type" required>
                                <option value="kiloan">Pakaian kiloan</option>
                                <option value="pakaian">Pakaian satuan</option>
                                <option value="selimut">Selimut</option>
                                <option value="sepatu">Sepatu</option>
                                <option value="lainnya">Lainnya</option>
                            </select></label
                        ><label
                            >Jenis jasa cuci<select name="wash_type" required>
                                <option value="cuci">Cuci</option>
                                <option value="setrika">Setrika</option>
                                <option value="cuci_setrika">
                                    Cuci dan setrika
                                </option>
                            </select></label
                        >
                    </div>
                    <fieldset class="delivery-choice">
                        <legend>Cara penyerahan cucian</legend>
                        <label class="delivery-option"
                            ><input
                                type="radio"
                                name="delivery_method"
                                value="dropoff"
                                data-pickup-fee="0"
                                checked
                            /><span
                                ><strong>Antar sendiri</strong
                                ><small
                                    >Drop-off langsung ke tempat laundry</small
                                ></span
                            ><b>Rp0</b></label
                        >
                        @if ($laundry->pickup_available)
                            <label class="delivery-option"
                                ><input
                                    type="radio"
                                    name="delivery_method"
                                    value="pickup"
                                    data-pickup-fee="{{ $pickupFee }}"
                                /><span
                                    ><strong>Jemput cucian</strong
                                    ><small
                                        >Kurir laundry menjemput ke
                                        alamatmu</small
                                    ></span
                                ><b
                                    >+ Rp {{ number_format($pickupFee, 0, ',', '.') }}</b
                                ></label
                            >
                        @endif
                    </fieldset>
                    <label
                        >Catatan untuk laundry<input
                            type="text"
                            name="notes"
                            maxlength="500"
                            placeholder="Contoh: alamat penjemputan, patokan, atau pisahkan pakaian putih"
                    /></label>
                    <div class="booking-footer">
                        <p>
                            <strong data-rate-preview
                                >Rp {{ number_format($services->firstWhere('id', request('service'))?->price ?? $services->first()->price, 0, ',', '.') }} /
                                kg</strong
                            ><br />Tagihan final dihitung owner setelah cucian
                            ditimbang.
                        </p>
                        <button class="primary-button" type="submit">
                            Ajukan cucian <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </form>
            </section>
        @endif
    </main>

    <footer class="customer-footer">
        <a class="customer-brand" href="{{ route('dashboard') }}"
            ><img
                class="profile-brand-logo"
                src="{{ asset('images/washly-logo.png') }}"
                alt=""
            />Washly</a
        ><span>Jasa laundry lokal dalam satu tempat.</span
        ><a href="{{ route('profile', ['tab' => 'orders']) }}"
            >Riwayat pesanan</a
        >
    </footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
