<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerShopTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Product $product;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create(['name' => 'Kebutuhan Harian']);
        $this->product = Product::create([
            'sku' => 'SKU-SHOP-01',
            'name' => 'Sabun Mandi Wangi',
            'category_id' => $this->category->id,
            'purchase_price' => 3000,
            'selling_price' => 5000,
            'minimum_stock' => 5,
            'stock' => 20,
        ]);

        $this->customer = User::factory()->create([
            'role' => 'user',
            'name' => 'Pelanggan Setia',
            'email' => 'customer@test.com',
        ]);
    }

    public function test_customer_can_register_new_account(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'User Baru',
            'email' => 'userbaru@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('shop.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'userbaru@test.com',
            'role' => 'user',
        ]);
    }

    public function test_customer_login_redirects_to_shop(): void
    {
        $response = $this->post(route('login.process'), [
            'email' => $this->customer->email,
            'password' => 'password', // standard factory password
        ]);

        $response->assertRedirect(route('shop.index'));
    }

    public function test_customer_can_browse_shop_catalog(): void
    {
        $response = $this->actingAs($this->customer)->get(route('shop.index'));
        $response->assertStatus(200);
        $response->assertSee('Sabun Mandi Wangi');
        $response->assertSee('Rp 5.000');
    }

    public function test_customer_can_add_product_to_cart(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('shop.cart.add', $this->product), [
                'quantity' => 3,
            ]);

        $response->assertRedirect(route('shop.cart'));
        $this->assertEquals(3, session('cart')[$this->product->id]['quantity']);
    }

    public function test_customer_cannot_add_more_than_available_stock(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('shop.cart.add', $this->product), [
                'quantity' => 25, // only 20 in stock
            ]);

        $response->assertSessionHas('error');
    }

    public function test_customer_can_checkout_and_stock_is_deducted(): void
    {
        // Place item in session cart
        $cart = [
            $this->product->id => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $this->product->name,
                'category' => $this->category->name,
                'price' => (float) $this->product->selling_price,
                'quantity' => 4,
                'stock' => $this->product->stock,
            ],
        ];

        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => $cart])
            ->post(route('shop.checkout.process'));

        $response->assertSessionHas('success');

        // Verify stock is decremented
        $this->assertEquals(16, $this->product->fresh()->stock);

        // Verify sale record created
        $sale = Sale::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($sale);
        $this->assertEquals(20000, $sale->total); // 4 * 5000
        $this->assertEquals(1, $sale->items()->count());

        // Verify cart is cleared
        $this->assertEmpty(session('cart'));
    }

    public function test_customer_can_view_order_history_and_detail(): void
    {
        $sale = Sale::create([
            'user_id' => $this->customer->id,
            'sale_number' => 'SO-ONLINE-001',
            'total' => 15000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $sale->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 5000,
            'subtotal' => 15000,
        ]);

        // Order list
        $listResponse = $this->actingAs($this->customer)->get(route('my-orders.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('SO-ONLINE-001');

        // Order detail
        $detailResponse = $this->actingAs($this->customer)->get(route('my-orders.show', $sale));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('SO-ONLINE-001');
        $detailResponse->assertSee('Sabun Mandi Wangi');
    }

    public function test_customer_cannot_access_backoffice_management(): void
    {
        // Customer tries to access purchases
        $purchasesResponse = $this->actingAs($this->customer)->get(route('purchases.index'));
        $purchasesResponse->assertStatus(403);

        // Customer tries to access users
        $usersResponse = $this->actingAs($this->customer)->get(route('users.index'));
        $usersResponse->assertStatus(403);

        // Customer tries to access reports
        $reportsResponse = $this->actingAs($this->customer)->get(route('reports.index'));
        $reportsResponse->assertStatus(403);
    }

    public function test_customer_can_request_cancellation_with_reason(): void
    {
        $sale = Sale::create([
            'user_id' => $this->customer->id,
            'sale_number' => 'SO-ONLINE-CANCEL-01',
            'total' => 10000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('my-orders.cancel', $sale), [
                'reason' => 'Salah pesan varian barang',
            ]);

        $response->assertRedirect(route('my-orders.show', $sale));
        $sale->refresh();

        $this->assertEquals('pending_cancel', $sale->status);
        $this->assertEquals('Salah pesan varian barang', $sale->cancellation_reason);
    }

    public function test_customer_cannot_request_cancellation_for_other_user_order(): void
    {
        $otherCustomer = User::factory()->create(['role' => 'user']);
        $sale = Sale::create([
            'user_id' => $otherCustomer->id,
            'sale_number' => 'SO-ONLINE-OTHER-01',
            'total' => 10000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('my-orders.cancel', $sale), [
                'reason' => 'Coba batalkan punya orang',
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_approve_cancellation_and_restore_stock(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $initialStock = $this->product->stock; // 20

        // Create sale with 4 units
        $sale = Sale::create([
            'user_id' => $this->customer->id,
            'sale_number' => 'SO-ONLINE-APPROVE-01',
            'total' => 20000,
            'status' => 'pending_cancel',
            'cancellation_reason' => 'Ingin ganti pesanan',
            'sold_at' => now(),
        ]);

        $sale->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 4,
            'unit_price' => 5000,
            'subtotal' => 20000,
        ]);

        // Owner approves cancellation
        $response = $this->actingAs($owner)
            ->post(route('sales.approve-cancel', $sale));

        $response->assertRedirect(route('sales.show', $sale));
        $sale->refresh();
        $this->product->refresh();

        $this->assertEquals('cancelled', $sale->status);
        $this->assertEquals($initialStock + 4, $this->product->stock); // 20 + 4 = 24
    }

    public function test_owner_can_reject_cancellation(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $sale = Sale::create([
            'user_id' => $this->customer->id,
            'sale_number' => 'SO-ONLINE-REJECT-01',
            'total' => 15000,
            'status' => 'pending_cancel',
            'cancellation_reason' => 'Batal mendadak',
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($owner)
            ->post(route('sales.reject-cancel', $sale), [
                'reject_note' => 'Barang sudah dalam proses pengiriman kurir',
            ]);

        $response->assertRedirect(route('sales.show', $sale));
        $sale->refresh();

        $this->assertEquals('completed', $sale->status);
        $this->assertStringContainsString('Barang sudah dalam proses pengiriman kurir', $sale->cancellation_reason);
    }

    public function test_staff_cannot_approve_or_reject_cancellation(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $sale = Sale::create([
            'user_id' => $this->customer->id,
            'sale_number' => 'SO-ONLINE-STAFF-01',
            'total' => 15000,
            'status' => 'pending_cancel',
            'sold_at' => now(),
        ]);

        // Staff tries approve
        $approveResponse = $this->actingAs($staff)->post(route('sales.approve-cancel', $sale));
        $approveResponse->assertStatus(403);

        // Staff tries reject
        $rejectResponse = $this->actingAs($staff)->post(route('sales.reject-cancel', $sale));
        $rejectResponse->assertStatus(403);
    }
}

