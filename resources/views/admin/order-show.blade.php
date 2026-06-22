@extends('layouts.admin')

@section('content')
    <div style="margin-bottom:20px;">
        <a
            href="{{ route('admin.orders') }}"
            style="color:#38bdf8; text-decoration:none;"
        >
            ← Kembali ke daftar pesanan
        </a>
    </div>

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:20px;
            margin-bottom:20px;
        "
    >
        <div>
            <h1 style="margin-bottom:8px;">
                Detail Pesanan {{ $order->order_code }}
            </h1>

            <p style="color:#94a3b8;">
                Seluruh data yang diisi user saat melakukan pemesanan.
            </p>
        </div>

        <span class="badge">
            {{ strtoupper($order->status) }}
        </span>
    </div>

    <div
        style="
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:18px;
        "
    >
        {{-- Data user --}}
        <section class="card">
            <h2>Data User</h2>

            <table>
                <tr>
                    <th>Nama</th>
                    <td>{{ $order->user->name }}</td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td>{{ $order->user->email }}</td>
                </tr>

                <tr>
                    <th>Nomor Telepon</th>
                    <td>{{ $order->user->phone ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Role</th>
                    <td>{{ $order->user->role }}</td>
                </tr>
            </table>
        </section>

        {{-- Informasi pesanan --}}
        <section class="card">
            <h2>Informasi Pesanan</h2>

            <table>
                <tr>
                    <th>Kode Pesanan</th>
                    <td>{{ $order->order_code }}</td>
                </tr>

                <tr>
                    <th>Paket</th>
                    <td>{{ $order->package->name }}</td>
                </tr>

                <tr>
                    <th>Judul Pesanan</th>
                    <td>{{ $order->title }}</td>
                </tr>

                <tr>
                    <th>Tanggal Booking</th>
                    <td>
                        {{ $order->booking_date->format('d-m-Y') }}
                    </td>
                </tr>

                <tr>
                    <th>Deadline</th>
                    <td>{{ ucfirst($order->deadline_type) }}</td>
                </tr>

                <tr>
                    <th>Prioritas</th>
                    <td>{{ $order->priority }}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge">
                            {{ $order->status }}
                        </span>
                    </td>
                </tr>
            </table>
        </section>
    </div>

    {{-- Brief dari user --}}
    <section class="card">
        <h2>Detail Kebutuhan Konten</h2>

        <table>
            <tr>
                <th style="width:220px;">Platform</th>
                <td>{{ $order->platform ?? '-' }}</td>
            </tr>

            <tr>
                <th>Ukuran Konten</th>
                <td>{{ $order->content_size ?? '-' }}</td>
            </tr>

            <tr>
                <th>Catatan / Brief User</th>
                <td style="white-space:pre-wrap;">
                    {{ $order->notes ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>File Referensi</th>
                <td>
                    @if($order->reference_file)
                        <a
                            href="{{ asset(
                                'storage/'.$order->reference_file
                            ) }}"
                            target="_blank"
                            style="
                                display:inline-block;
                                padding:9px 14px;
                                border-radius:8px;
                                background:#0284c7;
                                color:white;
                                text-decoration:none;
                            "
                        >
                            Buka File Referensi
                        </a>

                        <div
                            style="
                                margin-top:9px;
                                color:#94a3b8;
                                font-size:13px;
                            "
                        >
                            {{ basename($order->reference_file) }}
                        </div>
                    @else
                        User tidak mengunggah file referensi.
                    @endif
                </td>
            </tr>
        </table>
    </section>

    <div
        style="
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:18px;
        "
    >
        {{-- Keuangan --}}
        <section class="card">
            <h2>Rincian Harga</h2>

            <table>
                <tr>
                    <th>Harga Paket</th>
                    <td>
                        Rp{{ number_format(
                            $order->base_price,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>
                </tr>

                <tr>
                    <th>Biaya Tambahan</th>
                    <td>
                        Rp{{ number_format(
                            $order->additional_price,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>
                </tr>

                <tr>
                    <th>Voucher</th>
                    <td>
                        {{ $order->voucher?->code ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>Potongan</th>
                    <td>
                        Rp{{ number_format(
                            $order->discount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>
                </tr>

                <tr>
                    <th>Total</th>
                    <td>
                        <strong style="color:#38bdf8;">
                            Rp{{ number_format(
                                $order->total_price,
                                0,
                                ',',
                                '.'
                            ) }}
                        </strong>
                    </td>
                </tr>
            </table>
        </section>

        {{-- Pembayaran --}}
        <section class="card">
            <h2>Pembayaran</h2>

            @if($order->payment)
                <table>
                    <tr>
                        <th>Metode</th>
                        <td>{{ $order->payment->method }}</td>
                    </tr>

                    <tr>
                        <th>Nominal</th>
                        <td>
                            Rp{{ number_format(
                                $order->payment->amount,
                                0,
                                ',',
                                '.'
                            ) }}
                        </td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge">
                                {{ $order->payment->status }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Waktu Verifikasi</th>
                        <td>
                            {{
                                $order->payment->verified_at
                                    ? $order->payment
                                        ->verified_at
                                        ->format('d-m-Y H:i')
                                    : '-'
                            }}
                        </td>
                    </tr>

                    <tr>
                        <th>Bukti Pembayaran</th>
                        <td>
                            @if($order->payment->proof_image)
                                <a
                                    href="{{ asset(
                                        'storage/'.
                                        $order->payment->proof_image
                                    ) }}"
                                    target="_blank"
                                    style="
                                        display:inline-block;
                                        padding:9px 14px;
                                        border-radius:8px;
                                        background:#16a34a;
                                        color:white;
                                        text-decoration:none;
                                    "
                                >
                                    Buka Bukti Pembayaran
                                </a>
                            @else
                                Belum diunggah user.
                            @endif
                        </td>
                    </tr>
                </table>
            @else
                <p>Data pembayaran tidak ditemukan.</p>
            @endif
        </section>
    </div>

    {{-- Preview file --}}
    @if($order->reference_file)
        @php
            $referenceExtension = strtolower(
                pathinfo(
                    $order->reference_file,
                    PATHINFO_EXTENSION
                )
            );

            $imageExtensions = [
                'jpg',
                'jpeg',
                'png',
                'webp',
            ];
        @endphp

        @if(in_array($referenceExtension, $imageExtensions, true))
            <section class="card">
                <h2>Preview File Referensi</h2>

                <img
                    src="{{ asset(
                        'storage/'.$order->reference_file
                    ) }}"
                    alt="File referensi user"
                    style="
                        display:block;
                        width:100%;
                        max-width:700px;
                        max-height:500px;
                        object-fit:contain;
                        border-radius:12px;
                        background:#020617;
                    "
                >
            </section>
        @endif
    @endif

    {{-- Update status --}}
    <section class="card">
        <h2>Kelola Status Pesanan</h2>

        <form
            method="POST"
            action="{{ route(
                'admin.orders.status',
                $order
            ) }}"
        >
            @csrf
            @method('PATCH')

            <label for="status">
                Pilih status pengerjaan
            </label>

            <select
                id="status"
                name="status"
                style="max-width:420px;"
                required
            >
                <option
                    value="pending"
                    @selected($order->status === 'pending')
                >
                    Pending — menunggu verifikasi
                </option>

                <option
                    value="queue"
                    @selected($order->status === 'queue')
                >
                    Antrean produksi
                </option>

                <option
                    value="process"
                    @selected($order->status === 'process')
                >
                    Sedang diproses
                </option>

                <option
                    value="review"
                    @selected($order->status === 'review')
                >
                    Review hasil
                </option>

                <option
                    value="done"
                    @selected($order->status === 'done')
                >
                    Selesai
                </option>

                <option
                    value="rejected"
                    @selected($order->status === 'rejected')
                >
                    Ditolak
                </option>
            </select>

            <button type="submit">
                Simpan Status
            </button>
        </form>
    </section>

    {{-- Hasil konten --}}
    <section class="card">
        <h2>Hasil Konten</h2>

        @forelse($order->results as $result)
            <div
                style="
                    padding:14px;
                    margin-bottom:12px;
                    border:1px solid #334155;
                    border-radius:10px;
                "
            >
                <strong>{{ $result->file_name }}</strong>

                <p>{{ $result->notes ?? '-' }}</p>

                @if($result->file_path)
                    <a
                        href="{{ asset(
                            'storage/'.$result->file_path
                        ) }}"
                        target="_blank"
                        style="color:#38bdf8;"
                    >
                        Buka file hasil
                    </a>
                @endif
            </div>
        @empty
            <p style="color:#94a3b8;">
                Hasil konten belum diunggah.
            </p>
        @endforelse
    </section>
@endsection