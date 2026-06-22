@extends('layouts.admin')

@section('content')
    <h1>Kuota Kalender Produksi</h1>
    <p>Admin mengatur tanggal produksi, jumlah kuota, dan status hari.</p>

    <div class="card">
        <h2>Tambah / Update Kuota</h2>
        <form method="POST" action="{{ route('admin.quotas.store') }}">
            @csrf
            <div class="form-grid">
                <input type="date" name="date" required>
                <input type="number" name="max_quota" placeholder="Max kuota" value="5" required>
                <input type="number" name="used_quota" placeholder="Kuota terpakai" value="0" required>
                <select name="status" required>
                    <option value="open">Open</option>
                    <option value="full">Full</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <button type="submit">Simpan Kuota</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Kuota</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kuota</th>
                    <th>Terpakai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotas as $quota)
                    <tr>
                        <td>{{ $quota->date }}</td>
                        <td>{{ $quota->max_quota }}</td>
                        <td>{{ $quota->used_quota }}</td>
                        <td><span class="badge">{{ $quota->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection