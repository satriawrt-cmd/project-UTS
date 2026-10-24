<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with(['user', 'items.product'])
            ->when($request->search, function ($query, $search) {
                $query->where('sale_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->from, function ($query, $from) {
                $query->whereDate('sold_at', '>=', $from);
            })
            ->when($request->to, function ($query, $to) {
                $query->whereDate('sold_at', '<=', $to);
            })
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        $pendingCancelCount = Sale::where('status', 'pending_cancel')->count();

        return view('sales.index', compact('sales', 'pendingCancelCount'));
    }

    public function create(): View
    {
        $products = Product::with('category')->orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sold_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Minimal satu produk harus dipilih untuk transaksi penjualan.',
            'items.min' => 'Minimal satu produk harus dipilih untuk transaksi penjualan.',
            'items.*.product_id.distinct' => 'Produk pada baris penjualan tidak boleh duplikat.',
            'items.*.quantity.min' => 'Jumlah kuantitas penjualan minimal 1.',
        ]);

        $sale = DB::transaction(function () use ($validated) {
            $saleNumber = 'SO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'sale_number' => $saleNumber,
                'total' => 0,
                'status' => 'completed',
                'sold_at' => $validated['sold_at'] ?? now(),
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $qty = (int) $item['quantity'];

                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk '{$product->name}' (SKU: {$product->sku}) tidak mencukupi. Stok tersedia: {$product->stock}, permintaan: {$qty}.",
                    ]);
                }

                // Strict server-side price enforcement
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

        return redirect()->route('sales.show', $sale)
            ->with('success', "Transaksi penjualan {$sale->sale_number} berhasil disimpan dan stok otomatis berkurang.");
    }

    public function show(Sale $sale): View
    {
        $sale->load(['user', 'items.product.category']);

        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        if ($sale->isCancelled()) {
            return back()->with('error', 'Transaksi penjualan ini sudah dalam status dibatalkan.');
        }

        DB::transaction(function () use ($sale) {
            $s = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            foreach ($s->items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                $product->increment('stock', $item->quantity);
            }

            $s->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Transaksi penjualan {$sale->sale_number} berhasil dibatalkan dan stok dikembalikan ke sistem.");
    }

    public function approveCancellation(Sale $sale): RedirectResponse
    {
        if (!$sale->isPendingCancel()) {
            return back()->with('error', 'Transaksi ini tidak sedang menunggu persetujuan pembatalan.');
        }

        DB::transaction(function () use ($sale) {
            $s = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            foreach ($s->items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                $product->increment('stock', $item->quantity);
            }

            $s->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Permintaan pembatalan pesanan {$sale->sale_number} telah disetujui oleh Owner. Stok barang telah dikembalikan ke gudang.");
    }

    public function rejectCancellation(Request $request, Sale $sale): RedirectResponse
    {
        if (!$sale->isPendingCancel()) {
            return back()->with('error', 'Transaksi ini tidak sedang menunggu persetujuan pembatalan.');
        }

        $note = $request->input('reject_note', 'Ditolak oleh admin/owner.');
        $reason = $sale->cancellation_reason ? "{$sale->cancellation_reason} [Ditolak: {$note}]" : "[Ditolak: {$note}]";

        $sale->update([
            'status' => 'completed',
            'cancellation_reason' => $reason,
        ]);

        return redirect()->route('sales.show', $sale)
            ->with('success', "Permintaan pembatalan pesanan {$sale->sale_number} telah ditolak. Status pesanan kembali menjadi Selesai (Completed).");
    }
}
