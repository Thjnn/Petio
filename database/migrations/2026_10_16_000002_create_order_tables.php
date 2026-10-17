<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('description')->nullable();
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->unsignedInteger('discount_value')->comment('percent: 1-100 | fixed: số tiền');
            $table->unsignedInteger('max_discount')->nullable()->comment('Trần giảm cho loại percent');
            $table->unsignedInteger('min_order_value')->default(0);
            $table->boolean('first_order_only')->default(false);
            $table->unsignedInteger('usage_limit')->nullable()->comment('NULL = không giới hạn');
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Thông tin nhận hàng và giá được lưu snapshot: khách sửa địa chỉ hay shop đổi giá
        // về sau cũng không làm sai đơn cũ.
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('NULL = khách vãng lai');
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20);
            $table->string('receiver_email', 150)->nullable();
            $table->string('ship_province', 100);
            $table->string('ship_district', 100);
            $table->string('ship_ward', 100);
            $table->string('ship_address');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('shipping_fee')->default(0);
            $table->unsignedInteger('total_amount')->comment('= subtotal - discount_amount + shipping_fee');
            $table->enum('payment_method', ['cod', 'bank_transfer', 'momo', 'vnpay'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->dateTime('paid_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'returned'])->default('pending');
            $table->string('note', 500)->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name', 200);
            $table->string('variant_name', 100);
            $table->string('sku', 50)->nullable();
            $table->unsignedInteger('unit_price');
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('line_total')->storedAs('unit_price * quantity');
        });

        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
    }
};
