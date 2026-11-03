<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('coupons',function(Blueprint $t){$t->string('scope',20)->default('product');$t->string('shipping_method',20)->nullable();$t->unsignedInteger('new_account_days')->nullable();$t->unsignedInteger('per_user_limit')->nullable();});
  Schema::table('orders',function(Blueprint $t){$t->foreignId('shipping_coupon_id')->nullable()->constrained('coupons')->nullOnDelete();$t->string('shipping_method',20)->default('standard');$t->unsignedInteger('shipping_discount')->default(0);$t->timestamp('received_at')->nullable();});
  Schema::table('order_items',fn(Blueprint $t)=>$t->string('image_url')->nullable());
  Schema::create('product_reviews',function(Blueprint $t){$t->id();$t->foreignId('order_item_id')->unique()->constrained('order_items')->cascadeOnDelete();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();$t->unsignedTinyInteger('rating');$t->text('content');$t->timestamps();});
 }
 public function down(): void {
  Schema::dropIfExists('product_reviews');
  Schema::table('order_items',fn(Blueprint $t)=>$t->dropColumn('image_url'));
  Schema::table('orders',function(Blueprint $t){$t->dropConstrainedForeignId('shipping_coupon_id');$t->dropColumn(['shipping_method','shipping_discount','received_at']);});
  Schema::table('coupons',fn(Blueprint $t)=>$t->dropColumn(['scope','shipping_method','new_account_days','per_user_limit']));
 }
};
