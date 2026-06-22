<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->date('booking_date');
            $table->string('deadline_type')->default('regular');
            $table->integer('base_price');
            $table->integer('additional_price')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('total_price');
            $table->string('status')->default('pending');
            $table->string('priority')->default('Reguler');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};