<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Trang đăng nhập/đăng ký chỉ có một ô "Email/Số điện thoại/Tên đăng nhập":
        // username bắt buộc, email và phone tùy chọn nhưng có thì không được trùng.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password');
            $table->string('full_name', 100)->nullable();
            $table->string('avatar_url')->nullable();
            $table->enum('role', ['customer', 'staff', 'admin'])->default('customer');
            $table->enum('status', ['active', 'locked'])->default('active');
            $table->dateTime('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
