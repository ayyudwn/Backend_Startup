@extends('layouts.user')

@section('title', 'Pesanan Saya')

@section('content')
    <div style="display:flex; justify-content:space-between; gap:20px;">
        <div>
            <h1>Pesanan Saya</h1>
            <p style="color:#64748b;">
                Halo, {{ auth()->user()->name }}.
                Pantau seluruh proses pesananmu di sini.
            </p>
        </div>

        <div>
            <a
                class="button"
                href="{{ route('user.orders.create') }}"
            >
                + Buat Pesanan
            </a>
        </div>
    </div>

    <div class="stats">
        <div class="stat">
            <small>Total Pesanan</small>
            <strong>{{ $stats['total'] }}</strong>
        </div>

        <div class="stat">
            <small>Menunggu Pembayaran</small>
            <strong>{{ $stats['waiting_payment'] }}</strong>
        </div>

        <div class="stat">
            <small>Sedang Diproses</small>
            <strong>{{ $stats['in_progress'] }}</strong>
        </div>

        <div class="stat">
            <small>Selesai</small>
            <strong>{{ $stats['done'] }}</strong>
        </div>
    </div>

    <div class="card" style="margin-top:22px;">
        <h2>Riwayat Pesanan</h2>

        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Paket</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Proses</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>

                        <td>{{ $order->package->name }}</td>

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
                                class="button"
                                href="{{ route(
                                    'user.orders.show',
                                    $order
                                ) }}"
                            >
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            Belum ada pesanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection