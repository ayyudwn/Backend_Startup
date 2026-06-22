@extends('layouts.admin')

@section('content')
    <h1>Verifikasi Pembayaran</h1>

    <p>
        Admin harus memilih satu anggota tim yang
        memiliki keahlian sesuai layanan sebelum
        pembayaran dapat diverifikasi.
    </p>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Klien</th>
                        <th>Layanan</th>
                        <th>Total</th>
                        <th>Bukti</th>
                        <th>Tim yang Sesuai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $order = $payment->order;

                            $requiredSkill =
                                $order?->required_skill;

                            $matchingTeams = $teams
                                ->filter(
                                    fn ($team) =>
                                        $requiredSkill
                                        && $team->hasSkill(
                                            $requiredSkill
                                        )
                                )
                                ->values();

                            $selectableTeams =
                                $matchingTeams->filter(
                                    fn ($team) =>
                                        $team->status !== 'offline'
                                        && $team->active_orders_count
                                            < \App\Models\ProductionTeam::MAX_ACTIVE_ORDERS
                                );
                        @endphp

                        <tr>
                            <td>
                                {{ $order?->order_code ?? '-' }}
                            </td>

                            <td>
                                {{ $order?->user?->name ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    {{ $requiredSkill ?? '-' }}
                                </strong>

                                @if($order?->package?->description)
                                    <div
                                        style="
                                            margin-top:6px;
                                            color:#94a3b8;
                                            font-size:12px;
                                            max-width:220px;
                                        "
                                    >
                                        {{ $order->package->description }}
                                    </div>
                                @endif
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
                                @if($payment->proof_image)
                                    <a
                                        href="{{ asset(
                                            'storage/' .
                                            $payment->proof_image
                                        ) }}"
                                        target="_blank"
                                        style="
                                            display:inline-block;
                                            padding:8px 12px;
                                            border-radius:8px;
                                            background:#334155;
                                            color:white;
                                            text-decoration:none;
                                            white-space:nowrap;
                                        "
                                    >
                                        Lihat Bukti
                                    </a>
                                @else
                                    <span class="badge">
                                        Belum ada bukti
                                    </span>
                                @endif
                            </td>

                            <td>
                                <form
                                    id="verify-payment-{{ $payment->id }}"
                                    method="POST"
                                    action="{{ route(
                                        'admin.payments.verify',
                                        $payment
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    @if($matchingTeams->isEmpty())
                                        <div
                                            style="
                                                margin-bottom:10px;
                                                padding:10px;
                                                border-radius:8px;
                                                background:#7f1d1d;
                                                color:#fee2e2;
                                                font-size:13px;
                                            "
                                        >
                                            Belum ada anggota tim
                                            dengan keahlian
                                            {{ $requiredSkill ?? 'yang sesuai' }}.
                                        </div>
                                    @else
                                        <select
                                            name="production_team_id"
                                            required
                                            @disabled(
                                                $selectableTeams->isEmpty()
                                            )
                                        >
                                            <option value="">
                                                Pilih anggota tim
                                            </option>

                                            @foreach($matchingTeams as $team)
                                                @php
                                                    $isOffline =
                                                        $team->status
                                                        === 'offline';

                                                    $isFull =
                                                        $team->active_orders_count
                                                        >= \App\Models\ProductionTeam::MAX_ACTIVE_ORDERS;

                                                    $isDisabled =
                                                        $isOffline
                                                        || $isFull;
                                                @endphp

                                                <option
                                                    value="{{ $team->id }}"
                                                    @disabled($isDisabled)
                                                    @selected(
                                                        (string) old(
                                                            'production_team_id'
                                                        )
                                                        === (string) $team->id
                                                    )
                                                >
                                                    {{ $team->name }}
                                                    — {{ $team->role }}
                                                    — {{ $team->active_orders_count }}/5 aktif

                                                    @if($isOffline)
                                                        — offline
                                                    @elseif($isFull)
                                                        — penuh
                                                    @else
                                                        — tersedia
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        @if($selectableTeams->isEmpty())
                                            <div
                                                style="
                                                    margin-bottom:10px;
                                                    color:#fca5a5;
                                                    font-size:13px;
                                                "
                                            >
                                                Semua anggota yang sesuai
                                                sedang offline atau sudah
                                                memiliki lima tugas aktif.
                                            </div>
                                        @endif
                                    @endif
                                </form>
                            </td>

                            <td>
                                <div
                                    style="
                                        display:flex;
                                        flex-direction:column;
                                        gap:8px;
                                        min-width:170px;
                                    "
                                >
                                    <button
                                        type="submit"
                                        form="verify-payment-{{ $payment->id }}"
                                        class="success"
                                        @disabled(
                                            $selectableTeams->isEmpty()
                                        )
                                        onclick="
                                            return confirm(
                                                'Verifikasi pembayaran dan masukkan pesanan ke antrean produksi?'
                                            )
                                        "
                                    >
                                        Verifikasi
                                    </button>

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
                                            style="width:100%;"
                                            onclick="
                                                return confirm(
                                                    'Yakin ingin menolak pembayaran ini?'
                                                )
                                            "
                                        >
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                Tidak ada pembayaran yang
                                menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
