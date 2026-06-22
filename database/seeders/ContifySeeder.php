<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductionQuota;
use App\Models\ProductionTeam;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContifySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@contify.test',
            ],
            [
                'name' => 'Admin Contify',
                'password' => Hash::make('AdminContify123!'),
                'role' => 'admin',
                'phone' => '081111111111',
            ]
        );

        $user = User::updateOrCreate(
            [
                'email' => 'user@contify.test',
            ],
            [
                'name' => 'UMKM Kuliner Bu Rina',
                'password' => Hash::make('UserContify123!'),
                'role' => 'user',
                'phone' => '082222222222',
            ]
        );

        $editFoto = ServicePackage::create([
            'name' => 'Edit Foto Produk',
            'description' => 'Layanan edit foto produk agar terlihat lebih menarik untuk media sosial.',
            'price' => 45000,
            'duration' => '1-3 hari',
            'revision_limit' => 1,
            'total_slot' => 10,
        ]);

        $video = ServicePackage::create([
            'name' => 'Video TikTok / Reels',
            'description' => 'Pembuatan video pendek untuk promosi produk di TikTok dan Instagram Reels.',
            'price' => 100000,
            'duration' => '2-4 hari',
            'revision_limit' => 2,
            'total_slot' => 5,
        ]);

        $copywriting = ServicePackage::create([
            'name' => 'Copywriting Konten',
            'description' => 'Pembuatan caption dan kalimat promosi untuk media sosial.',
            'price' => 35000,
            'duration' => '1-2 hari',
            'revision_limit' => 1,
            'total_slot' => 15,
        ]);

        ServicePackage::create([
            'name' => 'Strategi Konten Premium',
            'description' => 'Paket perencanaan konten untuk promosi UMKM secara lebih terarah.',
            'price' => 150000,
            'duration' => '3-5 hari',
            'revision_limit' => 2,
            'total_slot' => 3,
        ]);

        $order1 = Order::create([
            'order_code' => 'CTF-001',
            'user_id' => $user->id,
            'package_id' => $editFoto->id,
            'title' => 'Edit Foto Menu Bakso',
            'notes' => 'Tolong dibuat lebih cerah dan cocok untuk Instagram.',
            'booking_date' => now()->toDateString(),
            'deadline_type' => 'regular',
            'base_price' => 45000,
            'additional_price' => 0,
            'discount' => 0,
            'total_price' => 45000,
            'status' => 'pending',
            'priority' => 'Reguler',
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'method' => 'Transfer Bank',
            'amount' => 45000,
            'proof_image' => 'bukti-bayar-demo.jpg',
            'status' => 'pending',
        ]);

        $order2 = Order::create([
            'order_code' => 'CTF-002',
            'user_id' => $user->id,
            'package_id' => $video->id,
            'title' => 'Video Reels Promo Minuman',
            'notes' => 'Video singkat untuk promo es kopi.',
            'booking_date' => now()->addDay()->toDateString(),
            'deadline_type' => 'express',
            'base_price' => 100000,
            'additional_price' => 25000,
            'discount' => 0,
            'total_price' => 125000,
            'status' => 'queue',
            'priority' => 'Cepat',
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'method' => 'QRIS',
            'amount' => 125000,
            'proof_image' => 'bukti-qris-demo.jpg',
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $order3 = Order::create([
            'order_code' => 'CTF-003',
            'user_id' => $user->id,
            'package_id' => $copywriting->id,
            'title' => 'Caption Promo Diskon',
            'notes' => 'Caption untuk promo akhir pekan.',
            'booking_date' => now()->toDateString(),
            'deadline_type' => 'regular',
            'base_price' => 35000,
            'additional_price' => 0,
            'discount' => 5000,
            'total_price' => 30000,
            'status' => 'process',
            'priority' => 'Reguler',
        ]);

        Payment::create([
            'order_id' => $order3->id,
            'method' => 'Transfer Bank',
            'amount' => 30000,
            'proof_image' => 'bukti-demo.jpg',
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        ProductionQuota::create([
            'date' => now()->toDateString(),
            'max_quota' => 5,
            'used_quota' => 3,
            'status' => 'open',
        ]);

        ProductionQuota::create([
            'date' => now()->addDay()->toDateString(),
            'max_quota' => 5,
            'used_quota' => 5,
            'status' => 'full',
        ]);

        ProductionQuota::create([
            'date' => now()->addDays(2)->toDateString(),
            'max_quota' => 0,
            'used_quota' => 0,
            'status' => 'closed',
        ]);

        Voucher::create([
            'code' => 'CONTIFY20',
            'discount_percent' => 20,
            'usage_limit' => 50,
            'usage_count' => 5,
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'UMKM10',
            'discount_percent' => 10,
            'usage_limit' => 100,
            'usage_count' => 12,
            'is_active' => true,
        ]);

        ProductionTeam::create([
            'name' => 'Sari Rahayu',
            'role' => 'Editor Foto',
            'skills' => 'Edit foto produk, retouching, desain feed',
            'active_orders' => 2,
            'completed_orders' => 18,
            'status' => 'busy',
        ]);

        ProductionTeam::create([
            'name' => 'Nila Agustina',
            'role' => 'Videographer',
            'skills' => 'Video Reels, TikTok, editing video pendek',
            'active_orders' => 1,
            'completed_orders' => 11,
            'status' => 'available',
        ]);

        ProductionTeam::create([
            'name' => 'Dewi Lestari',
            'role' => 'Copywriter',
            'skills' => 'Caption, headline, copy promosi',
            'active_orders' => 0,
            'completed_orders' => 25,
            'status' => 'available',
        ]);
    }
}