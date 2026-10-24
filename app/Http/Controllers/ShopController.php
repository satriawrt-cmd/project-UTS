<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('shop.index', compact('products', 'categories'));
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $qty = max(1, (int) $request->input('quantity', 1));

        if ($product->stock <= 0) {
            return back()->with('error', "Maaf, stok produk '{$product->name}' saat ini habis.");
        }

        $cart = session()->get('cart', []);
        $currentInCart = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        $newTotalQty = $currentInCart + $qty;

        if ($newTotalQty > $product->stock) {
            return back()->with('error', "Kuantitas pesanan melebihi stok yang tersedia! Stok saat ini: {$product->stock} unit (sudah di keranjang: {$currentInCart}).");
        }

        $cart[$product->id] = [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'category' => $product->category->name ?? '-',
            'price' => (float) $product->selling_price,
            'quantity' => $newTotalQty,
            'stock' => $product->stock,
        ];

        session()->put('cart', $cart);

        return redirect()->route('shop.cart')
            ->with('success', "'{$product->name}' berhasil ditambahkan ke keranjang belanja!");
    }

    public function cart(): View
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['price'] * $item['quantity']);
        }

        return view('shop.cart', compact('cart', 'total'));
    }

    public function updateCart(Request $request): RedirectResponse
    {
        $quantities = $request->input('quantities', []);
        $cart = session()->get('cart', []);

        foreach ($quantities as $productId => $qty) {
            $qty = (int) $qty;
            if (isset($cart[$productId])) {
                $product = Product::find($productId);
                if (!$product) {
                    unset($cart[$productId]);
                    continue;
                }

                if ($qty <= 0) {
                    unset($cart[$productId]);
                } elseif ($qty > $product->stock) {
                    $cart[$productId]['quantity'] = $product->stock;
                    session()->flash('error', "Kuantitas untuk '{$product->name}' disesuaikan ke stok maksimum ({$product->stock} unit).");
                } else {
                    $cart[$productId]['quantity'] = $qty;
                }
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('shop.cart')
            ->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    public function removeFromCart($productId): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart')
            ->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')
                ->with('error', 'Keranjang belanja Anda masih kosong. Silakan pilih produk terlebih dahulu.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['price'] * $item['quantity']);
        }

        return view('shop.checkout', compact('cart', 'total'));
    }

    public function processCheckout(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')
                ->with('error', 'Keranjang belanja kosong.');
        }

        $sale = DB::transaction(function () use ($cart) {
            $saleNumber = 'SO-ONLINE-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'sale_number' => $saleNumber,
                'total' => 0,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $total = 0;

            foreach ($cart as $item) {
                $product = Product::whereKey($item['id'])->lockForUpdate()->firstOrFail();
                $qty = (int) $item['quantity'];

                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'checkout' => "Stok produk '{$product->name}' tidak mencukupi (tersedia: {$product->stock}, diminta: {$qty}). Silakan sesuaikan keranjang belanja Anda.",
                    ]);
                }

                $price = (float) $product->selling_price;
                $subtotal = $price * $qty;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => $total]);

            return $sale;
        });

        // Clear cart
        session()->forget('cart');

        return redirect()->route('my-orders.show', $sale)
            ->with('success', "Pesanan Anda dengan nomor {$sale->sale_number} berhasil diproses! Terima kasih telah berbelanja.");
    }

    public function myOrders(): View
    {
        $orders = Sale::with('items.product')
            ->where('user_id', auth()->id())
            ->latest('sold_at')
            ->paginate(10);

        return view('shop.orders', compact('orders'));
    }

    public function showOrder(Sale $sale): View
    {
        if ($sale->user_id !== auth()->id() && !auth()->user()->isOwner() && !auth()->user()->isStaff()) {
            abort(403, 'Anda tidak memiliki hak untuk melihat pesanan ini.');
        }

        $sale->load(['user', 'items.product.category']);

        return view('shop.order-detail', compact('sale'));
    }

    public function requestCancellation(Request $request, Sale $sale): RedirectResponse
    {
        if ($sale->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak membatalkan pesanan milik akun lain.');
        }

        if (!$sale->isCompleted()) {
            return back()->with('error', 'Pesanan ini tidak dapat diajukan pembatalan karena statusnya bukan Selesai (Completed).');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'reason.required' => 'Mohon sertakan alasan pengajuan pembatalan pesanan.',
        ]);

        $sale->update([
            'status' => 'pending_cancel',
            'cancellation_reason' => $validated['reason'],
        ]);

        return redirect()->route('my-orders.show', $sale)
            ->with('success', 'Permintaan pembatalan pesanan telah dikirimkan. Menunggu verifikasi dan konfirmasi dari Owner.');
    }
}

