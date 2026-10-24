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

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_staff_cannot_access_user_management(): void
    {
        // Staff accessing users index
        $indexResponse = $this->actingAs($this->staff)->get(route('users.index'));
        $indexResponse->assertStatus(403);

        // Staff accessing user create
        $createResponse = $this->actingAs($this->staff)->get(route('users.create'));
        $createResponse->assertStatus(403);

        // Staff attempting to create user
        $storeResponse = $this->actingAs($this->staff)->post(route('users.store'), [
            'name' => 'Hacker Staff',
            'email' => 'hacker@example.com',
            'role' => 'owner',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);
        $storeResponse->assertStatus(403);
    }

    public function test_staff_cannot_cancel_purchase_or_sale(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier Test']);
        $category = Category::create(['name' => 'Kategori Test']);
        $product = Product::create([
            'sku' => 'SKU-AUTH-01',
            'name' => 'Produk Auth',
            'category_id' => $category->id,
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'minimum_stock' => 1,
            'stock' => 10,
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'user_id' => $this->staff->id,
            'purchase_number' => 'PO-AUTH-01',
            'total' => 10000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $sale = Sale::create([
            'user_id' => $this->staff->id,
            'sale_number' => 'SO-AUTH-01',
            'total' => 20000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // Staff tries to cancel purchase
        $purchaseCancelResponse = $this->actingAs($this->staff)->post(route('purchases.cancel', $purchase));
        $purchaseCancelResponse->assertStatus(403);

        // Staff tries to cancel sale
        $saleCancelResponse = $this->actingAs($this->staff)->post(route('sales.cancel', $sale));
        $saleCancelResponse->assertStatus(403);
    }

    public function test_staff_cannot_delete_category_or_supplier(): void
    {
        $category = Category::create(['name' => 'Kategori Staff']);
        $supplier = Supplier::create(['name' => 'Supplier Staff']);

        $catResponse = $this->actingAs($this->staff)->delete(route('categories.destroy', $category));
        $catResponse->assertStatus(403);

        $supResponse = $this->actingAs($this->staff)->delete(route('suppliers.destroy', $supplier));
        $supResponse->assertStatus(403);
    }

    public function test_owner_cannot_demote_or_delete_their_own_account(): void
    {
        // Try demoting self to staff
        $updateResponse = $this->actingAs($this->owner)->put(route('users.update', $this->owner), [
            'name' => $this->owner->name,
            'email' => $this->owner->email,
            'role' => 'staff',
        ]);

        $updateResponse->assertSessionHasErrors('role');
        $this->assertEquals('owner', $this->owner->fresh()->role);

        // Try deleting self
        $deleteResponse = $this->actingAs($this->owner)->delete(route('users.destroy', $this->owner));
        $deleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->owner->id]);
    }
}
