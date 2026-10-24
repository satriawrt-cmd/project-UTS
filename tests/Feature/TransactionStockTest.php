<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionStockTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $staff;
    private Supplier $supplier;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->supplier = Supplier::create([
            'name' => 'PT Mitra Utama',
            'phone' => '0812345678',
            'email' => 'mitra@utama.com',
        ]);
        $this->category = Category::create(['name' => 'Bahan Pokok']);
    }

    public function test_purchase_increases_product_stock(): void
    {
        $product = Product::create([
            'sku' => 'SKU-PO-01',
            'name' => 'Beras 5kg',
            'category_id' => $this->category->id,
            'purchase_price' => 60000,
            'selling_price' => 75000,
            'minimum_stock' => 5,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->staff)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 15,
                    'unit_cost' => 60000,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(25, $product->fresh()->stock);

        $purchase = Purchase::where('supplier_id', $this->supplier->id)->latest()->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(900000, $purchase->total);
        $this->assertEquals('completed', $purchase->status);
    }

    public function test_sale_decreases_product_stock(): void
    {
        $product = Product::create([
            'sku' => 'SKU-SO-01',
            'name' => 'Gula Pasir 1kg',
            'category_id' => $this->category->id,
            'purchase_price' => 12000,
            'selling_price' => 16000,
            'minimum_stock' => 5,
            'stock' => 20,
        ]);

        $response = $this->actingAs($this->staff)->post(route('sales.store'), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 7,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(13, $product->fresh()->stock);

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $this->assertEquals(112000, $sale->total); // 7 * 16000
        $this->assertEquals('completed', $sale->status);
    }

    public function test_sale_with_insufficient_stock_is_rejected_and_stock_remains_unchanged(): void
    {
        $product = Product::create([
            'sku' => 'SKU-SO-LOW',
            'name' => 'Minyak Goreng 2L',
            'category_id' => $this->category->id,
            'purchase_price' => 30000,
            'selling_price' => 38000,
            'minimum_stock' => 2,
            'stock' => 4, // only 4 available
        ]);

        // Attempt to buy 10
        $response = $this->actingAs($this->staff)->post(route('sales.store'), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertEquals(4, $product->fresh()->stock); // Stock intact
        $this->assertEquals(0, Sale::count()); // No sale created
    }

    public function test_atomic_rollback_when_one_item_in_sale_fails(): void
    {
        $productA = Product::create([
            'sku' => 'SKU-ATOMIC-A',
            'name' => 'Produk A',
            'category_id' => $this->category->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 2,
            'stock' => 10, // Stock is plenty
        ]);

        $productB = Product::create([
            'sku' => 'SKU-ATOMIC-B',
            'name' => 'Produk B',
            'category_id' => $this->category->id,
            'purchase_price' => 20000,
            'selling_price' => 25000,
            'minimum_stock' => 2,
            'stock' => 2, // Stock is only 2
        ]);

        // Request 5 of A (valid) and 5 of B (invalid: exceeds 2)
        $response = $this->actingAs($this->staff)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $productA->id, 'quantity' => 5],
                ['product_id' => $productB->id, 'quantity' => 5],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Rollback check: Neither product A nor B had their stock modified!
        $this->assertEquals(10, $productA->fresh()->stock);
        $this->assertEquals(2, $productB->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    public function test_owner_can_cancel_sale_and_stock_is_restored(): void
    {
        $product = Product::create([
            'sku' => 'SKU-CANCEL-SO',
            'name' => 'Kopi Sachet',
            'category_id' => $this->category->id,
            'purchase_price' => 1500,
            'selling_price' => 2500,
            'minimum_stock' => 5,
            'stock' => 20,
        ]);

        // Perform sale of 8 items
        $this->actingAs($this->staff)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 8],
            ],
        ]);

        $this->assertEquals(12, $product->fresh()->stock);
        $sale = Sale::latest()->first();

        // Owner cancels sale
        $cancelResponse = $this->actingAs($this->owner)->post(route('sales.cancel', $sale));
        $cancelResponse->assertSessionHas('success');

        // Stock restored back to 20
        $this->assertEquals(20, $product->fresh()->stock);
        $this->assertEquals('cancelled', $sale->fresh()->status);
    }

    public function test_owner_can_cancel_purchase_and_stock_is_reduced(): void
    {
        $product = Product::create([
            'sku' => 'SKU-CANCEL-PO',
            'name' => 'Teh Celup',
            'category_id' => $this->category->id,
            'purchase_price' => 5000,
            'selling_price' => 8000,
            'minimum_stock' => 2,
            'stock' => 5,
        ]);

        // Purchase 10 items -> stock becomes 15
        $this->actingAs($this->staff)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 5000],
            ],
        ]);

        $this->assertEquals(15, $product->fresh()->stock);
        $purchase = Purchase::latest()->first();

        // Owner cancels purchase -> stock reduced by 10 back to 5
        $cancelResponse = $this->actingAs($this->owner)->post(route('purchases.cancel', $purchase));
        $cancelResponse->assertSessionHas('success');

        $this->assertEquals(5, $product->fresh()->stock);
        $this->assertEquals('cancelled', $purchase->fresh()->status);
    }

    public function test_purchase_cancel_rejected_if_stock_already_sold(): void
    {
        $product = Product::create([
            'sku' => 'SKU-PO-SOLD',
            'name' => 'Biskuit',
            'category_id' => $this->category->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'minimum_stock' => 2,
            'stock' => 0,
        ]);

        // Purchase 10 items -> stock becomes 10
        $this->actingAs($this->staff)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 10000],
            ],
        ]);

        $purchase = Purchase::latest()->first();

        // Customer buys 6 items -> stock becomes 4
        $this->actingAs($this->staff)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 6],
            ],
        ]);

        $this->assertEquals(4, $product->fresh()->stock);

        // Owner tries to cancel the 10-item purchase -> rejected because remaining stock is only 4!
        $cancelResponse = $this->actingAs($this->owner)->post(route('purchases.cancel', $purchase));
        $cancelResponse->assertSessionHasErrors('transaction');

        // Purchase remains completed and stock remains 4
        $this->assertEquals('completed', $purchase->fresh()->status);
        $this->assertEquals(4, $product->fresh()->stock);
    }
}
