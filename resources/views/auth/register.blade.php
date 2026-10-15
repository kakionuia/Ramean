<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7ff">
    <title>Daftar — Washly</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <header class="auth-top">
        <a class="back-home" href="{{ route('home') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14"/></svg>Kembali ke Beranda Washly</a>
        <a class="auth-brand" href="{{ route('home') }}"><img src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a>
    </header>

    <main class="auth-main auth-main-register">
        <section class="auth-card auth-card-register" aria-labelledby="register-title">
            <div class="auth-card-main">
                <div class="auth-heading">
                    <div class="auth-emblem" style="border-radius:50%;background:var(--auth-mint)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><circle cx="12" cy="13" r="4"/><path d="M8 6h.01M11 6h.01"/></svg></div>
                    <span class="auth-kicker"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 2 2.2 3-.2.9 2.9 2.7 1.4-1 2.8 1 2.8-2.7 1.4-.9 2.9-3-.2-2 2.2-2-2.2-3 .2-.9-2.9-2.7-1.4 1-2.8-1-2.8 2.7-1.4.9-2.9 3 .2L12 3Z"/><path d="m9 12 2 2 4-4"/></svg>Layanan Laundry Terpercaya</span>
                    <h2 id="register-title">Buat Akun Washly</h2>
                    <p>Daftar untuk mempermudah pemesanan laundry kamu.</p>
                </div>

                @if ($errors->any())
                    <div class="auth-errors" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                <form class="auth-form" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" autocomplete="name" required></div>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required></div>
                    </div>
                    <div class="field">
                        <label for="phone">Nomor WhatsApp</label>
                        <div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 3a2 2 0 0 1-.6 1.7L7.7 9.7a16 16 0 0 0 6.6 6.6l1.3-1.3a2 2 0 0 1 1.7-.6l3 .5a2 2 0 0 1 1.7 2Z"/></svg><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" autocomplete="tel" required></div>
                    </div>
                    <div class="field">
                        <label for="address">Alamat Penjemputan</label>
                        <div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1V10Z"/></svg><input id="address" name="address" type="text" value="{{ old('address') }}" placeholder="Nama jalan, nomor rumah, dan patokan" autocomplete="street-address" required></div>
                    </div>
                    <div class="field">
                        <label for="password">Password <span class="field-hint">Min. 8 karakter</span></label>
                        <div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg><input id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required><button class="input-action" type="button" data-toggle-password="password" aria-label="Tampilkan password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M9 15l2 2 4-4"/></svg><input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required><button class="input-action" type="button" data-toggle-password="password_confirmation" aria-label="Tampilkan konfirmasi password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
                    </div>
                    <button class="auth-submit" type="submit">Buat Akun <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
                </form>
                <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk <span aria-hidden="true">›</span></a></p>
            </div>
        </section>
    </main>
    <script>document.querySelectorAll('[data-toggle-password]').forEach(button=>button.addEventListener('click',()=>{const input=document.getElementById(button.dataset.togglePassword);input.type=input.type==='password'?'text':'password';button.setAttribute('aria-label',input.type==='password'?'Tampilkan password':'Sembunyikan password')}));</script>
</body>
</html>
