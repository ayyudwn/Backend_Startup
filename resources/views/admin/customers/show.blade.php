@extends('layouts.admin')

@section('content')
    <div style="margin-bottom:20px;">
        <a
            href="{{ route('admin.customers.index') }}"
            style="color:#38bdf8; text-decoration:none;"
        >
            ← Kembali ke daftar pelanggan
        </a>
    </div>

    <h1>Detail Pelanggan</h1>

    <p>
        Data akun, statistik, riwayat pesanan, dan pengelolaan akses.
    </p>

    <div class="grid" style="margin-bottom:20px;">
        <div class="stat">
            <h3>Total Pesanan</h3>
            <p>{{ $customerStats['total_orders'] }}</p>
        </div>

        <div class="stat">
            <h3>Menunggu</h3>
            <p>{{ $customerStats['pending_orders'] }}</p>
        </div>

        <div class="stat">
            <h3>Sedang Diproses</h3>
            <p>{{ $customerStats['active_orders'] }}</p>
        </div>

        <div class="stat">
            <h3>Selesai</h3>
            <p>{{ $customerStats['done_orders'] }}</p>
        </div>

        <div class="stat">
            <h3>Total Transaksi</h3>
            <p>
                Rp{{ number_format(
                    $customerStats['total_spending'],
                    0,
                    ',',
                    '.'
                ) }}
            </p>
        </div>
    </div>

    <div
        style="
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:18px;
        "
    >
        <section class="card">
            <h2>Informasi Akun</h2>

            <table>
                <tr>
                    <th>Nama</th>
                    <td>{{ $user->name }}</td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>

                <tr>
                    <th>Telepon</th>
                    <td>{{ $user->phone ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Role</th>
                    <td>{{ $user->role }}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Terdaftar</th>
                    <td>
                        {{ $user->created_at->format('d-m-Y H:i') }}
                    </td>
                </tr>
            </table>

            <form
                method="POST"
                action="{{ route(
                    'admin.customers.status',
                    $user
                ) }}"
                style="margin-top:18px;"
            >
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="{{ $user->is_active ? 'danger' : 'success' }}"
                    onclick="return confirm(
                        'Yakin ingin mengubah status akun ini?'
                    )"
                >
                    {{
                        $user->is_active
                            ? 'Nonaktifkan Akun'
                            : 'Aktifkan Akun'
                    }}
                </button>
            </form>
        </section>

        <section class="card">
            <h2>Reset Password Pelanggan</h2>

            <p style="color:#94a3b8;">
                Gunakan ketika pelanggan lupa password. Berikan
                password baru kepada pelanggan melalui saluran yang aman.
            </p>

            <form
                method="POST"
                action="{{ route(
                    'admin.customers.password',
                    $user
                ) }}"
            >
                @csrf
                @method('PATCH')

                <label>Password baru</label>

                <input
                    type="password"
                    name="password"
                    minlength="8"
                    required
                >

                <label>Konfirmasi password baru</label>

                <input
                    type="password"
                    name="password_confirmation"
                    minlength="8"
                    required
                >

                <button
                    type="submit"
                    onclick="return confirm(
                        'Yakin ingin mengganti password pelanggan ini?'
                    )"
                >
                    Ganti Password
                </button>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>Riwayat Pesanan</h2>

        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Paket</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead>

            <tbody>
                @forelse($user->orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>

                        <td>{{ $order->package->name }}</td>

                        <td>{{ $order->title }}</td>

                        <td>
                            {{ $order->booking_date->format('d-m-Y') }}
                        </td>

                        <td>
                            Rp{{ number_format(
                                $order->total_price,
                                0,
                                ',',
                                '.'
                            ) }}
                        </td>

                        <td>
                            <span class="badge">
                                {{ $order->payment?->status ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <span class="badge">
                                {{ $order->status }}
                            </span>
                        </td>

                        <td>
                            <a
                                href="{{ route(
                                    'admin.orders.show',
                                    $order
                                ) }}"
                                style="
                                    color:#38bdf8;
                                    text-decoration:none;
                                "
                            >
                                Buka Pesanan
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            Pelanggan belum memiliki pesanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection