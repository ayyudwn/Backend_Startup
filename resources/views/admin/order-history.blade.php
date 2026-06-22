@extends('layouts.admin')

@section('content')
    <h1>Riwayat Pesanan</h1>

    <p>
        Menampilkan seluruh pesanan yang sudah selesai dikerjakan
        dan tidak lagi masuk ke Kanban Produksi.
    </p>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Klien</th>
                        <th>Layanan</th>
                        <th>Judul</th>
                        <th>PIC</th>
                        <th>Booking</th>
                        <th>Selesai Pada</th>
                        <th>Prioritas</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                {{ $order->order_code }}
                            </td>

                            <td>
                                {{ $order->user?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $order->package?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $order->title }}
                            </td>

                            <td>
                                {{ $order->productionTeam?->name ?? '-' }}

                                @if($order->productionTeam)
                                    <div
                                        style="
                                            margin-top: 4px;
                                            color: #94a3b8;
                                            font-size: 12px;
                                        "
                                    >
                                        {{ $order->productionTeam->role }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $order->booking_date?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $order->updated_at?->format('d-m-Y H:i') ?? '-' }}
                            </td>

                            <td>
                                {{ $order->priority ?? '-' }}
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
                                <span
                                    class="badge"
                                    style="
                                        background: #166534;
                                        color: #dcfce7;
                                    "
                                >
                                    Selesai
                                </span>
                            </td>

                            <td>
                                <a
                                    href="{{ route(
                                        'admin.orders.show',
                                        $order
                                    ) }}"
                                    style="
                                        display: inline-block;
                                        padding: 9px 13px;
                                        border-radius: 8px;
                                        background: #2563eb;
                                        color: white;
                                        text-decoration: none;
                                        white-space: nowrap;
                                    "
                                >
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                Belum ada pesanan yang selesai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection