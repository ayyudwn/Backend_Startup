@extends('layouts.user')

@section('title', 'Layanan Contify')

@section('content')
    <style>
        .hero {
            position: relative;
            overflow: hidden;
            padding: 42px;
            background:
                linear-gradient(
                    135deg,
                    #020617 0%,
                    #0f172a 55%,
                    #0c4a6e 100%
                );
            color: white;
        }

        .hero::after {
            position: absolute;
            right: -80px;
            bottom: -100px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.12);
            content: '';
        }

        .hero-label {
            margin: 0 0 12px;
            color: #38bdf8;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1.5px;
        }

        .hero-title {
            max-width: 760px;
            margin: 0 0 14px;
            font-size: 40px;
            line-height: 1.2;
        }

        .hero-description {
            max-width: 720px;
            margin-bottom: 24px;
            color: #cbd5e1;
            line-height: 1.7;
        }

        .section-heading {
            margin-top: 34px;
            margin-bottom: 6px;
        }

        .section-description {
            margin-top: 0;
            margin-bottom: 22px;
            color: #64748b;
            line-height: 1.6;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(
                2,
                minmax(0, 1fr)
            );
            gap: 20px;
        }

        .service-card {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
            padding: 26px;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.12);
        }

        .service-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .service-name {
            margin: 0;
            color: #0f172a;
            font-size: 23px;
        }

        .service-badge {
            flex-shrink: 0;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 12px;
            font-weight: bold;
        }

        .service-description {
            min-height: 72px;
            margin: 16px 0;
            color: #64748b;
            line-height: 1.65;
        }

        .service-information {
            display: grid;
            grid-template-columns: repeat(
                3,
                minmax(0, 1fr)
            );
            gap: 10px;
            margin-bottom: 20px;
        }

        .service-information div {
            padding: 11px;
            border-radius: 10px;
            background: #f8fafc;
        }

        .service-information small {
            display: block;
            margin-bottom: 5px;
            color: #94a3b8;
            font-size: 11px;
        }

        .service-information strong {
            color: #334155;
            font-size: 13px;
        }

        .include-title {
            margin: 0 0 12px;
            color: #334155;
            font-size: 15px;
        }

        .include-list {
            flex-grow: 1;
            margin: 0 0 24px;
            padding: 0;
            list-style: none;
        }

        .include-list li {
            position: relative;
            margin-bottom: 10px;
            padding-left: 25px;
            color: #475569;
            line-height: 1.5;
        }

        .include-list li::before {
            position: absolute;
            top: 1px;
            left: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            content: '✓';
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            line-height: 18px;
        }

        .service-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
        }

        .service-price-label {
            display: block;
            margin-bottom: 4px;
            color: #94a3b8;
            font-size: 12px;
        }

        .service-price {
            margin: 0;
            color: #0284c7;
            font-size: 24px;
        }

        .empty-service {
            grid-column: 1 / -1;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 850px) {
            .hero {
                padding: 28px;
            }

            .hero-title {
                font-size: 31px;
            }

            .service-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .service-top,
            .service-footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .service-information {
                grid-template-columns: 1fr;
            }

            .service-footer .button {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <section class="card hero">
        <p class="hero-label">
            SOLUSI KONTEN UNTUK UMKM
        </p>

        <h1 class="hero-title">
            Buat kebutuhan konten bisnis dengan proses
            yang lebih mudah dan terarah.
        </h1>

        <p class="hero-description">
            Pilih layanan yang sesuai, tentukan jadwal produksi,
            lihat perhitungan harga secara transparan, lalu pantau
            seluruh proses pengerjaan melalui Contify.
        </p>

        <a
            class="button"
            href="{{ route('user.orders.create') }}"
        >
            Pesan Layanan
        </a>
    </section>

    <h2 class="section-heading">
        Pilih Layanan
    </h2>

    <p class="section-description">
        Setiap layanan memiliki hasil, cakupan pengerjaan,
        durasi, dan batas revisi yang berbeda.
    </p>

    <div class="service-grid">
        @forelse($packages as $package)
            <article class="card service-card">
                <div class="service-top">
                    <h3 class="service-name">
                        {{ $package->name }}
                    </h3>

                    <span class="service-badge">
                        Layanan Aktif
                    </span>
                </div>

                <p class="service-description">
                    {{ $package->description }}
                </p>

                <div class="service-information">
                    <div>
                        <small>Durasi</small>

                        <strong>
                            {{ $package->duration ?? '-' }}
                        </strong>
                    </div>

                    <div>
                        <small>Maksimal Revisi</small>

                        <strong>
                            {{ $package->revision_limit }} kali
                        </strong>
                    </div>

                    <div>
                        <small>Kapasitas</small>

                        <strong>
                            {{ $package->total_slot }} slot
                        </strong>
                    </div>
                </div>

                <h4 class="include-title">
                    Layanan ini sudah termasuk:
                </h4>

                <ul class="include-list">
                    @forelse($package->includes ?? [] as $include)
                        <li>
                            {{ $include }}
                        </li>
                    @empty
                        <li>
                            Detail layanan belum tersedia.
                        </li>
                    @endforelse
                </ul>

                <div class="service-footer">
                    <div>
                        <span class="service-price-label">
                            Harga mulai dari
                        </span>

                        <h2 class="service-price">
                            Rp{{ number_format(
                                $package->price,
                                0,
                                ',',
                                '.'
                            ) }}
                        </h2>
                    </div>

                    <a
                        class="button"
                        href="{{ route(
                            'user.orders.create',
                            [
                                'package' => $package->id,
                            ]
                        ) }}"
                    >
                        Pilih Layanan
                    </a>
                </div>
            </article>
        @empty
            <div class="card empty-service">
                Belum ada layanan Contify yang aktif.
            </div>
        @endforelse
    </div>
@endsection

