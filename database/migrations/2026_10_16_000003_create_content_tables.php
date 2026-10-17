<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---------- Spa ----------
        // by_weight: giá tra theo mốc cân nặng ở spa_service_prices
        // range    : giá từ price_from đến price_to, nhân viên chốt khi làm
        Schema::create('spa_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('description', 500)->nullable();
            $table->enum('pricing_type', ['by_weight', 'range'])->default('by_weight');
            $table->unsignedInteger('price_from')->nullable();
            $table->unsignedInteger('price_to')->nullable();
            $table->unsignedSmallInteger('duration_min')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
        });

        // weight_over_kg < cân nặng <= weight_up_to_kg
        Schema::create('spa_service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('spa_services')->cascadeOnDelete();
            $table->decimal('weight_over_kg', 5, 1);
            $table->decimal('weight_up_to_kg', 5, 1);
            $table->unsignedInteger('price');
            $table->unique(['service_id', 'weight_over_kg']);
        });

        // ---------- Tin tức ----------
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug', 280)->unique();
            $table->string('summary', 500)->nullable();
            $table->mediumText('content')->comment('HTML');
            $table->string('thumbnail_url')->nullable();
            $table->enum('status', ['draft', 'published', 'hidden'])->default('draft');
            $table->unsignedInteger('view_count')->default(0);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'published_at']);
        });

        Schema::create('post_tags', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'tag_id']);
        });

        // FAQ chia mục: "01. Đặt hàng", "02. Lô hàng"...
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('section_title', 120);
            $table->smallInteger('section_order')->default(0);
            $table->string('question', 500);
            $table->text('answer');
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->index(['is_active', 'section_order', 'sort_order']);
        });

        // Banner trang chủ, admin thay ảnh/chữ không cần sửa code.
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->enum('position', ['home_hero', 'home_category', 'home_promo', 'home_wide']);
            $table->string('title', 200)->nullable()->comment('Xuống dòng = ký tự LF');
            $table->string('subtitle', 200)->nullable();
            $table->string('image_url');
            $table->string('alt_text', 200)->nullable();
            $table->string('button_text', 60)->nullable();
            $table->string('link_url')->nullable()->comment('Đường dẫn tương đối, vd: shop?cat=thuc-an');
            $table->smallInteger('sort_order')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['position', 'is_active', 'sort_order']);
        });

        // ---------- Liên hệ & bản tin ----------
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name', 100);
            $table->string('email', 150);
            $table->string('phone', 20);
            $table->text('content');
            $table->enum('status', ['new', 'read', 'replied'])->default('new');
            $table->string('admin_note', 500)->nullable();
            $table->dateTime('replied_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150)->unique();
            $table->boolean('is_active')->default(true);
            $table->dateTime('subscribed_at')->useCurrent();
            $table->dateTime('unsubscribed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('spa_service_prices');
        Schema::dropIfExists('spa_services');
    }
};
