<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_teams', function (Blueprint $table) {
            $table->dropColumn([
                'active_orders',
                'completed_orders',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('production_teams', function (Blueprint $table) {
            $table->unsignedInteger('active_orders')
                ->default(0);

            $table->unsignedInteger('completed_orders')
                ->default(0);
        });
    }
};