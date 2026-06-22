@extends('layouts.user')

@section('title', 'Pesan Layanan')

@section('content')
    <div class="card">
        <h1>Pesan Layanan Contify</h1>

        <p style="color:#64748b;">
            Lengkapi kebutuhan kontenmu. Harga akan dihitung
            berdasarkan paket, deadline, dan voucher.
        </p>

        <form
            method="POST"
            action="{{ route('user.orders.store') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <label>Paket layanan</label>
            <select id="package" name="package_id" required>
                <option value="">Pilih paket</option>

                @foreach($packages as $package)
                    <option
                        value="{{ $package->id }}"
                        data-price="{{ $package->price }}"
                        @selected(
                            old('package_id', $selectedPackageId)
                            == $package->id
                        )
                    >
                        {{ $package->name }}
                        — Rp{{ number_format($package->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>

            <label>Judul pesanan</label>
            <input
                type="text"
                name="title"
                value="{{ old('title') }}"
                placeholder="Contoh: Video promo menu baru"
                required
            >

            <label>Detail kebutuhan</label>
            <textarea
                name="notes"
                placeholder="Jelaskan konsep, warna, teks, dan kebutuhan lainnya"
            >{{ old('notes') }}</textarea>

            <div class="grid">
                <div>
                    <label>Platform</label>
                    <select name="platform">
                        <option value="">Pilih platform</option>
                        <option
                            value="Instagram"
                            @selected(old('platform') === 'Instagram')
                        >
                            Instagram
                        </option>
                        <option
                            value="TikTok"
                            @selected(old('platform') === 'TikTok')
                        >
                            TikTok
                        </option>
                        <option
                            value="Facebook"
                            @selected(old('platform') === 'Facebook')
                        >
                            Facebook
                        </option>
                        <option
                            value="Lainnya"
                            @selected(old('platform') === 'Lainnya')
                        >
                            Lainnya
                        </option>
                    </select>
                </div>

                <div>
                    <label>Ukuran konten</label>
                    <select name="content_size">
                        <option value="">Pilih ukuran</option>
                        <option
                            value="Feed 1:1"
                            @selected(old('content_size') === 'Feed 1:1')
                        >
                            Feed 1:1
                        </option>
                        <option
                            value="Portrait 4:5"
                            @selected(old('content_size') === 'Portrait 4:5')
                        >
                            Portrait 4:5
                        </option>
                        <option
                            value="Story 9:16"
                            @selected(old('content_size') === 'Story 9:16')
                        >
                            Story 9:16
                        </option>
                    </select>
                </div>

                <div>
                    <label>File referensi</label>
                    <input
                        type="file"
                        name="reference_file"
                        accept=".jpg,.jpeg,.png,.webp,.pdf,.zip"
                    >
                </div>
            </div>

            <label>Tanggal produksi</label>
            <select name="booking_date" required>
                <option value="">Pilih tanggal tersedia</option>

                @foreach($quotas as $quota)
                    <option
                        value="{{ $quota->date->format('Y-m-d') }}"
                        @selected(
                            old('booking_date')
                            === $quota->date->format('Y-m-d')
                        )
                    >
                        {{ $quota->date->format('d-m-Y') }}
                        — sisa {{ $quota->remaining_quota }} slot
                    </option>
                @endforeach
            </select>

            @if($quotas->isEmpty())
                <p style="color:#b91c1c;">
                    Belum ada tanggal produksi yang tersedia.
                    Hubungi admin atau tambahkan kuota dari dashboard admin.
                </p>
            @endif

            <label>Deadline</label>
            <select id="deadline" name="deadline_type" required>
                <option
                    value="regular"
                    @selected(old('deadline_type') === 'regular')
                >
                    Regular — tanpa tambahan biaya
                </option>

                <option
                    value="express"
                    @selected(old('deadline_type') === 'express')
                >
                    Express — tambahan 25%
                </option>

                <option
                    value="kilat"
                    @selected(old('deadline_type') === 'kilat')
                >
                    Kilat — tambahan 50%
                </option>
            </select>

            <label>Voucher</label>
            <select id="voucher" name="voucher_code">
                <option
                    value=""
                    data-discount="0"
                >
                    Tanpa voucher
                </option>

                @foreach($vouchers as $voucher)
                    <option
                        value="{{ $voucher->code }}"
                        data-discount="{{ $voucher->discount_percent }}"
                        @selected(
                            old('voucher_code') === $voucher->code
                        )
                    >
                        {{ $voucher->code }}
                        — diskon {{ $voucher->discount_percent }}%
                    </option>
                @endforeach
            </select>

            <label>Metode pembayaran</label>
            <select name="payment_method" required>
                <option value="Transfer Bank">
                    Transfer Bank
                </option>
                <option value="QRIS">
                    QRIS
                </option>
                <option value="E-Wallet">
                    E-Wallet
                </option>
            </select>

            <div
                class="card"
                style="margin-top:20px; background:#f1f5f9;"
            >
                <h3>Estimasi Harga</h3>

                <p>
                    Harga paket:
                    <strong id="basePrice">Rp0</strong>
                </p>

                <p>
                    Biaya deadline:
                    <strong id="additionalPrice">Rp0</strong>
                </p>

                <p>
                    Potongan:
                    <strong id="discountPrice">Rp0</strong>
                </p>

                <h2>
                    Total:
                    <span id="totalPrice" style="color:#0284c7;">
                        Rp0
                    </span>
                </h2>
            </div>

            <button
                class="button"
                type="submit"
                @disabled($quotas->isEmpty())
            >
                Buat Pesanan
            </button>
        </form>
    </div>

    <script>
        const packageSelect = document.getElementById('package');
        const deadlineSelect = document.getElementById('deadline');
        const voucherSelect = document.getElementById('voucher');

        const basePriceElement = document.getElementById('basePrice');
        const additionalPriceElement =
            document.getElementById('additionalPrice');
        const discountPriceElement =
            document.getElementById('discountPrice');
        const totalPriceElement = document.getElementById('totalPrice');

        const rupiah = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        function calculatePrice() {
            const selectedPackage =
                packageSelect.options[packageSelect.selectedIndex];

            const basePrice =
                Number(selectedPackage?.dataset.price ?? 0);

            const deadline = deadlineSelect.value;

            let deadlineRate = 0;

            if (deadline === 'express') {
                deadlineRate = 0.25;
            }

            if (deadline === 'kilat') {
                deadlineRate = 0.50;
            }

            const additionalPrice =
                Math.round(basePrice * deadlineRate);

            const subtotal = basePrice + additionalPrice;

            const selectedVoucher =
                voucherSelect.options[voucherSelect.selectedIndex];

            const discountRate =
                Number(selectedVoucher?.dataset.discount ?? 0) / 100;

            const discountPrice =
                Math.round(subtotal * discountRate);

            const totalPrice =
                Math.max(0, subtotal - discountPrice);

            basePriceElement.textContent = rupiah.format(basePrice);

            additionalPriceElement.textContent =
                rupiah.format(additionalPrice);

            discountPriceElement.textContent =
                rupiah.format(discountPrice);

            totalPriceElement.textContent =
                rupiah.format(totalPrice);
        }

        packageSelect.addEventListener('change', calculatePrice);
        deadlineSelect.addEventListener('change', calculatePrice);
        voucherSelect.addEventListener('change', calculatePrice);

        calculatePrice();
    </script>
@endsection