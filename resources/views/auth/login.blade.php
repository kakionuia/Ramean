<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7ff">
    <title>Masuk — Washly</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>

<body class="auth-page login-page">
    <header class="auth-top">
        <a class="back-home" href="{{ route('home') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m12 19-7-7 7-7M5 12h14" />
            </svg>Kembali ke Beranda Washly</a>
        <a class="auth-brand" href="{{ route('home') }}"><img src="{{ asset('images/washly-logo.png') }}"
                alt="">Washly</a>
    </header>

    <main class="auth-main">

        <div class="label">
            <p>Punya laundry, mau daftar sebagai owner? <a href="{{ url('/register-owner') }}">Klik disini</a></p>
        </div>

        <section class="auth-card" aria-labelledby="login-title">
            <div class="auth-card-main">
                <div class="auth-heading">
                    <div class="auth-emblem"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3s7 7.1 7 12a7 7 0 0 1-14 0c0-4.9 7-12 7-12Z" />
                            <path d="M9 15a3 3 0 0 0 3 3" />
                        </svg></div>
                    <h1>Washly</h1>
                    <h2 id="login-title">Selamat Datang Kembali!</h2>
                    <p>Masuk untuk melihat dan mengelola pesanan laundry kamu dengan mudah.</p>
                </div>

                @if (session('success'))
                    <div class="auth-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="auth-errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="auth-form" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="identity">Email atau Nomor WhatsApp</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M16 12v1.5a2 2 0 0 0 4 0V12a8 8 0 1 0-3 6.2" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <input id="identity" name="identity" type="text" value="{{ old('identity') }}"
                                placeholder="Contoh: budi@mail.com atau 081234567890" autocomplete="username" required
                                autofocus>
                        </div>
                        <p class="field-note">Gunakan kontak yang terdaftar saat penjemputan cucian.</p>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="5" y="10" width="14" height="11" rx="2" />
                                <path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3" />
                            </svg>
                            <input id="password" name="password" type="password" placeholder="Masukkan kata sandi"
                                autocomplete="current-password" required>
                            <button class="input-action" type="button" data-toggle-password="password"
                                aria-label="Tampilkan password"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg></button>
                        </div>
                    </div>
                    <div class="auth-options">
                        <label class="check-line"><input type="checkbox" name="remember" value="1"
                                @checked(old('remember'))>Ingat Saya</label>
                        <a class="auth-link"
                            href="https://wa.me/6281234567890?text=Halo%20Washly%2C%20saya%20lupa%20password">Lupa
                            Password?</a>
                    </div>
                    <button class="auth-submit" type="submit">Masuk <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <path d="M14 4h5v16h-5M10 8l4 4-4 4M14 12H3" />
                        </svg></button>
                </form>
            </div>
            <div class="auth-footer-panel">
                <p>Belum punya akun? <a class="auth-link" href="{{ route('register') }}">Daftar Sekarang</a></p>
                <span class="auth-assurance">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m12 3 8 3v5c0 5-3.4 8.2-8 10-4.6-1.8-8-5-8-10V6l8-3Z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>Layanan laundry higienis, teratur, dan transparan.</span>
            </div>
        </section>

    </main>
    <footer class="auth-legal">© {{ date('Y') }} Washly Laundry. Higienis, Teratur, dan Terpercaya.<span
            class="dot">•</span>Pusat Bantuan<span class="dot">•</span>Privasi</footer>
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(button => button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.togglePassword);
            input.type = input.type === 'password' ? 'text' : 'password';
            button.setAttribute('aria-label', input.type === 'password' ? 'Tampilkan password' :
                'Sembunyikan password')
        }));
    </script>
</body>

</html>
