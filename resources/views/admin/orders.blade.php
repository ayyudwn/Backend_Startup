@extends('layouts.admin')

@section('content')
    <h1>Kanban Produksi</h1>

    <p>
        Menampilkan pesanan yang sudah diverifikasi
        dan memiliki penanggung jawab.
    </p>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Klien</th>
                        <th>Paket</th>
                        <th>Judul</th>
                        <th>PIC</th>
                        <th>Booking</th>
                        <th>Prioritas</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Detail</th>
                        <th>Ubah Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                {{ $order->order_code }}
                            </td>

                            <td>
                                {{ $order->user->name }}
                            </td>

                            <td>
                                {{ $order->package->name }}
                            </td>

                            <td>
                                {{ $order->title }}
                            </td>

                            <td>
                                {{ $order->productionTeam?->name ?? '-' }}

                                @if($order->productionTeam)
                                    <div
                                        style="
                                            color:#94a3b8;
                                            font-size:12px;
                                            margin-top:4px;
                                        "
                                    >
                                        {{ $order->productionTeam->role }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $order->booking_date?->format(
                                    'd-m-Y'
                                ) ?? '-' }}
                            </td>

                            <td>
                                {{ $order->priority }}
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
                                    @switch($order->status)
                                        @case('queue')
                                            Antrean
                                            @break

                                        @case('process')
                                            Diproses
                                            @break

                                        @case('review')
                                            Review
                                            @break

                                        @default
                                            {{ ucfirst($order->status) }}
                                    @endswitch
                                </span>
                            </td>

                            <td>
                                <a
                                    href="{{ route(
                                        'admin.orders.show',
                                        $order
                                    ) }}"
                                    style="
                                        display:inline-block;
                                        padding:9px 13px;
                                        border-radius:8px;
                                        background:#2563eb;
                                        color:white;
                                        text-decoration:none;
                                        white-space:nowrap;
                                    "
                                >
                                    Lihat Detail
                                </a>
                            </td>

                            <td>
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.orders.status',
                                        $order
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        required
                                    >
                                        <option
                                            value="queue"
                                            @selected(
                                                $order->status === 'queue'
                                            )
                                        >
                                            Antrean
                                        </option>

                                        <option
                                            value="process"
                                            @selected(
                                                $order->status === 'process'
                                            )
                                        >
                                            Diproses
                                        </option>

                                        <option
                                            value="review"
                                            @selected(
                                                $order->status === 'review'
                                            )
                                        >
                                            Review
                                        </option>

                                        <option value="done">
                                            Selesai
                                        </option>
                                    </select>

                                    <button
                                        type="submit"
                                        onclick="
                                            return confirm(
                                                this.form.status.value === 'done'
                                                    ? 'Pesanan selesai akan masuk riwayat dan total tugas selesai PIC akan bertambah. Lanjutkan?'
                                                    : 'Perbarui status produksi?'
                                            )
                                        "
                                    >
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                Belum ada pesanan dalam proses produksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection