<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Owner Laundry</title>
</head>
<body>
    @if ($errors->any())
        <div role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <p role="alert">{{ session('error') }}</p>
    @endif

    <form action="{{ route('register.owner') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <h3>Informasi Pemilik (Owner)</h3>
    <div>
        <label>Nama Lengkap Owner</label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@example.com">
    </div>
    <div>
        <label>Nomor WhatsApp / HP</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="08123456789">
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <hr>

    <h3>Informasi Usaha Laundry</h3>
    <div>
        <label>Nama Laundry</label>
        <input type="text" name="store_name" value="{{ old('store_name') }}" required placeholder="Contoh: Clean & Fresh Laundry">
    </div>
    <div>
        <label>Nomor Telepon Toko/Usaha</label>
        <input type="text" name="store_phone" value="{{ old('store_phone') }}" required placeholder="081299998888">
    </div>
    <div>
        <label>Alamat Lengkap Laundry</label>
        <textarea name="address" required placeholder="Jl. Mawar No. 123, Kota...">{{ old('address') }}</textarea>
    </div>
    <div>
        <label>Deskripsi Singkat (Opsional)</label>
        <textarea name="description" placeholder="Melayani kiloan, satuan, express...">{{ old('description') }}</textarea>
    </div>
    <div>
        <label>Logo / Foto Tempat (Opsional)</label>
        <input type="file" name="logo" accept="image/*">
    </div>

    <button type="submit">Daftar Sebagai Owner Laundry</button>
</form>
<p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
</body>
</html>