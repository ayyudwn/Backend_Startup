<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Contify</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e5e7eb;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            padding: 24px;
            overflow-y: auto;
            background: #020617;
        }

        .sidebar h2 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #38bdf8;
        }

        .sidebar-subtitle {
            margin-top: 0;
            margin-bottom: 20px;
            color: #94a3b8;
        }

        .sidebar a,
        .logout-btn {
            display: block;
            width: 100%;
            margin: 8px 0;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #cbd5e1;
            text-align: left;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .sidebar a:hover,
        .logout-btn:hover {
            background: #1e293b;
            color: white;
        }

        .sidebar a.active {
            background: #0284c7;
            color: white;
        }

        .logout-form {
            margin-top: 22px;
        }

        .logout-btn {
            background: #7f1d1d;
            color: #fee2e2;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }

        .content {
            margin-left: 240px;
            padding: 28px;
            min-height: 100vh;
        }

        .card {
            margin-bottom: 18px;
            padding: 18px;
            border: 1px solid #1f2937;
            border-radius: 14px;
            background: #111827;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(180px, 1fr)
            );
            gap: 14px;
        }

        .stat {
            padding: 18px;
            border-radius: 14px;
            background: #1e293b;
        }

        .stat h3 {
            margin: 0 0 8px;
            color: #94a3b8;
            font-size: 13px;
        }

        .stat p {
            margin: 0;
            color: #38bdf8;
            font-size: 22px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #111827;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #334155;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        th {
            color: #38bdf8;
        }

        input,
        textarea,
        select {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #334155;
            border-radius: 8px;
            background: #020617;
            color: white;
        }

        button {
            padding: 9px 14px;
            border: none;
            border-radius: 8px;
            background: #0284c7;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #0369a1;
        }

        .danger {
            background: #dc2626;
        }

        .danger:hover {
            background: #b91c1c;
        }

        .success {
            background: #16a34a;
        }

        .success:hover {
            background: #15803d;
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #334155;
            font-size: 12px;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 10px;
        }

        .alert-success {
            background: #064e3b;
            color: #d1fae5;
        }

        .alert-error {
            background: #7f1d1d;
            color: #fee2e2;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(200px, 1fr)
            );
            gap: 12px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <h2>CONTIFY</h2>

        <p class="sidebar-subtitle">
            Admin Panel
        </p>

        <a
            href="{{ route('admin.dashboard') }}"
            class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>

        <a
            href="{{ route('admin.customers.index') }}"
            class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
        >
            Kelola Pelanggan
        </a>

        <a
            href="{{ route('admin.payments') }}"
            class="{{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
        >
            Verifikasi Pembayaran
        </a>

        <a 
            href="{{ route('admin.orders') }}" 
            class="{{ request()->routeIs('admin.orders') ? 'active' : '' }}" 
        > 
            Kanban Produksi 
        </a>

        <a
            href="{{ route('admin.packages') }}"
            class="{{ request()->routeIs('admin.packages*') ? 'active' : '' }}"
        >
            Kelola Paket
        </a>

        <a
            href="{{ route('admin.quotas') }}"
            class="{{ request()->routeIs('admin.quotas*') ? 'active' : '' }}"
        >
            Kuota Kalender
        </a>

        <a
            href="{{ route('admin.vouchers') }}"
            class="{{ request()->routeIs('admin.vouchers*') ? 'active' : '' }}"
        >
            Voucher
        </a>

        <a
            href="{{ route('admin.teams') }}"
            class="{{ request()->routeIs('admin.teams*') ? 'active' : '' }}"
        >
            Tim Produksi
        </a>

        <a 
            href="{{ route('admin.orders.history') }}" 
            class="{{ request()->routeIs('admin.orders.history') ? 'active' : '' }}" 
        > 
            Riwayat Pesanan 
        </a>

        <form
            method="POST"
            action="{{ route('logout') }}"
            class="logout-form"
        >
            @csrf

            <button
                class="logout-btn"
                type="submit"
            >
                Logout
            </button>
        </form>
    </aside>

    <main class="content">
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
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>