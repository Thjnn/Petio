
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sổ địa chỉ giao hàng
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('receiver_name', 100);
                $table->string('receiver_phone', 20);
                $table->string('province', 100)->comment('Tỉnh/Thành phố');
                $table->string('district', 100)->comment('Quận/Huyện');
                $table->string('ward', 100)->comment('Phường/Xã');
                $table->string('address_line')->comment('Số nhà, tên đường');
                $table->boolean('is_default')->default(false);
                $table->dateTime('created_at')->useCurrent();
            });
        }

        // 2. Ảnh phụ sản phẩm
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('image_url');
                $table->string('alt_text', 200)->nullable();
                $table->smallInteger('sort_order')->default(0);
                $table->index(['product_id', 'sort_order']);
            });
        }

        // 3. Đánh giá sản phẩm
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->enum('status', ['pending', 'approved', 'hidden'])->default('pending');
                $table->dateTime('created_at')->useCurrent();
                $table->unique(['user_id', 'product_id']);
                $table->index(['product_id', 'status']);
            });

            DB::statement(
                'ALTER TABLE product_reviews
                 ADD CONSTRAINT chk_reviews_rating
                 CHECK (rating BETWEEN 1 AND 5)'
            );
        }

        // 4. Danh sách yêu thích
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->dateTime('created_at')->useCurrent();
                $table->primary(['user_id', 'product_id']);
            });
        }

        // 5. Lịch hẹn spa
        if (!Schema::hasTable('spa_appointments')) {
            Schema::create('spa_appointments', function (Blueprint $table) {
                $table->id();
                $table->string('appointment_code', 20)->unique();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('contact_name', 100);
                $table->string('contact_phone', 20);
                $table->string('pet_name', 80);
                $table->enum('pet_species', ['dog', 'cat']);
                $table->string('pet_breed', 80)->nullable();
                $table->decimal('pet_weight_kg', 5, 1);
                $table->dateTime('appointment_at')->comment('Giờ hẹn mang thú cưng tới');
                $table->boolean('need_pickup')->default(false)->comment('Dịch vụ đưa đón thú cưng');
                $table->string('pickup_address')->nullable();
                $table->string('note', 500)->nullable();
                $table->unsignedInteger('total_price')->default(0);
                $table->enum('status', [
                    'pending',
                    'confirmed',
                    'in_progress',
                    'completed',
                    'cancelled',
                    'no_show'
                ])->default('pending');

                $table->timestamps();
                $table->index(['appointment_at', 'status']);
            });

            DB::statement(
                'ALTER TABLE spa_appointments
                 ADD CONSTRAINT chk_spa_appt_weight
                 CHECK (pet_weight_kg > 0)'
            );
        }

        // 6. Dịch vụ trong lịch hẹn spa
        if (!Schema::hasTable('spa_appointment_services')) {
            Schema::create('spa_appointment_services', function (Blueprint $table) {
                $table->foreignId('appointment_id')
                    ->constrained('spa_appointments')
                    ->cascadeOnDelete();

                $table->foreignId('service_id')
                    ->constrained('spa_services')
                    ->restrictOnDelete();

                $table->unsignedInteger('price');

                $table->primary(['appointment_id', 'service_id']);
            });
        }

        // 7. Nhật ký hoạt động admin
        if (!Schema::hasTable('admin_activity_logs')) {
            Schema::create('admin_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->enum('action', [
                    'create',
                    'update',
                    'delete',
                    'activate',
                    'deactivate'
                ]);

                $table->enum('entity_type', [
                    'product',
                    'product_variant',
                    'category',
                    'brand',
                    'banner',
                    'blog_post',
                    'blog_category',
                    'coupon',
                    'order',
                    'user'
                ]);

                $table->unsignedBigInteger('entity_id')->nullable();
                $table->string('entity_label')->nullable();
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->dateTime('created_at')->useCurrent();

                $table->index(['admin_id', 'created_at']);
                $table->index(['entity_type', 'entity_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('spa_appointment_services');
        Schema::dropIfExists('spa_appointments');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('user_addresses');
    }
};
