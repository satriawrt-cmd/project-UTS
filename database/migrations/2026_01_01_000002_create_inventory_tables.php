<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('categories',function(Blueprint $t){$t->id();$t->string('name',100)->unique();$t->timestamps();});
  Schema::create('products',function(Blueprint $t){$t->id();$t->foreignId('category_id')->constrained()->restrictOnDelete();$t->string('sku',50)->unique();$t->string('name',150)->index();$t->decimal('purchase_price',14,2);$t->decimal('selling_price',14,2);$t->unsignedInteger('stock')->default(0);$t->unsignedInteger('minimum_stock')->default(0);$t->timestamps();});
  Schema::create('suppliers',function(Blueprint $t){$t->id();$t->string('name',150)->index();$t->string('phone',30)->nullable();$t->string('email',150)->nullable();$t->timestamps();});
  Schema::create('purchases',function(Blueprint $t){$t->id();$t->foreignId('supplier_id')->constrained()->restrictOnDelete();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('purchase_number',40)->unique();$t->decimal('total',16,2)->default(0);$t->string('status',20)->default('completed')->index();$t->dateTime('purchased_at')->index();$t->timestamps();});
  Schema::create('purchase_items',function(Blueprint $t){$t->id();$t->foreignId('purchase_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->unsignedInteger('quantity');$t->decimal('unit_cost',14,2);$t->decimal('subtotal',16,2);$t->index(['purchase_id','product_id']);});
  Schema::create('sales',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('sale_number',40)->unique();$t->decimal('total',16,2)->default(0);$t->string('status',20)->default('completed')->index();$t->dateTime('sold_at')->index();$t->timestamps();});
  Schema::create('sale_items',function(Blueprint $t){$t->id();$t->foreignId('sale_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->unsignedInteger('quantity');$t->decimal('unit_price',14,2);$t->decimal('subtotal',16,2);$t->index(['sale_id','product_id']);});
 }
 public function down(): void {foreach(['sale_items','sales','purchase_items','purchases','suppliers','products','categories'] as $table)Schema::dropIfExists($table);}
};
