<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Laundry · Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
    <style>
        body{background:#faf8ff;color:#172033;font-family:Inter,"Segoe UI",Arial,sans-serif}.discover-wrap{width:min(100% - 40px,1120px);margin:40px auto}.discover-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:26px}.discover-top h1{margin:0;font-size:32px}.discover-top p{margin:7px 0 0;color:#58646a}.back-link{color:#00796f;font-weight:700}.discover-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px}.discover-card{overflow:hidden;border:1px solid #e3e7ff;border-radius:18px;background:#fff;box-shadow:0 8px 22px #252d3810}.discover-card img{display:block;width:100%;height:170px;object-fit:cover;background:#e5f4f2}.discover-content{padding:19px}.discover-content small{color:#78858a}.discover-content h2{margin:7px 0;font-size:21px}.discover-content p{color:#58646a;line-height:1.5}.discover-services{display:flex;flex-wrap:wrap;gap:7px}.discover-services span{padding:6px 9px;border-radius:8px;background:#f1f1ff;font-size:12px}.discover-bottom{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:20px}.discover-bottom strong{color:#00796f}.discover-bottom a,.discover-empty a{padding:10px 14px;border-radius:10px;color:white;background:#00796f;font-weight:700}.discover-empty{padding:50px 20px;border:1px dashed #cfd4e8;border-radius:18px;background:#fff;text-align:center}.discover-empty p{color:#69767b}.discover-empty a{display:inline-block;margin-top:10px}@media(max-width:560px){.discover-wrap{width:calc(100% - 30px);margin:25px auto}.discover-top{align-items:flex-start;gap:12px;flex-direction:column}.discover-top h1{font-size:27px}}
        .discover-filter{display:grid;grid-template-columns:1fr 210px auto;gap:10px;padding:15px;margin-bottom:22px;border:1px solid #e3e7ff;border-radius:15px;background:#fff}.discover-filter input,.discover-filter select{min-width:0;padding:11px 13px;border:1px solid #dfe5ed;border-radius:10px;background:#fff;color:inherit;font:inherit}.discover-filter button{padding:0 18px;border:0;border-radius:10px;background:#00796f;color:white;font:600 13px inherit;cursor:pointer}.discover-meta{display:flex;justify-content:space-between;gap:12px;margin:0 0 14px;color:#65717b;font-size:13px}.discover-badges{display:flex;flex-wrap:wrap;gap:6px;margin:12px 0}.discover-badges span{padding:5px 8px;border-radius:20px;background:#eaf7f3;color:#08796f;font-size:11px;font-weight:650}.discover-badges span.rating{background:#fff5dc;color:#855e00}.discover-empty{grid-column:1/-1}@media(max-width:560px){.discover-filter{grid-template-columns:1fr}.discover-filter button{min-height:42px}.discover-meta{align-items:flex-start;flex-direction:column}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>
<body>
    <main class="discover-wrap">
        <header class="discover-top"><div><h1>Pilih Laundry</h1><p>Temukan layanan laundry yang cocok untuk cucianmu.</p></div><a class="back-link" href="{{ route('dashboard') }}">← Kembali ke dashboard</a></header>
        <form class="discover-filter" method="GET" action="{{ route('laundries.discover') }}"><input name="area" value="{{ $area }}" placeholder="Cari kota, kecamatan, atau alamat"><select name="sort" aria-label="Urutkan laundry"><option value="recommended" @selected($sort === 'recommended')>Urutan rekomendasi</option><option value="price" @selected($sort === 'price')>Harga termurah</option><option value="rating" @selected($sort === 'rating')>Rating tertinggi</option></select><button type="submit">Cari Laundry</button></form>
        <p class="discover-meta"><span>{{ $laundries->count() }} mitra ditemukan</span><span>Lokasi cocok berdasarkan area dan alamat outlet</span></p>
        <section class="discover-grid">
            @forelse ($laundries as $laundry)
                <article class="discover-card">
                    <img src="{{ $laundry->logo ? asset('storage/' . $laundry->logo) : asset('images/washly-logo.png') }}" alt="{{ $laundry->store_name }}">
                    <div class="discover-content"><small>{{ $laundry->kecamatan ?: $laundry->domisili ?: 'Laundry Washly' }}</small><h2>{{ $laundry->store_name }}</h2><p>{{ $laundry->address }}</p><div class="discover-badges"><span>Antar sendiri</span>@if($laundry->pickup_available)<span>Antar-jemput tersedia</span>@endif<span class="rating">★ {{ $laundry->rating_count ? number_format($laundry->rating_average, 1) : 'Belum ada rating' }}@if($laundry->rating_count) ({{ $laundry->rating_count }})@endif</span></div><div class="discover-services">@forelse ($laundry->services->take(3) as $service)<span>{{ $service->name }}</span>@empty<span>Lihat layanan tersedia</span>@endforelse</div><div class="discover-bottom"><strong>@if ($laundry->starting_price !== null)Mulai Rp {{ number_format($laundry->starting_price, 0, ',', '.') }}@else Lihat layanan@endif</strong><a href="{{ route('laundry.show', $laundry->id) }}">Pilih Laundry</a></div></div>
                </article>
            @empty
                <div class="discover-empty"><h2>Belum ada laundry tersedia</h2><p>Laundry aktif akan muncul di sini setelah mendaftar.</p><a href="{{ route('dashboard') }}">Kembali ke dashboard</a></div>
            @endforelse
        </section>
    </main>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
