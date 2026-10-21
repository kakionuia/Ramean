<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#f5f7ff" />
    <title>Daftar — Washly</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />
</head>

<body class="auth-page">
    <header class="auth-top">
        <a class="back-home" href="{{ route('home') }}"
            ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="m12 19-7-7 7-7M5 12h14" /></svg
            >Kembali ke Beranda Washly</a
        >
        <a class="auth-brand" href="{{ route('home') }}"
            ><img src="{{ asset('images/washly-logo.png') }}" alt="" />Washly</a
        >
    </header>

    <main class="auth-main auth-main-register">
        <section
            class="auth-card auth-card-register"
            aria-labelledby="register-title"
        >
            <div class="auth-card-main">
                <div class="auth-heading">
                    <div
                        class="auth-emblem"
                        style="border-radius: 50%; background: var(--auth-mint)"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <rect x="5" y="3" width="14" height="18" rx="2" />
                            <circle cx="12" cy="13" r="4" />
                            <path d="M8 6h.01M11 6h.01" />
                        </svg>
                    </div>
                    <span class="auth-kicker"
                        ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="m12 3 2 2.2 3-.2.9 2.9 2.7 1.4-1 2.8 1 2.8-2.7 1.4-.9 2.9-3-.2-2 2.2-2-2.2-3 .2-.9-2.9-2.7-1.4 1-2.8-1-2.8 2.7-1.4.9-2.9 3 .2L12 3Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg
                        >Layanan Laundry Terpercaya</span
                    >
                    <h2 id="register-title">Buat Akun Washly</h2>
                    <p>Daftar untuk mempermudah pemesanan laundry kamu.</p>
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

                <form
                    class="auth-form"
                    action="{{ route('register') }}"
                    method="POST"
                >
                    @csrf
                    <div class="field">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-wrap">
                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21a8 8 0 0 1 16 0" />
                            </svg
                            ><input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="Contoh: Budi Santoso"
                                autocomplete="name"
                                required
                            />
                        </div>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="m3 7 9 6 9-6" />
                            </svg
                            ><input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                autocomplete="email"
                                required
                            />
                        </div>
                    </div>
                    <div class="field">
                        <label for="phone">Nomor WhatsApp</label>
                        <div class="input-wrap">
                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path
                                    d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 3a2 2 0 0 1-.6 1.7L7.7 9.7a16 16 0 0 0 6.6 6.6l1.3-1.3a2 2 0 0 1 1.7-.6l3 .5a2 2 0 0 1 1.7 2Z"
                                /></svg
                            ><input
                                id="phone"
                                name="phone"
                                type="tel"
                                value="{{ old('phone') }}"
                                placeholder="Contoh: 081234567890"
                                autocomplete="tel"
                                required
                            />
                        </div>
                    </div>
                    <div class="field">
                        <label for="address">Alamat Penjemputan</label>
                        <div class="input-wrap">
                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1V10Z" /></svg
                            ><input
                                id="address"
                                name="address"
                                type="text"
                                value="{{ old('address') }}"
                                placeholder="Nama jalan, nomor rumah, dan patokan"
                                autocomplete="street-address"
                                required
                            />
                        </div>
                    </div>
                    <div class="field">
                        <label for="domisili">Provinsi Domisili</label>
                        <div class="input-wrap">
                            <select
                                id="domisili"
                                name="domisili"
                                data-selected="{{ old('domisili') }}"
                                required
                            >
                                <option value="">Memuat provinsi...</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="sub_domisili">Kabupaten / Kota</label>
                        <div class="input-wrap">
                            <select
                                id="sub_domisili"
                                name="sub_domisili"
                                data-selected="{{ old('sub_domisili') }}"
                                required
                                disabled
                            >
                                <option value="">
                                    Pilih provinsi terlebih dahulu
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="kecamatan">Kecamatan</label>
                        <div class="input-wrap">
                            <select
                                id="kecamatan"
                                name="kecamatan"
                                data-selected="{{ old('kecamatan') }}"
                                required
                                disabled
                            >
                                <option value="">
                                    Pilih kabupaten / kota terlebih dahulu
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="wilayah_akhir">Desa / Kelurahan</label>
                        <div class="input-wrap">
                            <select
                                id="wilayah_akhir"
                                name="wilayah_akhir"
                                data-selected="{{ old('wilayah_akhir') }}"
                                required
                                disabled
                            >
                                <option value="">
                                    Pilih kecamatan terlebih dahulu
                                </option>
                            </select>
                        </div>
                    </div>
                    <p id="location-error" class="field-hint" role="alert" hidden></p>
                    <div class="field">
                        <label for="password"
                            >Password
                            <span class="field-hint"
                                >Min. 8 karakter</span
                            ></label
                        >
                        <div class="input-wrap">
                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <rect x="5" y="10" width="14" height="11" rx="2" />
                                <path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3" />
                            </svg
                            ><input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            /><button
                                class="input-action"
                                type="button"
                                data-toggle-password="password"
                                aria-label="Tampilkan password"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <label for="password_confirmation"
                            >Konfirmasi Password</label
                        >
                        <div class="input-wrap">
                            <svg
                                class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <rect x="5" y="10" width="14" height="11" rx="2" />
                                <path d="M8 10V7a4 4 0 1 1 8 0v3M9 15l2 2 4-4" />
                            </svg
                            ><input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Ulangi kata sandi"
                                autocomplete="new-password"
                                required
                            /><button
                                class="input-action"
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="Tampilkan konfirmasi password"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button class="auth-submit" type="submit">
                        Buat Akun
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </button>
                </form>
                <p class="auth-switch">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                        >Masuk <span aria-hidden="true">›</span></a
                    >
                </p>
            </div>
        </section>

        <div class="label">
            <p>Daftar sebagai owner laundry <a href="{{ url('/register-owner') }}">Klik disini</a></p>
        </div>
    </main>
    <script>
        document.querySelectorAll("[data-toggle-password]").forEach((button) =>
            button.addEventListener("click", () => {
                const input = document.getElementById(
                    button.dataset.togglePassword,
                );
                input.type = input.type === "password" ? "text" : "password";
                button.setAttribute(
                    "aria-label",
                    input.type === "password"
                        ? "Tampilkan password"
                        : "Sembunyikan password",
                );
            }),
        );
    </script>
    <script>
        (() => {
            const province = document.getElementById("domisili");
            const regency = document.getElementById("sub_domisili");
            const district = document.getElementById("kecamatan");
            const village = document.getElementById("wilayah_akhir");
            const error = document.getElementById("location-error");
            const api = "https://www.emsifa.com/api-wilayah-indonesia/api";
            const locationFields = [
                [province, "Provinsi"],
                [regency, "Kabupaten / kota"],
                [district, "Kecamatan"],
                [village, "Desa / kelurahan"],
            ];

            const reset = (select, label) => {
                select.replaceChildren(new Option(label, ""));
                select.disabled = true;
            };

            const loadOptions = async (
                select,
                url,
                placeholder,
                selected = "",
            ) => {
                select.replaceChildren(new Option("Memuat data...", ""));
                select.disabled = true;
                const response = await fetch(url);
                if (!response.ok) throw new Error("Gagal memuat data wilayah.");
                const regions = await response.json();
                select.replaceChildren(new Option(placeholder, ""));
                regions.forEach((region) => {
                    const option = new Option(region.name, region.name);
                    option.dataset.regionId = region.id;
                    select.add(option);
                });
                select.disabled = false;
                if (selected) {
                    const match = Array.from(select.options).find(
                        (option) => option.text === selected,
                    );
                    if (match) select.value = match.value;
                }
            };

            const showError = (message) => {
                error.textContent = message;
                error.hidden = !message;
            };

            const enableManualLocation = () => {
                locationFields.forEach(([select, label]) => {
                    if (
                        select.nextElementSibling?.classList.contains(
                            "region-fallback",
                        )
                    )
                        return;

                    const input = document.createElement("input");
                    input.className = "region-fallback";
                    input.id = `${select.id}_manual`;
                    input.name = select.name;
                    input.type = "text";
                    input.value = select.value;
                    input.placeholder = `Masukkan ${label.toLowerCase()}`;
                    input.autocomplete = "address-level2";
                    input.required = true;
                    input.addEventListener("input", () => showError(""));

                    const fieldLabel = document.querySelector(
                        `label[for="${select.id}"]`,
                    );
                    if (fieldLabel) fieldLabel.htmlFor = input.id;
                    select.disabled = true;
                    select.hidden = true;
                    select.after(input);
                });
                showError(
                    "Data wilayah tidak dapat dimuat. Isi wilayah secara manual untuk melanjutkan.",
                );
            };

            const loadRegencies = async (selected = "") => {
                reset(district, "Pilih kabupaten / kota terlebih dahulu");
                reset(village, "Pilih kecamatan terlebih dahulu");
                if (!province.value) {
                    reset(regency, "Pilih provinsi terlebih dahulu");
                    return;
                }
                await loadOptions(
                    regency,
                    `${api}/regencies/${province.selectedOptions[0].dataset.regionId}.json`,
                    "Pilih kabupaten / kota",
                    selected,
                );
            };

            const loadDistricts = async (selected = "") => {
                reset(village, "Pilih kecamatan terlebih dahulu");
                if (!regency.value) {
                    reset(district, "Pilih kabupaten / kota terlebih dahulu");
                    return;
                }
                await loadOptions(
                    district,
                    `${api}/districts/${regency.selectedOptions[0].dataset.regionId}.json`,
                    "Pilih kecamatan",
                    selected,
                );
            };

            const loadVillages = async (selected = "") => {
                if (!district.value) {
                    reset(village, "Pilih kecamatan terlebih dahulu");
                    return;
                }
                await loadOptions(
                    village,
                    `${api}/villages/${district.selectedOptions[0].dataset.regionId}.json`,
                    "Pilih desa / kelurahan",
                    selected,
                );
            };

            province.addEventListener("change", () => {
                showError("");
                loadRegencies().catch(enableManualLocation);
            });
            regency.addEventListener("change", () => {
                showError("");
                loadDistricts().catch(enableManualLocation);
            });
            district.addEventListener("change", () => {
                showError("");
                loadVillages().catch(enableManualLocation);
            });

            (async () => {
                try {
                    await loadOptions(
                        province,
                        `${api}/provinces.json`,
                        "Pilih provinsi",
                        province.dataset.selected,
                    );
                    if (province.value) {
                        await loadRegencies(regency.dataset.selected);
                        if (regency.value) {
                            await loadDistricts(district.dataset.selected);
                            if (district.value)
                                await loadVillages(village.dataset.selected);
                        }
                    }
                } catch {
                    enableManualLocation();
                }
            })();
        })();
    </script>
</body>
</html>
