<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#08796f">
    <title>Daftar Mitra Laundry - Washly</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>

<body class="auth-page">
    <header class="auth-top">
        <a class="back-home" href="{{ route('login') }}">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m15 18-6-6 6-6M9 12h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Kembali ke Login
        </a>
        <div class="auth-brand"><img src="{{ asset('images/washly-logo.png') }}" alt="">Washly</div>
    </header>

    <main class="auth-main auth-main-register">
        <div class="auth-context">
            <span>Mulai bergabung sebagai mitra</span>
            <span class="auth-pill">OWNER</span>
        </div>

        <section class="auth-card auth-card-register" aria-labelledby="register-title">
            <div class="auth-card-main">
                <div class="auth-heading">
                    <div class="auth-emblem" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4.75 8.25h14.5l1 12h-16l.5-12Z" stroke="currentColor" stroke-width="1.7"
                                stroke-linejoin="round" />
                            <path d="M8.5 8.25V6a3.5 3.5 0 0 1 7 0v2.25M9 14h6m-5 3h4" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="auth-kicker">Pendaftaran Mitra Laundry</div>
                    <h2 id="register-title">Buat Akun Owner</h2>
                    <p>Lengkapi informasi pemilik dan usaha laundry Anda untuk mulai menggunakan layanan.</p>
                </div>

                @if ($errors->any())
                    <div class="auth-errors" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="auth-errors" role="alert">{{ session('error') }}</div>
                @endif

                <form class="auth-form" action="{{ route('register.owner') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="auth-kicker">Informasi Pemilik</div>

                    <div class="field">
                        <label for="name">Nama Lengkap Owner</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 21a8 8 0 0 0-16 0m8-10a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"
                                    stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                autocomplete="name" required placeholder="Contoh: Budi Santoso">
                        </div>
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor"
                                    stroke-width="1.7" />
                                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                autocomplete="email" required placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="field">
                        <label for="phone">Nomor WhatsApp / HP</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"
                                    stroke="currentColor" stroke-width="1.7" />
                                <path d="M10 18h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                                autocomplete="tel" required placeholder="08123456789">
                        </div>
                    </div>

                    <div class="field">
                        <label for="domisili">Provinsi Domisili</label>
                        <div class="input-wrap">
                            <select id="domisili" name="domisili" data-selected="{{ old('domisili') }}" required>
                                <option value="">Memuat provinsi...</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="sub_domisili">Kabupaten / Kota</label>
                        <div class="input-wrap">
                            <select id="sub_domisili" name="sub_domisili"
                                data-selected="{{ old('sub_domisili') }}" required disabled>
                                <option value="">Pilih provinsi terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="kecamatan">Kecamatan</label>
                        <div class="input-wrap">
                            <select id="kecamatan" name="kecamatan" data-selected="{{ old('kecamatan') }}"
                                required disabled>
                                <option value="">Pilih kabupaten / kota terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="wilayah_akhir">Desa / Kelurahan</label>
                        <div class="input-wrap">
                            <select id="wilayah_akhir" name="wilayah_akhir"
                                data-selected="{{ old('wilayah_akhir') }}" required disabled>
                                <option value="">Pilih kecamatan terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                    <p id="location-error" class="field-hint" role="alert" hidden></p>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11" rx="2"
                                    stroke="currentColor" stroke-width="1.7" />
                                <path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                            </svg>
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                required placeholder="Buat password">
                        </div>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 7 10 17l-5-5" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="1.7" />
                            </svg>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                autocomplete="new-password" required placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="auth-kicker">Informasi Usaha Laundry</div>

                    <div class="field">
                        <label for="store_name">Nama Laundry</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10h18l-1.5-6h-15L3 10Zm1 0v10h16V10m-11 10v-6h6v6" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <input id="store_name" type="text" name="store_name" value="{{ old('store_name') }}"
                                autocomplete="organization" required placeholder="Contoh: Clean & Fresh Laundry">
                        </div>
                    </div>

                    <div class="field">
                        <label for="store_phone">Nomor Telepon Toko / Usaha</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"
                                    stroke="currentColor" stroke-width="1.7" />
                                <path d="M10 18h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                            <input id="store_phone" type="tel" name="store_phone"
                                value="{{ old('store_phone') }}" autocomplete="tel" required
                                placeholder="081299998888">
                        </div>
                    </div>

                    <div class="field">
                        <label for="address">Alamat Lengkap Laundry</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor"
                                    stroke-width="1.7" />
                                <circle cx="12" cy="10" r="2.5" stroke="currentColor"
                                    stroke-width="1.7" />
                            </svg>
                            <textarea id="address" name="address" autocomplete="street-address" required
                                placeholder="Jl. Mawar No. 123, Kota...">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="field">
                        <label for="description">Deskripsi Singkat <span class="field-hint">Opsional</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5h16M4 10h16M4 15h10M4 20h10" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                            </svg>
                            <textarea id="description" name="description" placeholder="Melayani kiloan, satuan, express...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="field">
                        <label for="logo">Logo / Foto Tempat <span class="field-hint">Opsional</span></label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"
                                    stroke="currentColor" stroke-width="1.7" />
                                <circle cx="8" cy="10" r="1.5" stroke="currentColor"
                                    stroke-width="1.5" />
                                <path d="m3 16 5-4 4 3 3-2 6 5" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <input id="logo" type="file" name="logo" accept="image/*">
                        </div>
                    </div>

                    <button class="auth-submit" type="submit">Daftar Sebagai Owner Laundry</button>
                </form>
            </div>

            <div class="auth-footer-panel">
                <p>Sudah punya akun?</p>
                <a class="auth-link" href="{{ route('login') }}">Login di sini</a>
            </div>
        </section>
    </main>
    <script>
        (() => {
            const province = document.getElementById('domisili');
            const regency = document.getElementById('sub_domisili');
            const district = document.getElementById('kecamatan');
            const village = document.getElementById('wilayah_akhir');
            const error = document.getElementById('location-error');
            const api = 'https://www.emsifa.com/api-wilayah-indonesia/api';

            const reset = (select, label) => {
                select.replaceChildren(new Option(label, ''));
                select.disabled = true;
            };

            const loadOptions = async (select, url, placeholder, selected = '') => {
                select.replaceChildren(new Option('Memuat data...', ''));
                select.disabled = true;
                const response = await fetch(url);
                if (!response.ok) throw new Error('Gagal memuat data wilayah.');
                const regions = await response.json();
                select.replaceChildren(new Option(placeholder, ''));
                regions.forEach(region => {
                    const option = new Option(region.name, region.name);
                    option.dataset.regionId = region.id;
                    select.add(option);
                });
                select.disabled = false;
                if (selected) {
                    const match = Array.from(select.options).find(option => option.text === selected);
                    if (match) select.value = match.value;
                }
            };

            const showError = message => {
                error.textContent = message;
                error.hidden = !message;
            };

            const loadRegencies = async (selected = '') => {
                reset(district, 'Pilih kabupaten / kota terlebih dahulu');
                reset(village, 'Pilih kecamatan terlebih dahulu');
                if (!province.value) {
                    reset(regency, 'Pilih provinsi terlebih dahulu');
                    return;
                }
                await loadOptions(regency, `${api}/regencies/${province.selectedOptions[0].dataset.regionId}.json`,
                    'Pilih kabupaten / kota', selected);
            };

            const loadDistricts = async (selected = '') => {
                reset(village, 'Pilih kecamatan terlebih dahulu');
                if (!regency.value) {
                    reset(district, 'Pilih kabupaten / kota terlebih dahulu');
                    return;
                }
                await loadOptions(district, `${api}/districts/${regency.selectedOptions[0].dataset.regionId}.json`,
                    'Pilih kecamatan', selected);
            };

            const loadVillages = async (selected = '') => {
                if (!district.value) {
                    reset(village, 'Pilih kecamatan terlebih dahulu');
                    return;
                }
                await loadOptions(village, `${api}/villages/${district.selectedOptions[0].dataset.regionId}.json`,
                    'Pilih desa / kelurahan', selected);
            };

            province.addEventListener('change', () => {
                showError('');
                loadRegencies().catch(() => showError('Data kabupaten/kota gagal dimuat. Periksa koneksi lalu muat ulang halaman.'));
            });
            regency.addEventListener('change', () => {
                showError('');
                loadDistricts().catch(() => showError('Data kecamatan gagal dimuat. Periksa koneksi lalu muat ulang halaman.'));
            });
            district.addEventListener('change', () => {
                showError('');
                loadVillages().catch(() => showError('Data desa/kelurahan gagal dimuat. Periksa koneksi lalu muat ulang halaman.'));
            });

            (async () => {
                try {
                    await loadOptions(province, `${api}/provinces.json`, 'Pilih provinsi',
                        province.dataset.selected);
                    if (province.value) {
                        await loadRegencies(regency.dataset.selected);
                        if (regency.value) {
                            await loadDistricts(district.dataset.selected);
                            if (district.value) await loadVillages(village.dataset.selected);
                        }
                    }
                } catch {
                    showError('Data provinsi gagal dimuat. Periksa koneksi lalu muat ulang halaman.');
                }
            })();
        })();
    </script>
</body>

</html>
