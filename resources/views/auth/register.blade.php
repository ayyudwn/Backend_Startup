<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Contify</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 30px;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, #1d4ed8, transparent 35%),
                linear-gradient(135deg, #020617, #0f172a);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .box {
            width: 100%;
            max-width: 500px;
            padding: 34px;
            border: 1px solid #334155;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.97);
        }

        h1 {
            margin-top: 0;
            color: #38bdf8;
        }

        label {
            display: block;
            margin: 13px 0 6px;
            color: #cbd5e1;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #334155;
            border-radius: 10px;
            background: #020617;
            color: white;
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 13px;
            border: 0;
            border-radius: 10px;
            background: #0284c7;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .errors {
            padding: 12px;
            border-radius: 10px;
            background: #7f1d1d;
            color: #fee2e2;
        }

        a {
            color: #38bdf8;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>Buat Akun Contify</h1>

        <p style="color:#94a3b8;">
            Daftar sebagai user untuk memesan layanan konten.
        </p>

        @if($errors->any())
            <div class="errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.process') }}">
            @csrf

            <label>Nama lengkap / nama UMKM</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
            >

            <label>Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
            >

            <label>Nomor telepon</label>
            <input
                type="text"
                name="phone"
                value="{{ old('phone') }}"
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                required
            >

            <label>Konfirmasi password</label>
            <input
                type="password"
                name="password_confirmation"
                required
            >

            <button type="submit">
                Buat Akun
            </button>
        </form>

        <p style="text-align:center; color:#94a3b8;">
            Sudah memiliki akun?
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</body>
</html>