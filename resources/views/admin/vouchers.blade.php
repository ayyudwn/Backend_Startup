@extends('layouts.admin')

@section('content')
    <h1>Kode Voucher</h1>
    <p>Voucher digunakan untuk promo dan potongan harga saat user melakukan pemesanan.</p>

    <div class="card">
        <h2>Tambah Voucher</h2>
        <form method="POST" action="{{ route('admin.vouchers.store') }}">
            @csrf
            <div class="form-grid">
                <input type="text" name="code" placeholder="Kode voucher, contoh: CONTIFY20" required>
                <input type="number" name="discount_percent" placeholder="Diskon %" required>
                <input type="number" name="usage_limit" placeholder="Batas penggunaan" value="100" required>
            </div>
            <button type="submit">Simpan Voucher</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Voucher</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Diskon</th>
                    <th>Digunakan</th>
                    <th>Batas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->code }}</td>
                        <td>{{ $voucher->discount_percent }}%</td>
                        <td>{{ $voucher->usage_count }}</td>
                        <td>{{ $voucher->usage_limit }}</td>
                        <td><span class="badge">{{ $voucher->is_active ? 'active' : 'inactive' }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection