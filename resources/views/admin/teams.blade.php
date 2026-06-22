@extends('layouts.admin')

@section('content')
    <h1>Verifikasi Pembayaran</h1>

    <p>
        Pilih satu anggota tim produksi sebelum
        memverifikasi pembayaran.
    </p>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Klien</th>
                        <th>Paket</th>
                        <th>Total</th>
                        <th>Bukti</th>
                        <th>Tim Produksi</th>
                        <th>Verifikasi</th>
                        <th>Tolak</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                {{ $payment->order->order_code }}
                            </td>

                            <td>
                                {{ $payment->order->user->name }}
                            </td>

                            <td>
                                {{ $payment->order->package->name }}
                            </td>

                            <td>
                                Rp{{ number_format(
                                    $payment->amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>
                                <a
                                    href="{{ asset(
                                        'storage/' .
                                        $payment->proof_image
                                    ) }}"
                                    target="_blank"
                                    style="color:#38bdf8;"
                                >
                                    Lihat Bukti
                                </a>
                            </td>

                            <td colspan="2">
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.payments.verify',
                                        $payment
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="production_team_id"
                                        required
                                    >
                                        <option value="">
                                            Pilih anggota tim
                                        </option>

                                        @foreach($teams as $team)
                                            @php
                                                $status = $team->display_status;

                                                $disabled =
                                                    $status === 'offline'
                                                    || $team->active_orders_count >= 5;
                                            @endphp

                                            <option
                                                value="{{ $team->id }}"
                                                @disabled($disabled)
                                            >
                                                {{ $team->name }}
                                                — {{ $team->role }}
                                                — {{ $team->active_orders_count }}/5 aktif

                                                @if($status === 'offline')
                                                    — offline
                                                @elseif($status === 'busy')
                                                    — penuh
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit">
                                        Verifikasi dan Masukkan Antrean
                                    </button>
                                </form>
                            </td>

                            <td>
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.payments.reject',
                                        $payment
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="danger"
                                        onclick="
                                            return confirm(
                                                'Yakin ingin menolak pembayaran ini?'
                                            )
                                        "
                                    >
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                Tidak ada pembayaran yang menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection