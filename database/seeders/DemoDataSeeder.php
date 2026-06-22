<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderResult;
use App\Models\Payment;
use App\Models\ProductionQuota;
use App\Models\ProductionTeam;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    private int $orderSequence = 1;

    private array $customerCycle = [
        'andi',
        'bunga',
        'citra',
        'annisa',
        'eka',
        'farhan',
    ];

    private int $customerCycleIndex = 0;

    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('demo');

        DB::transaction(function (): void {
            $this->clearApplicationData();

            $users = $this->seedUsers();
            $packages = $this->seedPackages();
            $teams = $this->seedProductionTeams();
            $vouchers = $this->seedVouchers();

            $this->seedProductionQuotas();

            $this->seedOrders(
                $users,
                $packages,
                $teams,
                $vouchers
            );
        });

        $this->command?->newLine();

        $this->command?->info(
            'Data demo Contify berhasil dibuat.'
        );

        $this->command?->table(
            [
                'Tim',
                'Aktif',
                'Selesai',
                'Status',
            ],
            [
                [
                    'Sari Rahayu',
                    5,
                    3,
                    'Busy',
                ],
                [
                    'Nila Agustina',
                    2,
                    2,
                    'Available',
                ],
                [
                    'Dewi Lestari',
                    1,
                    4,
                    'Available',
                ],
                [
                    'Fajar Pratama',
                    0,
                    2,
                    'Available',
                ],
                [
                    'Raka Putra',
                    0,
                    1,
                    'Offline',
                ],
            ]
        );
    }

    private function clearApplicationData(): void
    {
        OrderResult::query()->delete();
        Payment::query()->delete();
        Order::query()->delete();
        ProductionQuota::query()->delete();
        Voucher::query()->delete();
        ProductionTeam::query()->delete();
        ServicePackage::query()->delete();

        DB::table('sessions')->delete();
        DB::table('password_reset_tokens')->delete();

        User::query()->delete();
    }

    private function seedUsers(): array
    {
        $users = [];

        $users['admin'] = User::query()->create([
            'name' => 'Admin Contify',
            'email' => 'admin@contify.test',
            'phone' => '081200000001',
            'password' => Hash::make(
                'AdminContify123!'
            ),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $customers = [
            'andi' => [
                'Andi Pratama',
                'andi@contify.test',
                '081211110001',
                true,
            ],

            'bunga' => [
                'Bunga Lestari',
                'bunga@contify.test',
                '081211110002',
                true,
            ],

            'citra' => [
                'Citra Maharani',
                'citra@contify.test',
                '081211110003',
                true,
            ],

            'annisa' => [
                'Annisa nisa',
                'annisa@contify.test',
                '081211110004',
                true,
            ],

            'eka' => [
                'Eka Putri',
                'eka@contify.test',
                '081211110005',
                true,
            ],

            'farhan' => [
                'Farhan Akbar',
                'farhan@contify.test',
                '081211110006',
                true,
            ],

            'gina' => [
                'Gina Ramadhani',
                'gina@contify.test',
                '081211110007',
                false,
            ],
        ];

        foreach (
            $customers as
            $key => [
                $name,
                $email,
                $phone,
                $isActive,
            ]
        ) {
            $users[$key] = User::query()->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make(
                    'UserContify123!'
                ),
                'role' => 'user',
                'is_active' => $isActive,
            ]);
        }

        return $users;
    }

    private function seedPackages(): array
    {
        return [
            'edit_foto' => ServicePackage::query()->create([
                'name' => 'Edit Foto',

                'description' =>
                    'Layanan pengeditan foto produk atau promosi agar terlihat lebih bersih, menarik, dan siap digunakan di media sosial maupun marketplace.',

                'includes' => [
                    'Pengeditan maksimal 5 foto',
                    'Koreksi warna dan pencahayaan',
                    'Retouching ringan',
                    'Penyesuaian atau penghapusan background',
                    'Penyesuaian ukuran untuk satu platform',
                    'File hasil JPG atau PNG',
                    'Maksimal 2 kali revisi',
                ],

                'price' => 150000,
                'duration' => '2-3 hari',
                'revision_limit' => 2,
                'total_slot' => 10,
                'is_active' => true,
            ]),

            'video' => ServicePackage::query()->create([
                'name' => 'Video / Reels',

                'description' =>
                    'Layanan pengeditan video vertikal untuk TikTok, Instagram Reels, dan promosi singkat dengan tampilan yang lebih menarik.',

                'includes' => [
                    'Satu video vertikal maksimal 60 detik',
                    'Pemotongan dan penyusunan video',
                    'Transisi dasar',
                    'Penambahan teks pada video',
                    'Musik atau sound effect',
                    'Koreksi warna dasar',
                    'Format 1080 x 1920',
                    'Maksimal 2 kali revisi',
                ],

                'price' => 350000,
                'duration' => '4-5 hari',
                'revision_limit' => 2,
                'total_slot' => 8,
                'is_active' => true,
            ]),

            'copy_writing' => ServicePackage::query()->create([
                'name' => 'Copy Writing',

                'description' =>
                    'Layanan penulisan teks promosi yang disesuaikan dengan karakter bisnis, target audiens, dan tujuan konten.',

                'includes' => [
                    'Penulisan maksimal 5 caption',
                    'Headline atau kalimat pembuka',
                    'Isi caption promosi',
                    'Call to action',
                    'Rekomendasi hashtag',
                    'Penyesuaian tone komunikasi',
                    'Maksimal 2 kali revisi',
                ],

                'price' => 200000,
                'duration' => '2-3 hari',
                'revision_limit' => 2,
                'total_slot' => 12,
                'is_active' => true,
            ]),

            'strategi' => ServicePackage::query()->create([
                'name' => 'Strategi Konten',

                'description' =>
                    'Layanan penyusunan arah dan rencana konten agar bisnis memiliki topik, jadwal, dan tujuan publikasi yang lebih terstruktur.',

                'includes' => [
                    'Analisis singkat akun atau bisnis',
                    'Penentuan target audiens',
                    'Penentuan content pillar',
                    'Rencana konten selama 30 hari',
                    'Minimal 12 ide konten',
                    'Rekomendasi jadwal publikasi',
                    'Rekomendasi format konten',
                    'Rekomendasi indikator evaluasi konten',
                    'Maksimal 2 kali revisi',
                ],

                'price' => 500000,
                'duration' => '5-7 hari',
                'revision_limit' => 2,
                'total_slot' => 6,
                'is_active' => true,
            ]),
        ];
    }

    private function seedProductionTeams(): array
    {
        return [
            'sari' => ProductionTeam::query()->create([
                'name' => 'Sari Rahayu',

                'role' =>
                    'Visual Editor & Content Planner',

                'skills' => [
                    'Edit Foto',
                    'Strategi Konten',
                ],

                'status' => 'available',
            ]),

            'nila' => ProductionTeam::query()->create([
                'name' => 'Nila Agustina',

                'role' => 'Video Editor',

                'skills' => [
                    'Video / Reels',
                    'Edit Foto',
                ],

                'status' => 'available',
            ]),

            'dewi' => ProductionTeam::query()->create([
                'name' => 'Dewi Lestari',

                'role' =>
                    'Copywriter & Content Strategist',

                'skills' => [
                    'Copy Writing',
                    'Strategi Konten',
                ],

                'status' => 'available',
            ]),

            'fajar' => ProductionTeam::query()->create([
                'name' => 'Fajar Pratama',

                'role' => 'Content Specialist',

                'skills' => [
                    'Edit Foto',
                    'Copy Writing',
                ],

                'status' => 'available',
            ]),

            'raka' => ProductionTeam::query()->create([
                'name' => 'Raka Putra',

                'role' =>
                    'Video Specialist & Content Planner',

                'skills' => [
                    'Video / Reels',
                    'Strategi Konten',
                ],

                'status' => 'offline',
            ]),
        ];
    }

    private function seedVouchers(): array
    {
        return [
            'WELCOME10' => Voucher::query()->create([
                'code' => 'WELCOME10',
                'discount_percent' => 10,
                'usage_limit' => 100,
                'usage_count' => 0,
                'is_active' => true,
            ]),

            'UMKM20' => Voucher::query()->create([
                'code' => 'UMKM20',
                'discount_percent' => 20,
                'usage_limit' => 50,
                'usage_count' => 0,
                'is_active' => true,
            ]),

            'HEMAT15' => Voucher::query()->create([
                'code' => 'HEMAT15',
                'discount_percent' => 15,
                'usage_limit' => 30,
                'usage_count' => 0,
                'is_active' => false,
            ]),
        ];
    }

    private function seedProductionQuotas(): void
    {
        $quotas = [
            [0, 8, 5, 'open'],
            [1, 8, 3, 'open'],
            [2, 5, 5, 'full'],
            [3, 0, 0, 'closed'],
            [4, 10, 2, 'open'],
            [5, 10, 0, 'open'],
            [6, 8, 1, 'open'],
            [7, 8, 0, 'open'],
            [8, 10, 4, 'open'],
            [9, 10, 0, 'open'],
            [10, 6, 0, 'open'],
            [11, 6, 0, 'open'],
            [12, 6, 0, 'open'],
            [13, 6, 0, 'open'],
            [14, 6, 0, 'open'],
        ];

        foreach (
            $quotas as
            [
                $day,
                $max,
                $used,
                $status,
            ]
        ) {
            ProductionQuota::query()->create([
                'date' => now()
                    ->addDays($day)
                    ->toDateString(),

                'max_quota' => $max,
                'used_quota' => $used,
                'status' => $status,
            ]);
        }
    }

    private function seedOrders(
        array $users,
        array $packages,
        array $teams,
        array $vouchers
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Pesanan belum diverifikasi
        |--------------------------------------------------------------------------
        */

        $pendingOrders = [
            [
                'customer' => 'andi',
                'package' => 'edit_foto',
                'title' => 'Edit Foto Produk Kopi',
                'deadline_type' => 'regular',
                'payment_status' => 'pending',
                'proof' => true,
                'created_days_ago' => 0,
            ],

            [
                'customer' => 'bunga',
                'package' => 'video',
                'title' => 'Video Reels Promo Skincare',
                'deadline_type' => 'express',
                'payment_status' => 'pending',
                'proof' => true,
                'voucher' => 'WELCOME10',
                'created_days_ago' => 0,
            ],

            [
                'customer' => 'citra',
                'package' => 'copy_writing',
                'title' => 'Caption Launching Produk Baru',
                'deadline_type' => 'regular',
                'payment_status' => 'pending',
                'proof' => true,
                'created_days_ago' => 1,
            ],

            [
                'customer' => 'annisa',
                'package' => 'strategi',
                'title' => 'Strategi Konten Kedai Makanan',
                'deadline_type' => 'kilat',
                'payment_status' => 'pending',
                'proof' => true,
                'voucher' => 'UMKM20',
                'created_days_ago' => 1,
            ],

            /*
            | Belum mengunggah bukti pembayaran
            */

            [
                'customer' => 'eka',
                'package' => 'edit_foto',
                'title' => 'Edit Foto Menu Digital',
                'deadline_type' => 'regular',
                'payment_status' => 'pending',
                'proof' => false,
                'created_days_ago' => 2,
            ],

            /*
            | Bukti pembayaran ditolak
            */

            [
                'customer' => 'farhan',
                'package' => 'video',
                'title' => 'Video TikTok Promo Laundry',
                'deadline_type' => 'regular',
                'payment_status' => 'rejected',
                'proof' => true,
                'created_days_ago' => 3,
            ],
        ];

        foreach ($pendingOrders as $definition) {
            $this->createDemoOrder(
                array_merge(
                    [
                        'team' => null,
                        'status' => 'pending',

                        'updated_days_ago' =>
                            $definition[
                                'created_days_ago'
                            ],
                    ],
                    $definition
                ),
                $users,
                $packages,
                $teams,
                $vouchers
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Beban kerja dan hasil tim produksi
        |--------------------------------------------------------------------------
        */

        $workloads = [
            'sari' => [
                'active' => [
                    [
                        'edit_foto',
                        'queue',
                        'Edit Foto Katalog Kopi',
                        'regular',
                        6,
                        2,
                    ],
                    [
                        'strategi',
                        'queue',
                        'Content Plan Toko Skincare',
                        'regular',
                        7,
                        2,
                    ],
                    [
                        'edit_foto',
                        'process',
                        'Retouch Foto Fashion',
                        'express',
                        8,
                        1,
                    ],
                    [
                        'strategi',
                        'process',
                        'Strategi Konten Restoran',
                        'express',
                        9,
                        1,
                    ],
                    [
                        'edit_foto',
                        'review',
                        'Poster Promo Akhir Bulan',
                        'kilat',
                        10,
                        0,
                    ],
                ],

                'done' => [
                    [
                        'edit_foto',
                        'Edit Foto Promo Ramadan',
                        'regular',
                        25,
                        20,
                    ],
                    [
                        'strategi',
                        'Strategi Konten Toko Makanan',
                        'regular',
                        20,
                        15,
                    ],
                    [
                        'edit_foto',
                        'Edit Foto Katalog Bunga',
                        'express',
                        16,
                        0,
                    ],
                ],
            ],

            'nila' => [
                'active' => [
                    [
                        'video',
                        'queue',
                        'Video Reels Profil Bisnis',
                        'regular',
                        0,
                        0,
                    ],
                    [
                        'edit_foto',
                        'process',
                        'Edit Foto Produk Minuman',
                        'express',
                        6,
                        0,
                    ],
                ],

                'done' => [
                    [
                        'video',
                        'Video Testimoni Pelanggan',
                        'regular',
                        18,
                        12,
                    ],
                    [
                        'edit_foto',
                        'Edit Foto Launching Kedai',
                        'kilat',
                        15,
                        9,
                    ],
                ],
            ],

            'dewi' => [
                'active' => [
                    [
                        'copy_writing',
                        'review',
                        'Caption Kampanye Produk',
                        'regular',
                        7,
                        0,
                    ],
                ],

                'done' => [
                    [
                        'copy_writing',
                        'Caption Promo Mingguan',
                        'regular',
                        14,
                        10,
                    ],
                    [
                        'strategi',
                        'Rencana Konten Produk Fashion',
                        'express',
                        13,
                        8,
                    ],
                    [
                        'copy_writing',
                        'Caption Menu Baru',
                        'regular',
                        12,
                        7,
                    ],
                    [
                        'strategi',
                        'Strategi Konten Satu Bulan',
                        'regular',
                        11,
                        5,
                    ],
                ],
            ],

            'fajar' => [
                'active' => [],

                'done' => [
                    [
                        'edit_foto',
                        'Edit Foto Identitas Brand',
                        'regular',
                        10,
                        4,
                    ],
                    [
                        'copy_writing',
                        'Copy Writing Promo Kedai',
                        'regular',
                        9,
                        3,
                    ],
                ],
            ],

            'raka' => [
                'active' => [],

                'done' => [
                    [
                        'video',
                        'Video Dokumentasi Produk',
                        'regular',
                        8,
                        2,
                    ],
                ],
            ],
        ];

        foreach (
            $workloads as
            $teamKey => $workload
        ) {
            foreach (
                $workload['active'] as
                [
                    $package,
                    $status,
                    $title,
                    $deadline,
                    $created,
                    $updated,
                ]
            ) {
                $this->createDemoOrder(
                    [
                        'customer' =>
                            $this->nextCustomer(),

                        'package' => $package,
                        'team' => $teamKey,
                        'title' => $title,

                        'deadline_type' =>
                            $deadline,

                        'status' => $status,

                        'payment_status' =>
                            'verified',

                        'proof' => true,

                        'created_days_ago' =>
                            $created,

                        'updated_days_ago' =>
                            $updated,
                    ],
                    $users,
                    $packages,
                    $teams,
                    $vouchers
                );
            }

            foreach (
                $workload['done'] as
                [
                    $package,
                    $title,
                    $deadline,
                    $created,
                    $updated,
                ]
            ) {
                $this->createDemoOrder(
                    [
                        'customer' =>
                            $this->nextCustomer(),

                        'package' => $package,
                        'team' => $teamKey,
                        'title' => $title,

                        'deadline_type' =>
                            $deadline,

                        'status' => 'done',

                        'payment_status' =>
                            'verified',

                        'proof' => true,

                        'created_days_ago' =>
                            $created,

                        'updated_days_ago' =>
                            $updated,
                    ],
                    $users,
                    $packages,
                    $teams,
                    $vouchers
                );
            }
        }
    }

    private function createDemoOrder(
        array $definition,
        array $users,
        array $packages,
        array $teams,
        array $vouchers
    ): void {
        $package = $packages[
            $definition['package']
        ];

        $voucherCode =
            $definition['voucher'] ?? null;

        $voucher = $voucherCode
            ? $vouchers[$voucherCode]
            : null;

        $serviceInfo = $this->serviceInfo(
            $definition['package']
        );

        $code = sprintf(
            'CTF-DEMO-%03d',
            $this->orderSequence++
        );

        $basePrice = (int) $package->price;

        $additionalPrice = match (
            $definition['deadline_type']
        ) {
            'express' => (int) round(
                $basePrice * 0.25
            ),

            'kilat' => (int) round(
                $basePrice * 0.50
            ),

            default => 0,
        };

        $subtotal =
            $basePrice + $additionalPrice;

        $discount = $voucher
            ? (int) round(
                $subtotal
                * (
                    $voucher->discount_percent
                    / 100
                )
            )
            : 0;

        $totalPrice = max(
            0,
            $subtotal - $discount
        );

        $createdAt = now()->subDays(
            $definition['created_days_ago']
        );

        $updatedAt = now()->subDays(
            $definition['updated_days_ago']
        );

        $order = Order::query()->create([
            'order_code' => $code,

            'user_id' =>
                $users[
                    $definition['customer']
                ]->id,

            'production_team_id' =>
                $definition['team']
                    ? $teams[
                        $definition['team']
                    ]->id
                    : null,

            'package_id' => $package->id,

            'voucher_id' =>
                $voucher?->id,

            'title' => $definition['title'],

            'notes' =>
                $definition['notes']
                ?? $serviceInfo['notes'],

            'reference_file' => null,

            'platform' =>
                $serviceInfo['platform'],

            'content_size' =>
                $serviceInfo[
                    'content_size'
                ],

            'booking_date' => $createdAt
                ->copy()
                ->addDays(2)
                ->toDateString(),

            'deadline_type' =>
                $definition[
                    'deadline_type'
                ],

            'base_price' => $basePrice,

            'additional_price' =>
                $additionalPrice,

            'discount' => $discount,

            'total_price' => $totalPrice,

            'status' =>
                $definition['status'],

            'priority' => match (
                $definition[
                    'deadline_type'
                ]
            ) {
                'express' => 'Cepat',
                'kilat' => 'Kilat',
                default => 'Reguler',
            },
        ]);

        DB::table('orders')
            ->where('id', $order->id)
            ->update([
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

        $proofPath = $definition['proof']
            ? $this->createProofImage(
                $code
            )
            : null;

        $verifiedAt = null;

        if (
            $definition['payment_status']
            === 'verified'
        ) {
            $verifiedAt = $createdAt
                ->copy()
                ->addHours(4);

            if (
                $verifiedAt->greaterThan(
                    now()
                )
            ) {
                $verifiedAt = now();
            }
        }

        $payment = Payment::query()->create([
            'order_id' => $order->id,

            'method' =>
                $this->paymentMethodFor(
                    $code
                ),

            'amount' => $totalPrice,

            'proof_image' => $proofPath,

            'status' =>
                $definition[
                    'payment_status'
                ],

            'verified_at' => $verifiedAt,
        ]);

        DB::table('payments')
            ->where('id', $payment->id)
            ->update([
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

        if ($voucher) {
            $voucher->increment(
                'usage_count'
            );
        }

        if (
            $definition['status']
            === 'done'
        ) {
            $this->createOrderResult(
                $order,
                $updatedAt
            );
        }
    }

    private function serviceInfo(
        string $packageKey
    ): array {
        return match ($packageKey) {
            'video' => [
                'platform' => 'TikTok',

                'content_size' =>
                    'Story 9:16',

                'notes' =>
                    'Video vertikal dengan teks promosi, musik, dan penyuntingan dasar.',
            ],

            'copy_writing' => [
                'platform' => 'Instagram',

                'content_size' =>
                    'Caption',

                'notes' =>
                    'Penulisan caption promosi dengan headline dan call to action.',
            ],

            'strategi' => [
                'platform' => 'Instagram',

                'content_size' =>
                    'Content Plan',

                'notes' =>
                    'Penyusunan target audiens, content pillar, dan rencana publikasi.',
            ],

            default => [
                'platform' => 'Instagram',

                'content_size' =>
                    'Feed 1:1',

                'notes' =>
                    'Koreksi warna, pencahayaan, dan retouching ringan untuk foto produk.',
            ],
        };
    }

    private function nextCustomer(): string
    {
        $customer = $this->customerCycle[
            $this->customerCycleIndex
            % count(
                $this->customerCycle
            )
        ];

        $this->customerCycleIndex++;

        return $customer;
    }

    private function createProofImage(
        string $orderCode
    ): string {
        $path =
            'demo/payment-proofs/'
            . strtolower($orderCode)
            . '.png';

        $image = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
        );

        Storage::disk('public')->put(
            $path,
            $image
        );

        return $path;
    }

    private function createOrderResult(
        Order $order,
        Carbon $completedAt
    ): void {
        $fileName =
            'hasil-'
            . strtolower(
                $order->order_code
            )
            . '.txt';

        $filePath =
            'demo/order-results/'
            . $fileName;

        Storage::disk('public')->put(
            $filePath,
            "Hasil demo untuk pesanan {$order->order_code}."
        );

        $result = OrderResult::query()->create([
            'order_id' => $order->id,
            'file_name' => $fileName,
            'file_path' => $filePath,

            'notes' =>
                'File hasil demo pesanan yang sudah selesai.',
        ]);

        DB::table('order_results')
            ->where('id', $result->id)
            ->update([
                'created_at' =>
                    $completedAt,

                'updated_at' =>
                    $completedAt,
            ]);
    }

    private function paymentMethodFor(
        string $orderCode
    ): string {
        $number = (int) substr(
            $orderCode,
            -3
        );

        return match ($number % 3) {
            1 => 'Transfer Bank',
            2 => 'QRIS',
            default => 'E-Wallet',
        };
    }
}