<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Contify</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top right, #1d4ed8, transparent 35%),
                linear-gradient(135deg, #020617, #0f172a);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-box {
            width: 100%;
            max-width: 410px;
            padding: 34px;
            border: 1px solid #334155;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.96);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.45);
        }

        h1 {
            margin: 0;
            color: #38bdf8;
            letter-spacing: 1px;
        }

        .subtitle {
            color: #94a3b8;
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 14px 0 7px;
            color: #cbd5e1;
            font-size: 14px;
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

        button:hover {
            background: #0369a1;
        }

        .alert {
            margin-top: 15px;
            padding: 12px;
            border-radius: 10px;
        }

        .error {
            background: #7f1d1d;
            color: #fee2e2;
        }

        .success {
            background: #064e3b;
            color: #d1fae5;
        }

        .demo {
            margin-top: 18px;
            padding: 14px;
            border-radius: 12px;
            background: #111827;
            color: #cbd5e1;
            font-size: 13px;
            line-height: 1.7;
        }

        .register {
            margin-top: 18px;
            text-align: center;
            color: #94a3b8;
        }

        a {
            color: #38bdf8;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h1>CONTIFY</h1>

        <p class="subtitle">
            Masuk untuk mengelola pesanan dan kebutuhan kontenmu.
        </p>

        @if(session('error'))
            <div class="alert error">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <label>Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email"
                required
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <label style="display:flex; align-items:center; gap:8px;">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    style="width:auto;"
                >
                Ingat saya
            </label>

            <button type="submit">
                Masuk
            </button>
        </form>

        <div class="demo">
            <strong>Akun admin</strong><br>
            admin@contify.test<br>
            AdminContify123!<br><br>

            <strong>Akun user</strong><br>
            user@contify.test<br>
            UserContify123!
        </div>

        <div class="register">
            Belum memiliki akun?
            <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</body>
</html>