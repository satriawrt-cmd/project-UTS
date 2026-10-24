<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->input('period', 'this_month');
        $from = $request->input('from');
        $to = $request->input('to');

        // Determine date range based on period
        if ($period === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'this_week') {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
        } elseif ($period === 'this_year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } elseif ($period === 'custom' && $from && $to) {
            $startDate = \Carbon\Carbon::parse($from)->startOfDay();
            $endDate = \Carbon\Carbon::parse($to)->endOfDay();
        } else {
            // Default: this_month
            $period = 'this_month';
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        // Sales in period
        $salesQuery = Sale::with(['user', 'items.product'])
            ->where('status', 'completed')
            ->whereBetween('sold_at', [$startDate, $endDate]);

        $totalSales = (float) $salesQuery->sum('total');
        $salesCount = $salesQuery->count();

        // Purchases in period
        $purchasesQuery = Purchase::with(['supplier', 'user', 'items.product'])
            ->where('status', 'completed')
            ->whereBetween('purchased_at', [$startDate, $endDate]);

        $totalPurchases = (float) $purchasesQuery->sum('total');
        $purchasesCount = $purchasesQuery->count();

        // Cost of Goods Sold (HPP Modal Barang yang Terjual)
        $cogs = (float) DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sold_at', [$startDate, $endDate])
            ->sum(DB::raw('products.purchase_price * sale_items.quantity'));

        // Estimasi Laba Kotor (Gross Profit)
        $grossProfit = $totalSales - $cogs;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;

        // Top 5 Produk Terlaris di Periode Ini
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sold_at', [$startDate, $endDate])
            ->select(
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Detailed Sales List for Table
        $salesList = (clone $salesQuery)->latest('sold_at')->paginate(10, ['*'], 'sales_page')->withQueryString();

        // Detailed Purchases List for Table
        $purchasesList = (clone $purchasesQuery)->latest('purchased_at')->paginate(10, ['*'], 'purchases_page')->withQueryString();

        return view('reports.index', compact(
            'period',
            'from',
            'to',
            'startDate',
            'endDate',
            'totalSales',
            'salesCount',
            'totalPurchases',
            'purchasesCount',
            'cogs',
            'grossProfit',
            'profitMargin',
            'topProducts',
            'salesList',
            'purchasesList'
        ));
    }
}
