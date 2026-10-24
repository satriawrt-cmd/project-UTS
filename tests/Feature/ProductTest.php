<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $staff;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->category = Category::create(['name' => 'Elektronik']);
    }

    public function test_owner_can_create_and_update_product(): void
    {
        $response = $this->actingAs($this->owner)->post(route('products.store'), [
            'sku' => 'SKU-TEST-001',
            'name' => 'Mouse Wireless',
            'category_id' => $this->category->id,
            'purchase_price' => 50000,
            'selling_price' => 75000,
            'minimum_stock' => 5,
            'stock' => 10,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-TEST-001',
            'name' => 'Mouse Wireless',
        ]);

        $product = Product::where('sku', 'SKU-TEST-001')->first();

        // Update
        $updateResponse = $this->actingAs($this->owner)->put(route('products.update', $product), [
            'sku' => 'SKU-TEST-001',
            'name' => 'Mouse Wireless Pro',
            'category_id' => $this->category->id,
            'purchase_price' => 55000,
            'selling_price' => 85000,
            'minimum_stock' => 8,
        ]);

        $updateResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mouse Wireless Pro',
        ]);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        Product::create([
            'sku' => 'DUPLICATE-SKU',
            'name' => 'Produk A',
            'category_id' => $this->category->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 2,
            'stock' => 5,
        ]);

        $response = $this->actingAs($this->owner)->post(route('products.store'), [
            'sku' => 'DUPLICATE-SKU',
            'name' => 'Produk B',
            'category_id' => $this->category->id,
            'purchase_price' => 20000,
            'selling_price' => 25000,
            'minimum_stock' => 3,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_negative_price_or_minimum_stock_is_rejected(): void
    {
        $response = $this->actingAs($this->owner)->post(route('products.store'), [
            'sku' => 'SKU-NEG',
            'name' => 'Produk Negatif',
            'category_id' => $this->category->id,
            'purchase_price' => -5000,
            'selling_price' => -10000,
            'minimum_stock' => -1,
        ]);

        $response->assertSessionHasErrors(['purchase_price', 'selling_price', 'minimum_stock']);
    }

    public function test_staff_cannot_create_or_delete_product(): void
    {
        $product = Product::create([
            'sku' => 'SKU-FOR-DELETE',
            'name' => 'Produk Uji',
            'category_id' => $this->category->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 2,
            'stock' => 5,
        ]);

        // Staff attempts store
        $createResponse = $this->actingAs($this->staff)->post(route('products.store'), [
            'sku' => 'SKU-STAFF-FORBIDDEN',
            'name' => 'Produk Staff',
            'category_id' => $this->category->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 2,
        ]);
        $createResponse->assertStatus(403);

        // Staff attempts delete
        $deleteResponse = $this->actingAs($this->staff)->delete(route('products.destroy', $product));
        $deleteResponse->assertStatus(403);
    }

    public function test_product_used_in_transactions_cannot_be_deleted(): void
    {
        $supplier = Supplier::create([
            'name' => 'PT Mitra Supplier',
            'phone' => '08123',
            'email' => 'sup@test.com',
        ]);

        $product = Product::create([
            'sku' => 'SKU-USED',
            'name' => 'Produk Dipakai',
            'category_id' => $this->category->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 2,
            'stock' => 10,
        ]);

        // Create purchase transaction referencing product
        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'user_id' => $this->owner->id,
            'purchase_number' => 'PO-TEST-001',
            'total' => 100000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $purchase->items()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_cost' => 10000,
            'subtotal' => 100000,
        ]);

        // Attempt delete
        $response = $this->actingAs($this->owner)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
