<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where('purchase_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->supplier_id, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($request->from, function ($query, $from) {
                $query->whereDate('purchased_at', '>=', $from);
            })
            ->when($request->to, function ($query, $to) {
                $query->whereDate('purchased_at', '<=', $to);
            })
            ->latest('purchased_at')
            ->paginate(10)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchased_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'items.required' => 'Minimal satu produk harus ditambahkan dalam pembelian.',
            'items.min' => 'Minimal satu produk harus ditambahkan dalam pembelian.',
            'items.*.product_id.distinct' => 'Produk pada baris transaksi tidak boleh duplikat.',
            'items.*.quantity.min' => 'Jumlah pembelian minimal 1.',
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $purchaseNumber = 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_number' => $purchaseNumber,
                'total' => 0,
                'status' => 'completed',
                'purchased_at' => $validated['purchased_at'] ?? now(),
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $qty = (int) $item['quantity'];

                if ($qty < 1) {
                    throw ValidationException::withMessages([
                        'items' => "Kuantitas untuk produk {$product->name} tidak valid.",
                    ]);
                }

                $cost = isset($item['unit_cost']) && $item['unit_cost'] !== ''
                    ? (float) $item['unit_cost']
                    : (float) $product->purchase_price;

                $subtotal = $cost * $qty;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                ]);

                $product->increment('stock', $qty);
                $total += $subtotal;
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Transaksi pembelian {$purchase->purchase_number} berhasil disimpan dan stok otomatis bertambah.");
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product.category']);

        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        if ($purchase->isCancelled()) {
            return back()->with('error', 'Transaksi pembelian ini sudah dalam status dibatalkan.');
        }

        DB::transaction(function () use ($purchase) {
            $p = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            foreach ($p->items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();

                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'transaction' => "Pembelian tidak dapat dibatalkan karena stok '{$product->name}' sudah terpakai/terjual. Stok saat ini: {$product->stock}, kuantitas yang harus ditarik: {$item->quantity}.",
                    ]);
                }

                $product->decrement('stock', $item->quantity);
            }

            $p->update(['status' => 'cancelled']);
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Transaksi pembelian {$purchase->purchase_number} berhasil dibatalkan dan stok telah disesuaikan kembali.");
    }
}
