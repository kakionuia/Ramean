<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menunggu Persetujuan · Washly</title>
    <link rel="stylesheet" href="{{ asset('owner/owner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
    <style>
        body{min-height:100vh;display:grid;place-items:center;padding:24px;background:#f4f7f4;color:#20372c;font-family:"Segoe UI",Arial,sans-serif}.pending-card{width:min(100%,620px);padding:clamp(28px,6vw,52px);border:1px solid #e1eae3;border-radius:24px;background:#fff;box-shadow:0 24px 70px #20372c12;text-align:center}.pending-brand{display:inline-flex;align-items:center;gap:10px;margin-bottom:30px;color:#244e3d;font-size:21px;font-weight:750}.pending-brand img{width:44px;height:44px;object-fit:contain}.pending-icon{display:grid;width:76px;height:76px;place-items:center;margin:0 auto 20px;border-radius:24px;background:#fff5dc;color:#9a6a08;font-size:36px}.pending-card h1{margin:0 0 12px;font-size:clamp(25px,5vw,34px);letter-spacing:-.6px}.pending-card p{margin:0;color:#65746b;font-size:15px;line-height:1.75}.pending-card .pending-success{margin:0 0 14px;padding:10px;border-radius:10px;background:#edf7ef;color:#276447;font-size:13px}.pending-note{margin-top:24px;padding:14px 16px;border-radius:12px;background:#f2f7f3;color:#466150;font-size:13px;line-height:1.6}.pending-actions{display:flex;justify-content:center;gap:12px;margin-top:26px}.pending-actions button,.pending-actions a{padding:11px 18px;border:0;border-radius:10px;background:#244e3d;color:white;font:600 13px inherit;cursor:pointer}.pending-actions a{background:#edf3ee;color:#244e3d}@media(max-width:480px){.pending-actions{flex-direction:column}.pending-actions>*{width:100%}}
    </style>
</head>
<body>
    <main class="pending-card">
        <a class="pending-brand" href="{{ route('home') }}"><img src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a>
        <div class="pending-icon" aria-hidden="true">⏳</div>
        <h1>Pendaftaran Mitra Sedang Ditinjau</h1>
        @if (session('success'))<p class="pending-success" role="status">{{ session('success') }}</p>@endif
        <p>Halo {{ $owner->name }}, terima kasih sudah mendaftar sebagai mitra Washly. Tim admin kami sedang memeriksa data outlet kamu.</p>
        <div class="pending-note">Estimasi proses verifikasi maksimal 1 × 24 jam. Halaman ini akan menampilkan dashboard mitra setelah pendaftaran disetujui.</div>
        <div class="pending-actions"><button type="button" onclick="window.location.reload()">Periksa Status</button><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Keluar</button></form></div>
    </main>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>
</html>
