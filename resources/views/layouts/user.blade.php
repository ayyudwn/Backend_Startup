<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Contify')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f8fafc;
            color: #0f172a;
            font-family: Arial, sans-serif;
        }

        nav {
            position: sticky;
            top: 0;
            z-index: 20;
            padding: 16px 7%;
            background: #020617;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        }

        .brand {
            color: #38bdf8;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links a,
        .logout {
            padding: 9px 13px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: #cbd5e1;
            text-decoration: none;
            cursor: pointer;
        }

        .nav-links a:hover,
        .logout:hover {
            background: #1e293b;
            color: white;
        }

        .container {
            width: min(1150px, 92%);
            margin: 30px auto;
        }

        .card {
            margin-bottom: 20px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: white;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
        }

        .button {
            display: inline-block;
            padding: 11px 16px;
            border: 0;
            border-radius: 10px;
            background: #0284c7;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover {
            background: #0369a1;
        }

        .button-secondary {
            background: #334155;
        }

        .button-success {
            background: #16a34a;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
        }

        .stat {
            padding: 20px;
            border-radius: 15px;
            background: #0f172a;
            color: white;
        }

        .stat small {
            color: #94a3b8;
        }

        .stat strong {
            display: block;
            margin-top: 10px;
            color: #38bdf8;
            font-size: 28px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        label {
            display: block;
            margin: 13px 0 7px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            color: #475569;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            font-size: 12px;
            font-weight: bold;
        }

        .alert {
            margin-bottom: 18px;
            padding: 13px;
            border-radius: 10px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        footer {
            margin-top: 50px;
            padding: 24px;
            background: #020617;
            color: #94a3b8;
            text-align: center;
        }

        @media(max-width: 850px) {
            nav {
                align-items: flex-start;
                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .grid,
            .stats {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <nav>
        <a class="brand" href="{{ route('user.home') }}">
            CONTIFY
        </a>

        <div class="nav-links">
            <a href="{{ route('user.home') }}">
                Paket
            </a>

            <a href="{{ route('user.orders.create') }}">
                Pesan Layanan
            </a>

            <a href="{{ route('user.dashboard') }}">
                Pesanan Saya
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="logout" type="submit">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        Contify — Solusi Konten Profesional untuk UMKM
    </footer>
</body>
</html>