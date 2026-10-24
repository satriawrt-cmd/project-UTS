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

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $staff;
    private User $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->customer = User::factory()->create(['role' => 'user']);

        $category = Category::create(['name' => 'Elektronik']);
        $supplier = Supplier::create([
            'name' => 'PT Mitra Supplier',
            'email' => 'supplier@test.com',
            'phone' => '08123456789',
        ]);

        $this->product = Product::create([
            'sku' => 'SKU-RPT-01',
            'name' => 'Kabel Data Type-C',
            'category_id' => $category->id,
            'purchase_price' => 10000,
            'selling_price' => 25000,
            'minimum_stock' => 5,
            'stock' => 50,
        ]);

        // Purchase transaction (Stock-In)
        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'user_id' => $this->owner->id,
            'purchase_number' => 'PO-RPT-001',
            'total' => 500000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);
        $purchase->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 50,
            'unit_cost' => 10000,
            'subtotal' => 500000,
        ]);

        // Sale transaction (Stock-Out)
        $sale = Sale::create([
            'user_id' => $this->owner->id,
            'sale_number' => 'SO-RPT-001',
            'total' => 250000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);
        $sale->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_price' => 25000,
            'subtotal' => 250000,
        ]);
    }

    public function test_owner_can_access_business_reports(): void
    {
        $response = $this->actingAs($this->owner)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Analisis Bisnis &amp; Keuangan', false);
        $response->assertSee('Rp 250.000'); // Omset
        $response->assertSee('Rp 500.000'); // Pembelian
        $response->assertSee('Rp 100.000'); // HPP (10 x 10.000)
        $response->assertSee('Rp 150.000'); // Laba kotor (250.000 - 100.000)
        $response->assertSee('Kabel Data Type-C');
    }

    public function test_staff_can_access_business_reports(): void
    {
        $response = $this->actingAs($this->staff)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_reports_can_filter_by_period_presets(): void
    {
        $response = $this->actingAs($this->owner)->get(route('reports.index', ['period' => 'today']));
        $response->assertStatus(200);

        $responseYear = $this->actingAs($this->owner)->get(route('reports.index', ['period' => 'this_year']));
        $responseYear->assertStatus(200);
    }
}
