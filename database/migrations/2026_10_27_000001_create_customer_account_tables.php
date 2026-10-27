<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('label', 50);
            $t->string('receiver_name', 100);
            $t->string('receiver_phone', 20);
            $t->string('ship_province', 100);
            $t->string('ship_district', 100);
            $t->string('ship_ward', 100);
            $t->string('ship_address');
            $t->boolean('is_default')->default(false);
            $t->timestamps();
        });
        Schema::create('customer_favorites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['user_id', 'product_id']);
        });
        Schema::create('customer_notice_states', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('history_id');
            $t->foreign('history_id')->references('id')->on('order_status_history')->cascadeOnDelete();
            $t->timestamp('read_at')->nullable();
            $t->timestamp('hidden_at')->nullable();
            $t->unique(['user_id', 'history_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_notice_states');
        Schema::dropIfExists('customer_favorites');
        Schema::dropIfExists('customer_addresses');
    }
};
