<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
        name="description"
        content="Washly, layanan laundry praktis untuk pakaian bersih, wangi, dan terawat."
    />
    <title>Washly — Laundry Praktis &amp; Wangi</title>
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/general.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />
</head>

<body class="welcome-page">
    @php ($bookingUrl = auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'owner' ? route('owner.dashboard') : route('dashboard'))) : route('register'))
    <header class="topbar">
        <nav class="nav wrap" aria-label="Navigasi utama">
            <a class="brand" href="#home"
                ><img
                    class="brand-logo"
                    src="{{ asset('images/washly-logo.png') }}"
                    alt=""
                />Washly</a
            >
            <div class="navlinks">
                <a href="#home">Beranda</a
                ><a href="#keunggulan">Kenapa Washly</a
                ><a href="#layanan">Layanan</a><a href="#kontak">Kontak</a>
            </div>
            <div class="navright">
                @guest
                    <a class="login" href="{{ route('login') }}">Masuk</a
                    ><a class="register" href="{{ route('register') }}"
                        >Daftar</a
                    >
                @else
                    <a
                        class="login"
                        href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'owner' ? route('owner.dashboard') : route('dashboard')) }}"
                        >Dashboard</a
                    >
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="register" type="submit">Keluar</button>
                    </form>
                @endguest
            </div>
        </nav>
    </header>
    <main>
        <section class="hero" id="home">
            <div class="wrap hero-grid">
                <div>
                    <span class="eyebrow"
                        ><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m12 3 1.8 6.2L20 11l-6.2 1.8L12 19l-1.8-6.2L4 11l6.2-1.8L12 3Z" />
                            <path d="m19 14 .9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14Z" />
                        </svg
                        >&nbsp; Solusi Laundry Praktis &amp; Wangi</span
                    >
                    <h1>Cucian Bersih,<br /><span>Hati Lebih Tenang!</span></h1>
                    <p>Layanan laundry praktis untuk pakaian bersih dan wangi setiap hari. Tanpa repot, wangi tahan lama, dan terjamin higienis.</p>
                    <div class="actions">
                        <a
                            class="btn btn-primary"
                            href="{{ route('register') }}"
                            >Daftar untuk Memesan &nbsp;
                            <svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5v14M5 12l7 7 7-7" /></svg></a
                        ><a
                            class="btn btn-soft"
                            href="https://wa.me/6281234567890"
                            ><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 11.5a8.2 8.2 0 0 1-12.1 7.2L4 20l1.3-3.8A8.2 8.2 0 1 1 20 11.5Z" />
                                <path d="M9 8.5c.3-.6.6-.6 1-.6h.5l.8 1.9-.8 1c.7 1.4 1.7 2.4 3.2 3.1l.9-.9 1.9.9v.5c0 .4-.1.7-.6 1-1 .5-2.7 0-4.7-1.6-1.7-1.4-2.6-3.1-2.6-4.2 0-.5.2-.8.4-1.1Z" />
                            </svg
                            >&nbsp; Hubungi WhatsApp</a
                        >
                    </div>
                    <div class="proof">
                        <span>Detergen Ramah Serat</span
                        ><span>1 Mesin 1 Pelanggan</span
                        ><span>Tepat Waktu</span>
                    </div>
                </div>
                <div class="hero-photo">
                    <img
                        src="https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?auto=format&fit=crop&w=1100&q=85"
                        alt="Tumpukan handuk dan pakaian bersih di keranjang laundry"
                    />
                    <div class="photo-tag">
                        <span class="tag-icon"
                            ><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m12 3 1.8 6.2L20 11l-6.2 1.8L12 19l-1.8-6.2L4 11l6.2-1.8L12 3Z" />
                                <path d="m19 14 .9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14Z" />
                            </svg></span
                        ><span
                            ><b>Bersih, Rapi &amp; Siap Pakai</b>Higienis &amp;
                            Wangi Sepanjang Hari</span
                        >
                    </div>
                </div>
            </div>
        </section>
        @guest
            <section class="partner-banner">
                <div class="wrap partner-banner-inner">
                    <div>
                        <h2>Punya usaha laundry?</h2>
                        <p>Bergabung dengan Washly dan kelola layanan laundry kamu melalui dashboard mitra.</p>
                    </div>
                    <a class="btn" href="{{ route('register-owner') }}"
                        >Buka Kemitraan Laundry</a
                    >
                </div>
            </section>
        @endguest
        <section class="section" id="keunggulan">
            <div class="wrap">
                <div class="section-head">
                    <span class="eyebrow">Kenapa Washly?</span>
                    <h2>Laundry Digital yang Bikin Urusan Lebih Praktis</h2>
                    <p>Pesan layanan laundry dengan lebih mudah, pilih layanan sesuai kebutuhan, dan nikmati proses yang lebih tertata.</p>
                </div>
                <div class="why-grid">
                    <article class="why-card">
                        <span class="why-icon"
                            ><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="4" y="3" width="16" height="18" rx="2" />
                                <path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h8" />
                            </svg
                        ></span>
                        <h3>Pemesanan lebih praktis</h3>
                        <p>Ajukan pesanan secara online tanpa perlu repot datang dulu ke outlet.</p>
                    </article>
                    <article class="why-card">
                        <span class="why-icon"
                            ><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 5h16v14H4zM8 9h8M8 13h5" />
                                <path d="m16 16 2 2 3-4" />
                            </svg
                        ></span>
                        <h3>Pilihan layanan jelas</h3>
                        <p>Kenali jenis layanan, estimasi pengerjaan, dan harga sebelum menentukan pesanan.</p>
                    </article>
                    <article class="why-card">
                        <span class="why-icon"
                            ><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.5" />
                                <path d="M4 4v4.5h4.5M12 7v5l3 2" />
                            </svg
                        ></span>
                        <h3>Proses lebih tertata</h3>
                        <p>Pesanan tercatat secara digital agar komunikasi dan pengelolaan laundry terasa lebih mudah.</p>
                    </article>
                </div>
            </div>
        </section>
        <section class="section tint services-section" id="layanan">
            <div class="wrap">
                <div class="section-head">
                    <span class="eyebrow">Layanan Kami</span>
                    <h2>Pilihan Layanan Sesuai Kebutuhan Anda</h2>
                    <p>Kualitas cuci terbaik dengan standar higienis dan harga transparan untuk kenyamanan seluruh keluarga.</p>
                </div>
                <div class="floating-row">
                    <a class="floating" href="{{ $bookingUrl }}"
                        ><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 10h18l-1.5 10h-15L3 10Z" />
                            <path d="M3 10l2-5h14l2 5" />
                        </svg
                        >Pesan Laundry Sekarang</a
                    >
                </div>
                <div class="service-grid">
                    <article class="service">
                        <span class="service-icon"
                            ><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 10h18l-1.5 10h-15L3 10Z" />
                                <path d="M3 10l2-5h14l2 5M7 14h.01M12 14h.01M17 14h.01" />
                            </svg
                        ></span>
                        <h3>Cuci Kiloan</h3>
                        <p>Pilihan hemat untuk pakaian harian, seragam, dan pakaian santai. Dicuci bersih, dikeringkan, dan dilipat rapi.</p>
                        <div class="price">
                            <small>TARIF HEMAT</small><b>Mulai Rp7.000</b>
                            <span>/kg</span>
                        </div>
                        <ul class="features">
                            <li>Estimasi pengerjaan 2–3 hari</li>
                            <li>Detergen premium wangi tahan lama</li>
                            <li>Dipisah per pelanggan (anti-campur)</li>
                        </ul>
                        <a class="btn" href="{{ $bookingUrl }}"
                            >Pesan Cuci Kiloan</a
                        >
                    </article>
                    <article class="service featured">
                        <span class="popular"
                            ><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z" /></svg
                            >&nbsp; Paling Laris</span
                        ><span class="service-icon"
                            ><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 17h18l-1.6-7H9a6 6 0 0 0-6 6v1Z" />
                                <path d="m9 10-1.5-5h6l2 5M6 14h.01" />
                            </svg
                        ></span>
                        <h3>Cuci Setrika</h3>
                        <p>Paket komplit pakaian dicuci bersih higienis dan disetrika uap rapi, siap langsung masuk lemari pakaian.</p>
                        <div class="price">
                            <small>TARIF LENGKAP</small><b>Mulai Rp10.000</b>
                            <span>/kg</span>
                        </div>
                        <ul class="features">
                            <li>Estimasi selesai 2 hari kerja</li>
                            <li>Setrika uap anti kusut &amp; higienis</li>
                            <li>Pakaian langsung rapi &amp; wangi segar</li>
                        </ul>
                        <a class="btn btn-primary" href="{{ $bookingUrl }}"
                            >Pilih Cuci Setrika</a
                        >
                    </article>
                    <article class="service">
                        <span class="service-icon"
                            ><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m13 2-3 9h7L9 22l2-9H5l8-11Z" /></svg
                        ></span>
                        <h3>Express</h3>
                        <p>Layanan kilat prioritas untuk kebutuhan mendesak. Selesai cepat dengan kualitas kebersihan tetap maksimal.</p>
                        <div class="price">
                            <small>TARIF KILAT</small><b>Mulai Rp15.000</b>
                            <span>/kg</span>
                        </div>
                        <ul class="features">
                            <li>Selesai dalam 6–8 jam saja</li>
                            <li>Prioritas antrian mesin khusus</li>
                            <li>Cocok untuk urusan darurat atau bepergian</li>
                        </ul>
                        <a class="btn" href="{{ $bookingUrl }}"
                            >Pesan Layanan Express</a
                        >
                    </article>
                </div>
        </section>
    </main>
    <footer class="footer">
        <div class="wrap">
            <div class="footer-grid">
                <div>
                    <a class="brand" href="#home"
                        ><img
                            class="brand-logo"
                            src="{{ asset('images/washly-logo.png') }}"
                            alt=""
                        />Washly</a
                    >
                    <p>Layanan laundry keluarga yang praktis dan terpercaya untuk pakaian bersih, higienis, dan rapi setiap hari.</p>
                </div>
                <div>
                    <h4>Menu Cepat</h4>
                    <div class="footer-links">
                        <a href="#home">Beranda</a><a href="#layanan">Layanan</a
                        ><a href="#kontak">Kontak</a>
                    </div>
                </div>
                <div>
                    <h4>Hubungi Kami</h4>
                    <p>Siap jemput pakaian kotor di depan pintu rumah Anda.</p>
                    <a href="https://wa.me/6285187803376"
                    <svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 11.5a8.2 8.2 0 0 1-12.1 7.2L4 20l1.3-3.8A8.2 8.2 0 1 1 20 11.5Z" />
                        <path d="M9 8.5c.3-.6.6-.6 1-.6h.5l.8 1.9-.8 1c.7 1.4 1.7 2.4 3.2 3.1l.9-.9 1.9.9v.5c0 .4-.1.7-.6 1-1 .5-2.7 0-4.7-1.6-1.7-1.4-2.6-3.1-2.6-4.2 0-.5.2-.8.4-1.1Z" />
                    </svg>
                    &nbsp; WhatsApp CS Washly</a>
                </div>
            </div>
            <div class="copyright">
                <span>© {{ date('Y') }} Washly. Hak Cipta Dilindungi.</span
                ><span>Solusi Laundry Keluarga &amp; Profesional Modern</span>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
