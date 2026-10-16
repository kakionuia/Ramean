<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Washly, layanan laundry praktis untuk pakaian bersih, wangi, dan terawat.">
    <title>Washly — Laundry Praktis &amp; Wangi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root{--ink:#101827;--muted:#65717b;--teal:#006b61;--mint:#c9f7ef;--pale:#f3f6ff;--line:#e9edf3;--white:#fff}
        *{box-sizing:border-box}html{scroll-behavior:smooth;scroll-padding-top:76px}body{margin:0;color:var(--ink);font-family:'DM Sans',Arial,sans-serif;font-size:14px}a{color:inherit;text-decoration:none}.wrap{width:min(1200px,calc(100% - 40px));margin:auto}
        .topbar{position:sticky;top:0;z-index:1000;height:64px;border-bottom:1px solid #f0f2f5;background:#fff}.nav{height:100%;display:flex;align-items:center;gap:34px}.brand{display:flex;align-items:center;gap:10px;font-size:19px;font-weight:700}.brand-logo{display:block;width:42px;height:46px;object-fit:contain}.footer .brand-logo{width:34px;height:38px}.navlinks{display:flex;gap:25px;color:#45515b;font-size:13px}.navlinks a:hover,.footer a:hover{color:var(--teal)}.navright{margin-left:auto;display:flex;align-items:center;gap:10px}.navright form{margin:0}.navright button{border:0;cursor:pointer;font-family:inherit}.login,.register{padding:10px 17px;border-radius:10px;font-size:12px;font-weight:600}.login{background:#f0f4ff;color:#135a67}.register{background:var(--teal);color:#fff}
        .hero{padding:42px 0 50px}.hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center}.eyebrow{display:inline-flex;align-items:center;gap:7px;color:var(--teal);background:var(--mint);padding:6px 12px;border-radius:30px;font-size:11px}.hero h1{font-size:clamp(34px,4vw,46px);line-height:1.12;letter-spacing:-1.6px;margin:18px 0 14px}.hero h1 span{color:var(--teal)}.hero p{color:var(--muted);line-height:1.8;max-width:480px;margin:0 0 22px}.actions{display:flex;gap:12px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;border-radius:9px;font-size:13px;font-weight:600}.btn-primary{background:var(--teal);color:white}.btn-soft{background:#eff3ff;color:#125c67}.proof{display:flex;gap:19px;flex-wrap:wrap;margin-top:24px;color:#57636a;font-size:10px}.proof span:before{content:'✓';color:var(--teal);margin-right:5px}.hero-photo{position:relative;height:540px;border-radius:22px;overflow:hidden;background:#e9e1d6}.hero-photo img{width:100%;height:100%;object-fit:cover}.photo-tag{position:absolute;bottom:18px;left:18px;display:flex;align-items:center;gap:10px;background:white;border-radius:11px;padding:10px 14px;font-size:10px;box-shadow:0 5px 20px #0002}.photo-tag b{display:block;font-size:11px}.tag-icon{color:var(--teal);background:var(--mint);width:30px;height:30px;border-radius:50%;display:grid;place-items:center}
        .section{padding:48px 0}.tint{background:var(--pale)}.section-head{text-align:center;margin:0 auto 30px;max-width:620px}.section-head .eyebrow{font-size:10px;padding:5px 11px}.section-head h2{font-size:30px;letter-spacing:-.8px;margin:9px 0 5px}.section-head p{color:var(--muted);font-size:12px;line-height:1.6;margin:0}.service-grid,.steps-grid,.contact-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.service{position:relative;background:#fff;border:1px solid #edf0f4;border-radius:15px;padding:27px 30px 25px;min-height:365px;display:flex;flex-direction:column}.service.featured{border-color:var(--teal);box-shadow:0 5px 16px #063f3510}.popular{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--teal);color:#fff;border-radius:20px;padding:5px 14px;font-size:10px;white-space:nowrap}.service-icon,.step-icon,.contact-icon{width:40px;height:40px;border-radius:11px;display:grid;place-items:center;background:#e5efff;color:var(--teal);font-size:18px}.landing-icon{width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;vertical-align:middle;flex:none}.landing-icon.small{width:16px;height:16px}.service-icon .landing-icon,.step-icon .landing-icon,.contact-icon .landing-icon{width:23px;height:23px}.service.featured .service-icon,.step-icon{background:var(--teal);color:#fff}.service h3{font-size:18px;margin:18px 0 7px}.service p{font-size:12px;color:var(--muted);line-height:1.65;margin:0 0 15px}.price{background:#f4f7ff;border-radius:10px;padding:11px 13px;margin-bottom:14px}.price small{display:block;color:#73808a;font-size:9px;letter-spacing:.5px}.price b{font-size:16px;color:var(--teal)}.price span{font-size:10px;color:var(--muted)}.features{list-style:none;padding:0;margin:0 0 18px;color:#647078;font-size:10px;line-height:1.8}.features li:before{content:'✓';color:var(--teal);margin-right:7px}.service .btn{margin-top:auto;padding:10px;font-size:11px}.service:not(.featured) .btn{background:#eff3ff;color:var(--teal)}.floating-row{display:flex;justify-content:flex-end;margin:-10px 0 18px}.floating{display:inline-flex;align-items:center;justify-content:center;gap:7px;background:var(--teal);color:#fff;border-radius:24px;padding:11px 17px;font-size:12px;font-weight:600;box-shadow:0 5px 14px #006b6126;transition:background .2s,transform .2s,box-shadow .2s}.floating:hover{background:#00584f;transform:translateY(-2px);box-shadow:0 8px 18px #006b6133}.floating:focus-visible{outline:3px solid #84ddd0;outline-offset:3px}
        .step{background:var(--pale);border-radius:15px;padding:26px 28px;min-height:205px}.step-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}.step-label{font-size:9px;letter-spacing:.5px;color:var(--teal);background:var(--mint);border-radius:20px;padding:5px 9px}.step h3{font-size:14px;margin:0 0 8px}.step p{font-size:12px;color:var(--muted);line-height:1.7;margin:0}
        .contact-card{background:#fff;border-radius:15px;padding:25px 27px;min-height:195px}.contact-icon{margin-bottom:15px}.contact-card h3{font-size:14px;margin:0 0 7px}.contact-card p{color:var(--muted);font-size:11px;line-height:1.7;margin:0 0 12px}.contact-card a{color:var(--teal);font-size:10px;font-weight:600}
        .partner-banner{padding:0 0 40px}.partner-banner-inner{display:flex;align-items:center;justify-content:space-between;gap:22px;padding:22px 26px;border:1px solid #d9e9e6;border-radius:16px;background:linear-gradient(110deg,#f0fbf9,#f3f6ff)}.partner-banner h2{margin:0 0 5px;font-size:18px;letter-spacing:-.35px}.partner-banner p{margin:0;color:var(--muted);font-size:12px;line-height:1.6}.partner-banner .btn{flex:none;background:var(--teal);color:#fff}
        .footer{padding:48px 0 20px;border-top:1px solid #e8edf0;background:linear-gradient(180deg,#fbfcff 0%,#f3f6fb 100%)}.footer-grid{display:grid;grid-template-columns:minmax(0,1.8fr) minmax(130px,.8fr) minmax(220px,1fr);gap:clamp(28px,6vw,80px);padding-bottom:32px}.footer .brand{width:max-content;margin-bottom:12px;color:var(--ink)}.footer p,.footer a{color:var(--muted);font-size:12px;line-height:1.75}.footer-grid>div:first-child p{max-width:340px;margin:0}.footer h4{font-size:12px;letter-spacing:.04em;margin:5px 0 12px;color:var(--ink)}.footer-links{display:grid;justify-items:start;gap:6px}.footer a{transition:color .18s ease,transform .18s ease}.footer a:hover{color:var(--teal);transform:translateX(2px)}.footer-grid>div:last-child>a{display:inline-flex;align-items:center;gap:6px;color:var(--teal);font-weight:700}.copyright{display:flex;align-items:center;justify-content:space-between;gap:16px;border-top:1px solid #e0e6ed;padding:16px 0 0;color:#78838c;font-size:11px}.copyright span:last-child{color:#919aa2}
        @media(max-width:760px){.wrap{width:min(100% - 36px,560px)}.topbar{height:64px}.nav{gap:14px}.navlinks{gap:12px;font-size:11px}.navright{gap:8px}.login{padding:9px 12px}.hero{padding:48px 0}.hero-grid{grid-template-columns:1fr;gap:30px}.hero-photo{height:330px;grid-row:1}.hero h1{font-size:39px}.section{padding:58px 0}.service-grid,.steps-grid,.contact-grid{grid-template-columns:1fr}.service{min-height:auto}.service .btn{margin-top:4px}.floating-row{justify-content:center;margin:0 0 20px}.floating{width:100%;max-width:320px}.section-head h2{font-size:25px}.step{min-height:auto}.partner-banner{padding-bottom:28px}.partner-banner-inner{padding:19px 20px}.partner-banner h2{font-size:16px}.partner-banner .btn{padding:11px 14px;font-size:11px}.footer{padding:38px 0 18px}.footer-grid{grid-template-columns:1fr 1fr;gap:28px 22px;padding-bottom:26px}.footer-grid>div:first-child{grid-column:1/-1}.copyright{align-items:flex-start;flex-direction:column;gap:5px}}
        @media(max-width:430px){.navlinks{gap:9px}.navlinks a{font-size:10px}.brand{font-size:17px;gap:6px}.navright{gap:6px}.login,.register{padding:8px 10px;font-size:10px}.hero-photo{height:270px}.hero h1{font-size:34px}.proof{gap:10px}.partner-banner-inner{align-items:flex-start;flex-direction:column}.copyright{gap:12px;flex-direction:column}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>

<body>
    @php($bookingUrl = auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'owner' ? route('owner.dashboard') : route('dashboard'))) : route('register'))
    <header class="topbar">
        <nav class="nav wrap" aria-label="Navigasi utama">
            <a class="brand" href="#home"><img class="brand-logo" src="{{ asset('images/washly-logo.png') }}"
                    alt="">Washly</a>
            <div class="navlinks"><a href="#home">Beranda</a><a href="#layanan">Layanan</a><a
                    href="#kontak">Kontak</a></div>
            <div class="navright">
                @guest
                    <a class="login" href="{{ route('login') }}">Masuk</a><a class="register"
                        href="{{ route('register') }}">Daftar</a>
                @else
                    <a class="login"
                        href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'owner' ? route('owner.dashboard') : route('dashboard')) }}">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="register"
                            type="submit">Keluar</button></form>
                @endguest
            </div>
        </nav>
    </header>
    <main>
        <section class="hero" id="home">
            <div class="wrap hero-grid">
                <div><span class="eyebrow"><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m12 3 1.8 6.2L20 11l-6.2 1.8L12 19l-1.8-6.2L4 11l6.2-1.8L12 3Z" />
                            <path d="m19 14 .9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14Z" />
                        </svg>&nbsp; Solusi Laundry Praktis &amp; Wangi</span>
                    <h1>Cucian Bersih,<br><span>Hati Lebih Tenang!</span></h1>
                    <p>Layanan laundry praktis untuk pakaian bersih dan wangi setiap hari. Tanpa repot, wangi tahan
                        lama, dan terjamin higienis.</p>
                    <div class="actions"><a class="btn btn-primary" href="#layanan">Lihat Layanan &nbsp; <svg
                                class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5v14M5 12l7 7 7-7" />
                            </svg></a><a class="btn btn-soft" href="https://wa.me/6281234567890"><svg
                                class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 11.5a8.2 8.2 0 0 1-12.1 7.2L4 20l1.3-3.8A8.2 8.2 0 1 1 20 11.5Z" />
                                <path
                                    d="M9 8.5c.3-.6.6-.6 1-.6h.5l.8 1.9-.8 1c.7 1.4 1.7 2.4 3.2 3.1l.9-.9 1.9.9v.5c0 .4-.1.7-.6 1-1 .5-2.7 0-4.7-1.6-1.7-1.4-2.6-3.1-2.6-4.2 0-.5.2-.8.4-1.1Z" />
                            </svg>&nbsp; Hubungi WhatsApp</a></div>
                    <div class="proof"><span>Detergen Ramah Serat</span><span>1 Mesin 1 Pelanggan</span><span>Tepat
                            Waktu</span></div>
                </div>
                <div class="hero-photo"><img
                        src="https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?auto=format&fit=crop&w=1100&q=85"
                        alt="Tumpukan handuk dan pakaian bersih di keranjang laundry">
                    <div class="photo-tag"><span class="tag-icon"><svg class="landing-icon small" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path d="m12 3 1.8 6.2L20 11l-6.2 1.8L12 19l-1.8-6.2L4 11l6.2-1.8L12 3Z" />
                                <path d="m19 14 .9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14Z" />
                            </svg></span><span><b>Bersih, Rapi &amp; Siap Pakai</b>Higienis &amp; Wangi Sepanjang
                            Hari</span></div>
                </div>
            </div>
        </section>
        @guest
            <section class="partner-banner"><div class="wrap partner-banner-inner"><div><h2>Punya usaha laundry?</h2><p>Bergabung dengan Washly dan kelola layanan laundry kamu melalui dashboard mitra.</p></div><a class="btn" href="{{ route('register-owner') }}">Buka Kemitraan Laundry</a></div></section>
        @endguest
        <section class="section tint services-section" id="layanan"><div class="wrap"><div class="section-head"><span class="eyebrow">Layanan Kami</span><h2>Pilihan Layanan Sesuai Kebutuhan Anda</h2><p>Kualitas cuci terbaik dengan standar higienis dan harga transparan untuk kenyamanan seluruh keluarga.</p></div>
            <div class="floating-row"><a class="floating" href="{{ $bookingUrl }}"><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10h18l-1.5 10h-15L3 10Z"/><path d="M3 10l2-5h14l2 5"/></svg>Pesan Laundry Sekarang</a></div>
            <div class="service-grid">
                <article class="service"><span class="service-icon"><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10h18l-1.5 10h-15L3 10Z"/><path d="M3 10l2-5h14l2 5M7 14h.01M12 14h.01M17 14h.01"/></svg></span><h3>Cuci Kiloan</h3><p>Pilihan hemat untuk pakaian harian, seragam, dan pakaian santai. Dicuci bersih, dikeringkan, dan dilipat rapi.</p><div class="price"><small>TARIF HEMAT</small><b>Mulai Rp7.000</b> <span>/kg</span></div><ul class="features"><li>Estimasi pengerjaan 2–3 hari</li><li>Detergen premium wangi tahan lama</li><li>Dipisah per pelanggan (anti-campur)</li></ul><a class="btn" href="{{ $bookingUrl }}">Pesan Cuci Kiloan</a></article>
                <article class="service featured"><span class="popular"><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/></svg>&nbsp; Paling Laris</span><span class="service-icon"><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17h18l-1.6-7H9a6 6 0 0 0-6 6v1Z"/><path d="m9 10-1.5-5h6l2 5M6 14h.01"/></svg></span><h3>Cuci Setrika</h3><p>Paket komplit pakaian dicuci bersih higienis dan disetrika uap rapi, siap langsung masuk lemari pakaian.</p><div class="price"><small>TARIF LENGKAP</small><b>Mulai Rp10.000</b> <span>/kg</span></div><ul class="features"><li>Estimasi selesai 2 hari kerja</li><li>Setrika uap anti kusut &amp; higienis</li><li>Pakaian langsung rapi &amp; wangi segar</li></ul><a class="btn btn-primary" href="{{ $bookingUrl }}">Pilih Cuci Setrika</a></article>
                <article class="service"><span class="service-icon"><svg class="landing-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m13 2-3 9h7L9 22l2-9H5l8-11Z"/></svg></span><h3>Express</h3><p>Layanan kilat prioritas untuk kebutuhan mendesak. Selesai cepat dengan kualitas kebersihan tetap maksimal.</p><div class="price"><small>TARIF KILAT</small><b>Mulai Rp15.000</b> <span>/kg</span></div><ul class="features"><li>Selesai dalam 6–8 jam saja</li><li>Prioritas antrian mesin khusus</li><li>Cocok untuk urusan darurat atau bepergian</li></ul><a class="btn" href="{{ $bookingUrl }}">Pesan Layanan Express</a></article>
            </div>
        </section>
    </main>
    <footer class="footer"><div class="wrap"><div class="footer-grid">
        <div><a class="brand" href="#home"><img class="brand-logo" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a><p>Layanan laundry keluarga yang praktis dan terpercaya untuk pakaian bersih, higienis, dan rapi setiap hari.</p></div>
        <div><h4>Menu Cepat</h4><div class="footer-links"><a href="#home">Beranda</a><a href="#layanan">Layanan</a><a href="#kontak">Kontak</a></div></div>
        <div><h4>Hubungi Kami</h4><p>Siap jemput pakaian kotor di depan pintu rumah Anda.</p><a href="https://wa.me/6285187803376"><svg class="landing-icon small" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.5a8.2 8.2 0 0 1-12.1 7.2L4 20l1.3-3.8A8.2 8.2 0 1 1 20 11.5Z"/><path d="M9 8.5c.3-.6.6-.6 1-.6h.5l.8 1.9-.8 1c.7 1.4 1.7 2.4 3.2 3.1l.9-.9 1.9.9v.5c0 .4-.1.7-.6 1-1 .5-2.7 0-4.7-1.6-1.7-1.4-2.6-3.1-2.6-4.2 0-.5.2-.8.4-1.1Z"/></svg>&nbsp; WhatsApp CS Washly</a></div>
    </div><div class="copyright"><span>© {{ date('Y') }} Washly. Hak Cipta Dilindungi.</span><span>Solusi Laundry Keluarga &amp; Profesional Modern</span></div></div></footer>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>

</html>
