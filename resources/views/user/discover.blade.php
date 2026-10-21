<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pilih Laundry · Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}" />
    <style>
        body {
            background: #faf8ff;
            color: #172033;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }
        .discover-wrap {
            width: min(100% - 40px, 1120px);
            margin: 40px auto;
        }
        .discover-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
        }
        .discover-top h1 {
            margin: 0;
            font-size: 32px;
        }
        .discover-top p {
            margin: 7px 0 0;
            color: #58646a;
        }
        .back-link {
            color: #00796f;
            font-weight: 700;
        }
        .discover-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .discover-card {
            overflow: hidden;
            border: 1px solid #e3e7ff;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 8px 22px #252d3810;
        }
        .discover-card img {
            display: block;
            width: 100%;
            height: 170px;
            object-fit: cover;
            background: #e5f4f2;
        }
        .discover-content {
            padding: 19px;
        }
        .discover-content small {
            color: #78858a;
        }
        .discover-content h2 {
            margin: 7px 0;
            font-size: 21px;
        }
        .discover-content p {
            color: #58646a;
            line-height: 1.5;
        }
        .discover-services {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }
        .discover-services span {
            padding: 6px 9px;
            border-radius: 8px;
            background: #f1f1ff;
            font-size: 12px;
        }
        .discover-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }
        .discover-bottom strong {
            color: #00796f;
        }
        .discover-bottom a,
        .discover-empty a {
            padding: 10px 14px;
            border-radius: 10px;
            color: white;
            background: #00796f;
            font-weight: 700;
        }
        .discover-empty {
            padding: 50px 20px;
            border: 1px dashed #cfd4e8;
            border-radius: 18px;
            background: #fff;
            text-align: center;
        }
        .discover-empty p {
            color: #69767b;
        }
        .discover-empty a {
            display: inline-block;
            margin-top: 10px;
        }
        @media (max-width: 560px) {
            .discover-wrap {
                width: calc(100% - 30px);
                margin: 25px auto;
            }
            .discover-top {
                align-items: flex-start;
                gap: 12px;
                flex-direction: column;
            }
            .discover-top h1 {
                font-size: 27px;
            }
        }
        body.customer-page {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background: #f7f8f5;
            color: #24342d;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }
        .profile-nav {
            position: sticky;
            top: 0;
            z-index: 30;
            width: 100%;
            border-bottom: 1px solid #e7ece8;
            background: #fff;
        }
        .profile-nav-inner {
            display: flex;
            width: min(100% - 40px, 1180px);
            min-height: 76px;
            align-items: center;
            gap: 32px;
            margin: auto;
        }
        .customer-brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #08796f;
            font-size: 22px;
            font-weight: 750;
            letter-spacing: -0.04em;
        }
        .profile-brand-logo {
            width: 42px;
            height: 44px;
            object-fit: contain;
        }
        .profile-nav-links {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px;
            border-radius: 14px;
            background: #f2f2ff;
        }
        .profile-nav-links a {
            padding: 10px 16px;
            border-radius: 10px;
            color: #52605d;
            font-size: 13px;
            font-weight: 650;
            white-space: nowrap;
        }
        .profile-nav-links a:hover {
            color: #00796f;
            background: #e4f4f1;
        }
        .profile-nav-links a.active {
            color: #fff;
            background: #00796f;
        }
        .profile-nav-user {
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
        .nav-user-avatar {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            overflow: hidden;
            border-radius: 50%;
            color: #15564e;
            background: #e1f0e7;
        }
        .nav-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .discover-wrap {
            width: min(100% - 40px, 1180px);
            flex: 1;
            margin: 0 auto;
            padding: 46px 0 58px;
        }
        .discover-top {
            margin-bottom: 24px;
        }
        .discover-top .overline {
            margin: 0 0 8px;
            color: #08796f;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.13em;
        }
        .discover-top h1 {
            margin: 0;
            font-size: 36px;
            letter-spacing: -1.2px;
        }
        .discover-top p {
            margin: 8px 0 0;
            color: #748078;
            font-size: 14px;
        }
        .back-link {
            color: #08796f;
            font-size: 13px;
            font-weight: 700;
        }
        .discover-filter {
            grid-template-columns: minmax(0, 1fr) 230px auto;
            gap: 11px;
            padding: 12px;
            margin-bottom: 19px;
            border-color: #e2e9e4;
            border-radius: 15px;
            box-shadow: 0 5px 18px #24352b08;
        }
        .discover-filter input,
        .discover-filter select {
            min-height: 46px;
            padding: 11px 14px;
            border-color: #dce5df;
            border-radius: 10px;
            font-size: 13px;
        }
        .discover-filter button,
        .discover-bottom a,
        .discover-empty a {
            min-height: 44px;
            padding: 0 19px;
            border-radius: 10px;
            background: #08796f;
            color: #fff;
            font:
                700 13px Inter,
                "Segoe UI",
                Arial,
                sans-serif;
        }
        .discover-meta {
            margin-bottom: 13px;
            color: #718078;
            font-size: 12px;
        }
        .discover-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 350px), 1fr));
            gap: 16px;
        }
        .discover-card {
            border: 1px solid #e2e9e4;
            border-radius: 16px;
            box-shadow: 0 7px 24px #24352b0a;
            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease;
        }
        .discover-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px #24352b12;
        }
        .discover-card > img {
            display: none;
        }
        .discover-content {
            padding: 22px;
        }
        .discover-card-head {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .discover-card-head > img {
            width: 62px;
            height: 62px;
            flex: none;
            object-fit: cover;
            border: 1px solid #e2e9e4;
            border-radius: 14px;
            background: #f4f8f5;
        }
        .discover-content small {
            color: #718078;
            font-size: 11px;
        }
        .discover-content h2 {
            margin: 3px 0 0;
            color: #24342d;
            font-size: 20px;
            letter-spacing: -0.4px;
        }
        .discover-content > p {
            margin: 15px 0;
            color: #68776e;
            font-size: 13px;
            line-height: 1.6;
        }
        .discover-badges {
            gap: 7px;
            margin: 13px 0;
        }
        .discover-badges span {
            padding: 6px 9px;
            background: #eaf5ef;
            color: #08796f;
            font-size: 10px;
        }
        .discover-badges span.rating {
            background: #fff5dc;
            color: #855e00;
        }
        .discover-services {
            gap: 7px;
            margin-top: 13px;
        }
        .discover-services span {
            padding: 7px 10px;
            border: 1px solid #e8ece9;
            border-radius: 8px;
            background: #f8faf8;
            color: #53625a;
            font-size: 11px;
        }
        .discover-bottom {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #edf0ed;
        }
        .discover-bottom strong {
            color: #08796f;
            font-size: 14px;
        }
        .discover-bottom a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .discover-empty {
            border-color: #dce5df;
            color: #34443b;
        }
        .customer-footer {
            width: 100%;
            margin-top: auto;
        }
        .customer-footer .profile-brand-logo {
            width: 34px;
            height: 36px;
        }
        .customer-footer .customer-brand {
            font-size: 18px;
        }
        @media (max-width: 760px) {
            .profile-nav-inner {
                width: calc(100% - 32px);
                flex-wrap: wrap;
                gap: 12px;
                padding: 10px 0;
            }
            .profile-nav-links {
                order: 3;
                width: 100%;
                overflow-x: auto;
            }
            .profile-nav-links a {
                padding: 9px 13px;
            }
            .discover-wrap {
                padding: 32px 0 42px;
            }
            .discover-top h1 {
                font-size: 30px;
            }
        }
        @media (max-width: 560px) {
            .profile-nav-inner {
                width: calc(100% - 30px);
            }
            .customer-brand {
                font-size: 19px;
            }
            .profile-brand-logo {
                width: 36px;
                height: 38px;
            }
            .profile-nav-links a {
                font-size: 11px;
            }
            .profile-nav-user {
                gap: 7px;
                padding: 4px 9px 4px 4px;
                font-size: 12px;
            }
            .nav-user-avatar {
                width: 32px;
                height: 32px;
            }
            .discover-wrap {
                width: calc(100% - 30px);
                padding: 26px 0 36px;
            }
            .discover-top {
                align-items: flex-start;
                gap: 10px;
                flex-direction: column;
            }
            .discover-top h1 {
                font-size: 27px;
            }
            .discover-filter {
                grid-template-columns: 1fr;
            }
            .discover-filter button {
                min-height: 44px;
            }
            .discover-meta {
                align-items: flex-start;
                flex-direction: column;
            }
            .discover-content {
                padding: 17px;
            }
            .discover-card-head > img {
                width: 54px;
                height: 54px;
            }
            .discover-bottom {
                align-items: flex-start;
                flex-direction: column;
            }
            .discover-bottom a {
                width: 100%;
            }
            .customer-footer {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }
        }
        .discover-filter {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(200px, 240px) auto;
            align-items: center;
            gap: 14px;
            padding: 16px;
        }
        .discover-filter input,
        .discover-filter select {
            display: block;
            width: 100%;
            min-width: 0;
            min-height: 50px;
            margin: 0;
            padding: 0 16px;
            border: 1px solid #dce5df;
            border-radius: 11px;
            background: #fff;
            font: inherit;
        }
        .discover-filter input:focus,
        .discover-filter select:focus {
            border-color: #76a991;
            outline: 3px solid #e4f1e8;
        }
        .discover-filter button {
            min-height: 50px;
            margin: 0;
            padding: 0 22px;
            border: 0;
            border-radius: 11px;
            background: #337b70;
            color: #fff;
            font: inherit;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
        }
        .discover-filter button:hover {
            background: #28685e;
        }
        @media (max-width: 760px) {
            .discover-filter {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 14px;
            }
            .discover-filter button {
                width: 100%;
            }
        }
    </style>
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
    <main class="discover-wrap">
        <header class="discover-top">
            <div>
                <p class="overline">TEMUKAN MITRA TERBAIK</p>
                <h1>Pilih laundry di dekatmu</h1>
                <p>Bandingkan layanan dan pilih outlet yang paling sesuai dengan kebutuhanmu.</p>
            </div>
        </header>
        <form
            class="discover-filter"
            method="GET"
            action="{{ route('laundries.discover') }}"
        >
            <input
                name="area"
                value="{{ $area }}"
                placeholder="Cari kota, kecamatan, atau alamat"
            /><select name="sort" aria-label="Urutkan laundry">
                <option value="recommended" @selected ($sort === 'recommended')>
                    Urutan rekomendasi
                </option>
                <option value="price" @selected ($sort === 'price')>
                    Harga termurah
                </option>
                <option value="rating" @selected ($sort === 'rating')>
                    Rating tertinggi
                </option></select
            ><button type="submit">Cari Laundry</button>
        </form>
        <p class="discover-meta">
            <span>{{ $laundries->count() }} mitra ditemukan</span
            ><span>Lokasi cocok berdasarkan area dan alamat outlet</span>
        </p>
        <section class="discover-grid">
            @forelse ($laundries as $laundry)
                <article class="discover-card">
                    <img
                        src="{{ $laundry->logo ? asset('storage/' . $laundry->logo) : asset('images/washly-logo.png') }}"
                        alt="{{ $laundry->store_name }}"
                    />
                    <div class="discover-content">
                        <small
                            >{{ $laundry->kecamatan ?: $laundry->domisili ?: 'Laundry Washly' }}</small
                        >
                        <h2>{{ $laundry->store_name }}</h2>
                        <div class="discover-content">
                            <div class="discover-card-head">
                                <img
                                    src="{{ $laundry->logo ? asset('storage/' . $laundry->logo) : asset('images/washly-logo.png') }}"
                                    alt="Logo {{ $laundry->store_name }}"
                                />
                                <div>
                                    <small
                                        >{{ $laundry->kecamatan ?: $laundry->domisili ?: 'Mitra Washly' }}</small
                                    >
                                    <h2>{{ $laundry->store_name }}</h2>
                                </div>
                            </div>
                            <p>{{ $laundry->address }}</p>
                            <div class="discover-badges">
                                <span>Antar sendiri</span>
                                @if ($laundry->pickup_available)
                                    <span>Antar-jemput tersedia</span>
                                @endif
                                <span class="rating">
                                    ★ {{ $laundry->rating_count ? number_format($laundry->rating_average, 1) : 'Belum ada rating' }}
                                    <span class="rating"
                                        >★ {{ $laundry->rating_count ? number_format($laundry->rating_average, 1) : 'Belum ada rating' }}
                                        @if ($laundry->rating_count)
                                            ({{ $laundry->rating_count }})
                                        @endif
                                    </span>
                            </div>
                            <div class="discover-services">
                                @forelse ($laundry->services->take(3) as $service)
                                    <span>{{ $service->name }}</span>
                                @empty
                                    <span>Lihat layanan tersedia</span>
                                @endforelse
                            </div>
                            <div class="discover-bottom">
                                <strong>
                                    @if ($laundry->starting_price !== null)
                                        Mulai Rp {{ number_format($laundry->starting_price, 0, ',', '.') }}
                                    @else
                                        Lihat layanan
                                    @endif
                                </strong>
                                <a
                                    href="{{ route('laundry.show', $laundry->id) }}"
                                    >Pilih Laundry</a
                                >
                            </div>
                        </div>
                </article>
            @empty
                <div class="discover-empty">
                    <h2>Belum ada laundry tersedia</h2>
                    <p>Laundry aktif akan muncul di sini setelah mendaftar.</p>
                    <a href="{{ route('dashboard') }}">Kembali ke dashboard</a>
                </div>
            @endforelse
        </section>
    </main>
    <footer class="customer-footer">
        <a class="customer-brand" href="{{ route('dashboard') }}"
            ><img
                class="profile-brand-logo"
                src="{{ asset('images/washly-logo.png') }}"
                alt=""
            />Washly</a
        ><span>Jasa laundry lokal dalam satu tempat.</span
        ><a href="{{ route('profile') }}">Akun saya</a>
    </footer>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
