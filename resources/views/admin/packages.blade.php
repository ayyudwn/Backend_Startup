@extends('layouts.admin')

@section('content')
    <style>
        .package-form-grid {
            display: grid;
            grid-template-columns: repeat(
                2,
                minmax(0, 1fr)
            );
            gap: 14px;
        }

        .package-form-grid .full {
            grid-column: 1 / -1;
        }

        .package-form label {
            display: block;
            margin-bottom: 7px;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: bold;
        }

        .package-description {
            max-width: 280px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.5;
        }

        .package-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 150px;
        }

        .package-actions button {
            width: 100%;
        }

        .package-details {
            margin-bottom: 8px;
        }

        .package-details summary {
            padding: 9px 12px;
            border-radius: 8px;
            background: #334155;
            color: white;
            text-align: center;
            font-size: 13px;
            cursor: pointer;
            list-style: none;
        }

        .package-details summary::-webkit-details-marker {
            display: none;
        }

        .package-detail-content {
            margin-top: 10px;
            padding: 16px;
            border: 1px solid #334155;
            border-radius: 10px;
            background: #020617;
        }

        .package-detail-content h4 {
            margin: 0 0 8px;
            color: #38bdf8;
        }

        .package-detail-content p {
            color: #cbd5e1;
            line-height: 1.6;
        }

        .package-detail-content ul {
            margin: 10px 0 0;
            padding-left: 20px;
            color: #cbd5e1;
        }

        .package-detail-content li {
            margin-bottom: 7px;
        }

        .edit-package-box {
            margin-top: 10px;
            padding: 16px;
            border: 1px solid #334155;
            border-radius: 10px;
            background: #020617;
        }

        .status-active {
            background: #166534;
            color: #dcfce7;
        }

        .status-inactive {
            background: #7f1d1d;
            color: #fee2e2;
        }

        .warning-button {
            background: #d97706;
        }

        @media (max-width: 900px) {
            .package-form-grid {
                grid-template-columns: 1fr;
            }

            .package-form-grid .full {
                grid-column: auto;
            }
        }
    </style>

    <h1>Kelola Paket Layanan</h1>

    <p>
        Tambahkan paket berdasarkan jenis layanan Contify.
        Nama paket dapat dibuat berbeda untuk setiap variasi layanan.
    </p>

    <div class="card">
        <h2>Tambah Paket</h2>

        <form
            method="POST"
            action="{{ route('admin.packages.store') }}"
            class="package-form"
        >
            @csrf

            <div class="package-form-grid">
                <div>
                    <label>Jenis Layanan</label>

                    <select
                        name="service_type"
                        required
                    >
                        <option value="">
                            Pilih jenis layanan
                        </option>

                        @foreach(
                            \App\Models\ServicePackage::SERVICE_TYPES
                            as $serviceType
                        )
                            <option
                                value="{{ $serviceType }}"
                                @selected(
                                    old('service_type')
                                    === $serviceType
                                )
                            >
                                {{ $serviceType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Nama Paket</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Paket Foto Produk Hemat"
                        required
                    >
                </div>

                <div>
                    <label>Harga</label>

                    <input
                        type="number"
                        name="price"
                        value="{{ old('price') }}"
                        min="0"
                        placeholder="150000"
                        required
                    >
                </div>

                <div>
                    <label>Durasi</label>

                    <input
                        type="text"
                        name="duration"
                        value="{{ old('duration') }}"
                        placeholder="Contoh: 2-3 hari"
                        required
                    >
                </div>

                <div>
                    <label>Maksimal Revisi</label>

                    <input
                        type="number"
                        name="revision_limit"
                        value="{{ old('revision_limit', 2) }}"
                        min="0"
                        required
                    >
                </div>

                <div>
                    <label>Total Slot</label>

                    <input
                        type="number"
                        name="total_slot"
                        value="{{ old('total_slot', 10) }}"
                        min="0"
                        required
                    >
                </div>

                <div class="full">
                    <label>Deskripsi Paket</label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Jelaskan fungsi, target pengguna, dan hasil yang diperoleh dari paket ini."
                        required
                    >{{ old('description') }}</textarea>
                </div>

                <div class="full">
                    <label>
                        Isi Paket — satu item per baris
                    </label>

                    <textarea
                        name="includes_text"
                        rows="8"
                        placeholder="Pengeditan maksimal 5 foto&#10;Koreksi warna dan pencahayaan&#10;Retouching ringan&#10;File hasil JPG atau PNG"
                        required
                    >{{ old('includes_text') }}</textarea>
                </div>
            </div>

            <button type="submit">
                Simpan Paket
            </button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Paket</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Nama Paket</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Revisi</th>
                        <th>Slot</th>
                        <th>Status</th>
                        <th>Pesanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($packages as $package)
                        <tr>
                            <td>
                                {{ $package->service_type_label }}
                            </td>

                            <td>
                                <strong>
                                    {{ $package->name }}
                                </strong>

                                <div class="package-description">
                                    {{ \Illuminate\Support\Str::limit(
                                        $package->description,
                                        80
                                    ) }}
                                </div>
                            </td>

                            <td>
                                Rp{{ number_format(
                                    $package->price,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>
                                {{ $package->duration }}
                            </td>

                            <td>
                                {{ $package->revision_limit }}x
                            </td>

                            <td>
                                {{ $package->total_slot }}
                            </td>

                            <td>
                                <span
                                    class="badge {{
                                        $package->is_active
                                            ? 'status-active'
                                            : 'status-inactive'
                                    }}"
                                >
                                    {{ $package->is_active
                                        ? 'Aktif'
                                        : 'Nonaktif'
                                    }}
                                </span>
                            </td>

                            <td>
                                {{ $package->orders_count }}
                            </td>

                            <td>
                                <div class="package-actions">
                                    <details class="package-details">
                                        <summary>
                                            Lihat Detail
                                        </summary>

                                        <div class="package-detail-content">
                                            <h4>
                                                {{ $package->name }}
                                            </h4>

                                            <p>
                                                <strong>Jenis:</strong>
                                                {{ $package->service_type_label }}
                                            </p>

                                            <p>
                                                {{ $package->description }}
                                            </p>

                                            <strong>
                                                Sudah termasuk:
                                            </strong>

                                            <ul>
                                                @forelse(
                                                    $package->includes ?? []
                                                    as $include
                                                )
                                                    <li>
                                                        {{ $include }}
                                                    </li>
                                                @empty
                                                    <li>
                                                        Belum ada rincian isi paket.
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </details>

                                    <details class="package-details">
                                        <summary>
                                            Edit Paket
                                        </summary>

                                        <div class="edit-package-box">
                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.packages.update',
                                                    $package
                                                ) }}"
                                                class="package-form"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <label>Jenis Layanan</label>

                                                <select
                                                    name="service_type"
                                                    required
                                                >
                                                    @foreach(
                                                        \App\Models\ServicePackage::SERVICE_TYPES
                                                        as $serviceType
                                                    )
                                                        <option
                                                            value="{{ $serviceType }}"
                                                            @selected(
                                                                $package->service_type_label
                                                                === $serviceType
                                                            )
                                                        >
                                                            {{ $serviceType }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <label>Nama Paket</label>

                                                <input
                                                    type="text"
                                                    name="name"
                                                    value="{{ $package->name }}"
                                                    required
                                                >

                                                <label>Harga</label>

                                                <input
                                                    type="number"
                                                    name="price"
                                                    value="{{ $package->price }}"
                                                    min="0"
                                                    required
                                                >

                                                <label>Durasi</label>

                                                <input
                                                    type="text"
                                                    name="duration"
                                                    value="{{ $package->duration }}"
                                                    required
                                                >

                                                <label>Maksimal Revisi</label>

                                                <input
                                                    type="number"
                                                    name="revision_limit"
                                                    value="{{ $package->revision_limit }}"
                                                    min="0"
                                                    required
                                                >

                                                <label>Total Slot</label>

                                                <input
                                                    type="number"
                                                    name="total_slot"
                                                    value="{{ $package->total_slot }}"
                                                    min="0"
                                                    required
                                                >

                                                <label>Deskripsi</label>

                                                <textarea
                                                    name="description"
                                                    rows="5"
                                                    required
                                                >{{ $package->description }}</textarea>

                                                <label>
                                                    Isi Paket — satu item per baris
                                                </label>

                                                <textarea
                                                    name="includes_text"
                                                    rows="8"
                                                    required
                                                >{{ implode(
                                                    PHP_EOL,
                                                    $package->includes ?? []
                                                ) }}</textarea>

                                                <button type="submit">
                                                    Simpan Perubahan
                                                </button>
                                            </form>
                                        </div>
                                    </details>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.packages.status',
                                            $package
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="{{
                                                $package->is_active
                                                    ? 'warning-button'
                                                    : 'success'
                                            }}"
                                        >
                                            {{ $package->is_active
                                                ? 'Nonaktifkan'
                                                : 'Aktifkan'
                                            }}
                                        </button>
                                    </form>

                                    @if($package->orders_count === 0)
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.packages.delete',
                                                $package
                                            ) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="danger"
                                                onclick="
                                                    return confirm(
                                                        'Yakin ingin menghapus paket ini?'
                                                    )
                                                "
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <small style="color:#94a3b8;">
                                            Tidak dapat dihapus karena sudah digunakan.
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                Belum ada paket layanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection