<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mỗi chủ một giỏ: khách đã đăng nhập dùng user_id, khách vãng lai dùng session_token
        // (UUID lưu trong cookie). Đăng nhập xong thì gộp giỏ vãng lai vào giỏ của user.
        // Ít nhất một trong hai cột phải có giá trị — kiểm tra trong code vì MySQL không cho
        // đặt CHECK lên cột user_id đang có FK ON DELETE CASCADE.
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->char('session_token', 36)->nullable()->unique()->comment('UUID cookie cho khách vãng lai');
            $table->timestamps();
        });

        // Không lưu giá trong giỏ: giá luôn đọc từ product_variants để không lệch khi shop đổi giá.
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->dateTime('added_at')->useCurrent();
            $table->unique(['cart_id', 'variant_id']);
        });

        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_items_qty CHECK (quantity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
