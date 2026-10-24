<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sku', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->filter === 'low_stock', function ($query) {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function lowStock(): View
    {
        $products = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->paginate(10)
            ->withQueryString();

        return view('products.low-stock', compact('products'));
    }

    public function show(Product $product): View
    {
        $product->load([
            'category',
            'purchaseItems' => function ($q) {
                $q->with('purchase.supplier', 'purchase.user')->latest('id')->take(10);
            },
            'saleItems' => function ($q) {
                $q->with('sale.user')->latest('id')->take(10);
            },
        ]);

        return view('products.show', compact('product'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $product = new Product();

        return view('products.create', compact('categories', 'product'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                'unique:products,sku',
            ],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!isset($validated['stock'])) {
            $validated['stock'] = 0;
        }

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan.");
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Data produk '{$product->name}' berhasil diperbarui.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->purchaseItems()->exists() || $product->saleItems()->exists()) {
            return redirect()->route('products.index')
                ->with('error', "Produk '{$product->name}' tidak dapat dihapus karena sudah memiliki riwayat transaksi pembelian atau penjualan.");
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk '{$name}' berhasil dihapus.");
    }
}
