<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Danh mục dạng cây (cha – con). "Dành cho chó / mèo" không nằm ở đây mà là products.pet_type.
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->restrictOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140)->unique()->comment('Khớp ?cat=... trên URL, vd: thuc-an');
            $table->string('image_url')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->string('logo_url')->nullable();
            $table->boolean('is_active')->default(true);
        });

        // Giá và tồn kho nằm ở product_variants vì một sản phẩm có nhiều size/giá.
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 200)->index();
            $table->string('slug', 220)->unique();
            $table->enum('pet_type', ['dog', 'cat', 'both'])->default('both')->index();
            $table->string('short_description', 500)->nullable();
            $table->mediumText('description')->nullable()->comment('HTML');
            $table->string('thumbnail_url')->nullable();
            $table->decimal('rating_avg', 2, 1)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->boolean('is_featured')->default(false)->comment('Mục "Mặt hàng được yêu thích nhất" trang chủ');
            $table->boolean('is_active')->default(true)->comment('0 = ẩn (xóa mềm)');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['category_id', 'is_active']);
        });

        // Mỗi dòng là một lựa chọn mua được. Sản phẩm không có size thì có đúng 1 biến thể "Mặc định".
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('name', 100)->default('Mặc định');
            $table->unsignedInteger('price')->index()->comment('VND');
            $table->unsignedInteger('old_price')->nullable()->comment('Giá gạch ngang; NULL = không giảm');
            $table->unsignedInteger('stock_qty')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'name']);
        });

        // Tag dùng chung cho sản phẩm (Hot, Trend) và bài viết (Medical, Food...).
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('slug', 80)->unique();
        });

        Schema::create('product_tags', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
