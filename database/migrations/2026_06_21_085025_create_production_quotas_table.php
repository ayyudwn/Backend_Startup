<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_quotas', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('max_quota')->default(5);
            $table->integer('used_quota')->default(0);
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_quotas');
    }
};