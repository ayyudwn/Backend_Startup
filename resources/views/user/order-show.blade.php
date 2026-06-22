@extends('layouts.user')

@section('title', 'Detail Pesanan')

@section('content')
    <div style="margin-bottom:18px;">
        <a href="{{ route('user.dashboard') }}">
            ← Kembali ke pesanan
        </a>
    </div>

    <div class="grid">
        <section class="card" style="grid-column:span 2;">
            <h1>{{ $order->title }}</h1>

            <p>
                <span class="badge">
                    {{ $order->order_code }}
                </span>

                <span class="badge">
                    {{ $order->status }}
                </span>
            </p>

            <table>
                <tr>
                    <th>Paket</th>
                    <td>{{ $order->package->name }}</td>
                </tr>

                <tr>
                    <th>Tanggal produksi</th>
                    <td>
                        {{ $order->booking_date->format('d-m-Y') }}
                    </td>
                </tr>

                <tr>
                    <th>Prioritas</th>
                    <td>{{ $order->priority }}</td>
                </tr>

                <tr>
                    <th>Platform</th>
                    <td>{{ $order->platform ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Ukuran</th>
                    <td>{{ $order->content_size ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Catatan</th>
                    <td>{{ $order->notes ?? '-' }}</td>
                </tr>
            </table>
        </section>

        <aside class="card">
            <h2>Rincian Harga</h2>

            <p>
                Harga paket:
                <strong style="float:right;">
                    Rp{{ number_format(
                        $order->base_price,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </p>

            <p>
                Tambahan:
                <strong style="float:right;">
                    Rp{{ number_format(
                        $order->additional_price,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </p>

            <p>
                Diskon:
                <strong style="float:right;">
                    -Rp{{ number_format(
                        $order->discount,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </p>

            <hr>

            <h3>
                Total:
                <span style="float:right; color:#0284c7;">
                    Rp{{ number_format(
                        $order->total_price,
                        0,
                        ',',
                        '.'
                    ) }}
                </span>
            </h3>
        </aside>
    </div>

    <section class="card">
        <h2>Pembayaran</h2>

        <p>
            Metode:
            <strong>{{ $order->payment?->method ?? '-' }}</strong>
        </p>

        <p>
            Status:
            <span class="badge">
                {{ $order->payment?->status ?? '-' }}
            </span>
        </p>

        @if(
            $order->payment
            && in_array(
                $order->payment->status,
                ['pending', 'rejected'],
                true
            )
        )
            @if($order->payment->status === 'rejected')
                <div class="alert alert-error">
                    Bukti pembayaran ditolak.
                    Silakan unggah bukti yang baru.
                </div>
            @endif

            @if($order->payment->proof_image)
                <p>
                    Bukti pembayaran sudah dikirim dan sedang
                    menunggu verifikasi admin.
                </p>

                <img
                    src="{{ asset(
                        'storage/'.$order->payment->proof_image
                    ) }}"
                    alt="Bukti pembayaran"
                    style="
                        max-width:300px;
                        width:100%;
                        border-radius:12px;
                        display:block;
                        margin-bottom:15px;
                    "
                >
            @endif

            <form
                method="POST"
                action="{{ route(
                    'user.orders.payment',
                    $order
                ) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <label>
                    {{
                        $order->payment->proof_image
                            ? 'Ganti bukti pembayaran'
                            : 'Unggah bukti pembayaran'
                    }}
                </label>

                <input
                    type="file"
                    name="proof_image"
                    accept=".jpg,.jpeg,.png,.webp"
                    required
                >

                <button class="button" type="submit">
                    Kirim Bukti Pembayaran
                </button>
            </form>
        @elseif($order->payment?->status === 'verified')
            <div class="alert alert-success">
                Pembayaran sudah diverifikasi admin.
            </div>
        @endif
    </section>

    <section class="card">
        <h2>Status Pengerjaan</h2>

        @php
            $steps = [
                'pending' => 'Menunggu verifikasi pembayaran',
                'queue' => 'Masuk antrean produksi',
                'process' => 'Konten sedang diproses',
                'review' => 'Konten sedang direview',
                'done' => 'Konten selesai',
                'rejected' => 'Pesanan ditolak',
            ];
        @endphp

        <h3>
            {{ $steps[$order->status] ?? $order->status }}
        </h3>

        <p style="color:#64748b;">
            Status akan diperbarui oleh admin sesuai proses produksi.
        </p>
    </section>

    <section class="card">
        <h2>Hasil Konten</h2>

        @forelse($order->results as $result)
            <div
                style="
                    padding:14px;
                    border:1px solid #e2e8f0;
                    border-radius:10px;
                    margin-bottom:10px;
                "
            >
                <strong>{{ $result->file_name }}</strong>

                <p>{{ $result->notes }}</p>

                <a
                    class="button button-success"
                    href="{{ route(
                        'user.orders.results.download',
                        [
                            'order' => $order,
                            'result' => $result->id,
                        ]
                    ) }}"
                >
                    Download Hasil
                </a>
            </div>
        @empty
            <p style="color:#64748b;">
                Hasil konten belum tersedia.
            </p>
        @endforelse
    </section>
@endsection