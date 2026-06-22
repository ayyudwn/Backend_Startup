<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('voucher_id')
                ->nullable()
                ->after('package_id')
                ->constrained('vouchers')
                ->nullOnDelete();

            $table->string('reference_file')->nullable()->after('notes');
            $table->string('platform')->nullable()->after('reference_file');
            $table->string('content_size')->nullable()->after('platform');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);

            $table->dropColumn([
                'voucher_id',
                'reference_file',
                'platform',
                'content_size',
            ]);
        });
    }
};