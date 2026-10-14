<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h2>Login</h2>

    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div>
            <label>Email:</label><br>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>
        <div>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </div>
        <br>
        <div>
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Ingat Saya</label>
        </div>
        <br>
        <button type="submit">Masuk</button>
    </form>

    <p>Belum punya akun? <a href="{{ route('register') }}">Register di sini</a></p>
    <p>Punya usaha laundry? <a href="{{ route('register-owner') }}">Daftar sebagai owner laundry</a></p>
</body>
</html>