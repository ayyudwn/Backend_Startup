@extends('layouts.admin')

@section('content')
    <h1>Kelola Pelanggan</h1>

    <p>
        Lihat jumlah pelanggan, data akun, dan riwayat pesanan.
    </p>

    <div class="grid" style="margin-bottom:20px;">
        <div class="stat">
            <h3>Total Pelanggan</h3>
            <p>{{ $stats['total_customers'] }}</p>
        </div>

        <div class="stat">
            <h3>Akun Aktif</h3>
            <p>{{ $stats['active_customers'] }}</p>
        </div>

        <div class="stat">
            <h3>Akun Nonaktif</h3>
            <p>{{ $stats['inactive_customers'] }}</p>
        </div>

        <div class="stat">
            <h3>Pernah Memesan</h3>
            <p>{{ $stats['customers_with_orders'] }}</p>
        </div>
    </div>

    <div class="card">
        <form
            method="GET"
            action="{{ route('admin.customers.index') }}"
            style="
                display:flex;
                gap:10px;
                align-items:center;
                margin-bottom:20px;
            "
        >
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama, email, atau nomor telepon"
                style="margin:0; max-width:420px;"
            >

            <button type="submit">
                Cari
            </button>

            @if($search !== '')
                <a
                    href="{{ route('admin.customers.index') }}"
                    style="
                        padding:9px 14px;
                        border-radius:8px;
                        background:#334155;
                        color:white;
                        text-decoration:none;
                    "
                >
                    Reset
                </a>
            @endif
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Jumlah Pesanan</th>
                    <th>Status Akun</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>

                        <td>{{ $customer->email }}</td>

                        <td>{{ $customer->phone ?? '-' }}</td>

                        <td>{{ $customer->orders_count }}</td>

                        <td>
                            <span class="badge">
                                {{
                                    $customer->is_active
                                        ? 'Aktif'
                                        : 'Nonaktif'
                                }}
                            </span>
                        </td>

                        <td>
                            {{ $customer->created_at->format('d-m-Y') }}
                        </td>

                        <td>
                            <a
                                href="{{ route(
                                    'admin.customers.show',
                                    $customer
                                ) }}"
                                style="
                                    display:inline-block;
                                    padding:9px 13px;
                                    border-radius:8px;
                                    background:#0284c7;
                                    color:white;
                                    text-decoration:none;
                                    white-space:nowrap;
                                "
                            >
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            Data pelanggan tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $customers->links() }}
        </div>
    </div>
@endsection