<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" >
</head>
<body>
    <h2>Register</h2>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div>
            <label>Nama:</label><br>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <br>
        <div>
            <label>Email:</label><br>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>
        <div>
            <label>Alamat:</label><br>
            <input type="text" name="address" value="{{ old('address') }}" required>
        </div>
        <br>

        <!-- Tambahkan id="domisili" di sini agar terbaca oleh JavaScript -->
        <div>
            <label>Domisili</label><br>
            <select name="domisili" id="domisili" required>
                <option value="">Pilih Domisili</option>
                <option value="Bogor" @selected(old('domisili') === 'Bogor')>Bogor</option>
            </select>
        </div>

        <br>

        <div id="selectExtension" style="display: none;">
        </div>

        <br>
        <div>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </div>
        <br>
        <div>
            <label>Konfirmasi Password:</label><br>
            <input type="password" name="password_confirmation" required>
        </div>
        <br>
        <button type="submit">Daftar</button>
    </form>
    
    <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>

    <script>
        const domisiliSelect = document.getElementById('domisili');
        const selectExtensionDiv = document.getElementById('selectExtension');
        const previousSelection = {
            subDomisili: @json(old('sub_domisili')),
            kecamatan: @json(old('kecamatan')),
            wilayahAkhir: @json(old('wilayah_akhir')),
        };

        // Data contoh untuk Kabupaten & Kota Bogor (bisa Anda kembangkan sesuai kebutuhan)
        const dataBogor = {
            kabupaten: {
                "Cibinong": ["Cirimekar", "Ciriung", "Pabuaran"],
                "Bogor Barat": ["Dramaga", "Ciampea"]
            },
            kota: {
                "Bogor Tengah": ["Babakan", "Pabaton", "Sempur"],
                "Bogor Timur": ["Baranangsiang", "Katulampa"]
            }
        };

        domisiliSelect.addEventListener('change', function() {
            const val = this.value;
            if (val === 'Bogor') {
                selectExtensionDiv.style.display = 'block';
                selectExtensionDiv.innerHTML = `
                    <select name="sub_domisili" id="subDomisili" required>
                        <option value="">Pilih Jenis Wilayah Bogor</option>
                        <option value="kabupaten">Kabupaten Bogor</option>
                        <option value="kota">Kota Bogor</option>
                    </select>
                    <div id="kecamatanContainer" style="margin-top: 10px;"></div>
                    <div id="finalContainer" style="margin-top: 10px;"></div>
                `;

                const subDomisiliSelect = document.getElementById('subDomisili');
                subDomisiliSelect.addEventListener('change', function() {
                    const subVal = this.value;
                    const kecContainer = document.getElementById('kecamatanContainer');
                    document.getElementById('finalContainer').innerHTML = '';
                    
                    if (subVal) {
                        let options = '<option value="">Pilih Kecamatan</option>';
                        const listKec = Object.keys(dataBogor[subVal]);
                        listKec.forEach(kec => {
                            options += `<option value="${kec}">${kec}</option>`;
                        });

                        kecContainer.innerHTML = `<select name="kecamatan" id="kecamatanSelect" required>${options}</select>`;

                        const kecamatanSelect = document.getElementById('kecamatanSelect');
                        kecamatanSelect.addEventListener('change', function() {
                            const kecVal = this.value;
                            const finalContainer = document.getElementById('finalContainer');
                            
                            if (kecVal) {
                                let finalOptions = '<option value="">Pilih Desa / Kelurahan</option>';
                                const listDesaKel = dataBogor[subVal][kecVal] || [];
                                listDesaKel.forEach(item => {
                                    finalOptions += `<option value="${item}">${item}</option>`;
                                });

                                finalContainer.innerHTML = `<select name="wilayah_akhir" required>${finalOptions}</select>`;
                                const wilayahAkhirSelect = finalContainer.querySelector('select');
                                wilayahAkhirSelect.value = previousSelection.wilayahAkhir;
                                previousSelection.wilayahAkhir = '';
                            } else {
                                finalContainer.innerHTML = '';
                            }
                        });

                        if (previousSelection.kecamatan) {
                            kecamatanSelect.value = previousSelection.kecamatan;
                            previousSelection.kecamatan = '';
                            kecamatanSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        kecContainer.innerHTML = '';
                    }
                });

                if (previousSelection.subDomisili) {
                    subDomisiliSelect.value = previousSelection.subDomisili;
                    previousSelection.subDomisili = '';
                    subDomisiliSelect.dispatchEvent(new Event('change'));
                }
            } else {
                selectExtensionDiv.style.display = 'none';
                selectExtensionDiv.innerHTML = '';
            }
        });

        if (domisiliSelect.value) {
            domisiliSelect.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>