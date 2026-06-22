@extends('layouts.admin')

@section('content')
    <h1>Dashboard Admin</h1>
    <p>Ringkasan aktivitas pesanan dan revenue Contify.</p>

    <div class="grid">
        <div class="stat">
            <h3>Pesanan Hari Ini</h3>
            <p>{{ $stats['orders_today'] }}</p>
        </div>
        <div class="stat">
            <h3>Menunggu Verifikasi</h3>
            <p>{{ $stats['pending_payments'] }}</p>
        </div>
        <div class="stat">
            <h3>Dalam Produksi</h3>
            <p>{{ $stats['in_production'] }}</p>
        </div>
        <div class="stat">
            <h3>Selesai Hari Ini</h3>
            <p>{{ $stats['done_today'] }}</p>
        </div>
        <div class="stat">
            <h3>Revenue Hari Ini</h3>
            <p>Rp{{ number_format($stats['revenue_today'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card">
        <h2>Pesanan Terbaru</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Klien</th>
                    <th>Paket</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestOrders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->package->name }}</td>
                        <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td><span class="badge">{{ $order->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection