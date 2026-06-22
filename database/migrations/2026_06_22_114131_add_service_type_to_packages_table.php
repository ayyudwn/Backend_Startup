<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('service_type')
                ->nullable()
                ->after('name');
        });

        $serviceTypes = [
            'Edit Foto',
            'Video TikTok / Reels',
            'Copy Writing',
            'Strategi Konten',
        ];

        foreach ($serviceTypes as $serviceType) {
            DB::table('packages')
                ->where('name', $serviceType)
                ->update([
                    'service_type' => $serviceType,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};