<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->isCustomer()) {
            return redirect()->route('shop.index');
        }

        $today = now()->startOfDay();
        $month = now()->startOfMonth();

        $productCount = Product::count();
        $purchaseCount = Purchase::where('status', 'completed')->count();
        $saleCount = Sale::where('status', 'completed')->count();

        $salesToday = Sale::where('status', 'completed')
            ->where('sold_at', '>=', $today)
            ->sum('total');

        $salesMonth = Sale::where('status', 'completed')
            ->where('sold_at', '>=', $month)
            ->sum('total');

        $purchasesMonth = Purchase::where('status', 'completed')
            ->where('purchased_at', '>=', $month)
            ->sum('total');

        $lowStock = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->paginate(5, ['*'], 'low_page');

        $recentSales = Sale::with('user')
            ->latest('sold_at')
            ->take(5)
            ->get();

        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->latest('purchased_at')
            ->take(5)
            ->get();

        $pendingCancelCount = Sale::where('status', 'pending_cancel')->count();

        // 1. Data Grafik Tren Penjualan 7 Hari Terakhir
        $trendLabels = [];
        $trendSales = [];
        $trendPurchases = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $dateStr = $d->format('d M');
            $trendLabels[] = $dateStr;

            $daySale = Sale::where('status', 'completed')
                ->whereDate('sold_at', $d->toDateString())
                ->sum('total');
            $trendSales[] = (float) $daySale;

            $dayPurchase = Purchase::where('status', 'completed')
                ->whereDate('purchased_at', $d->toDateString())
                ->sum('total');
            $trendPurchases[] = (float) $dayPurchase;
        }

        // 2. Data Grafik Penjualan per Kategori
        $categorySalesData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.status', 'completed')
            ->select('categories.name', DB::raw('SUM(sale_items.subtotal) as total_amount'))
            ->groupBy('categories.id', 'categories.name')
            ->get();

        if ($categorySalesData->isNotEmpty()) {
            $categoryLabels = $categorySalesData->pluck('name')->toArray();
            $categoryData = $categorySalesData->pluck('total_amount')->map(fn($v) => (float) $v)->toArray();
            $categoryTitle = 'Omset Penjualan per Kategori (Rp)';
        } else {
            $catProducts = Category::withCount('products')->get();
            $categoryLabels = $catProducts->pluck('name')->toArray();
            $categoryData = $catProducts->pluck('products_count')->map(fn($v) => (int) $v)->toArray();
            $categoryTitle = 'Proporsi Jumlah Produk per Kategori';
        }

        return view('dashboard', compact(
            'productCount',
            'purchaseCount',
            'saleCount',
            'salesToday',
            'salesMonth',
            'purchasesMonth',
            'lowStock',
            'recentSales',
            'recentPurchases',
            'pendingCancelCount',
            'trendLabels',
            'trendSales',
            'trendPurchases',
            'categoryLabels',
            'categoryData',
            'categoryTitle'
        ));
    }
}
