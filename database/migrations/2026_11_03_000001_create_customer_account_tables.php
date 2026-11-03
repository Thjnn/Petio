<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Older local installations may already contain these tables.
        if (! Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('label', 50);
                $table->string('receiver_name', 100);
                $table->string('receiver_phone', 20);
                $table->string('ship_province', 100);
                $table->string('ship_district', 100);
                $table->string('ship_ward', 100);
                $table->string('ship_address');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('customer_favorites')) {
            Schema::create('customer_favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }
        if (! Schema::hasTable('customer_notice_states')) {
            Schema::create('customer_notice_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('history_id')->constrained('order_status_history')->cascadeOnDelete();
                $table->timestamp('read_at')->nullable();
                $table->timestamp('hidden_at')->nullable();
                $table->unique(['user_id', 'history_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_notice_states');
        Schema::dropIfExists('customer_favorites');
        Schema::dropIfExists('customer_addresses');
    }
};
