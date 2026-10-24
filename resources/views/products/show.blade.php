@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)
@section('page-title', 'Detail Produk & Riwayat')

@section('content')
<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Daftar Produk</a>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">+ Pembelian (Restock)</a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">+ Penjualan</a>
        @if(auth()->user()->isOwner())
            <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">Edit Produk</a>
        @endif
    </div>
</div>

<!-- Header Card -->
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title" style="font-size: 20px;">{{ $product->name }}</span>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                SKU: <code>{{ $product->sku }}</code> &bull; Kategori: <strong>{{ $product->category->name ?? '-' }}</strong>
            </div>
        </div>

        <div>
            @if($product->isLowStock())
                <span class="badge badge-danger" style="font-size: 14px; padding: 6px 14px;">⚠️ Stok Menipis</span>
            @else
                <span class="badge badge-success" style="font-size: 14px; padding: 6px 14px;">✅ Stok Aman</span>
            @endif
        </div>
    </div>

    <div class="panel-body">
        <div class="grid-4">
            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Stok Saat Ini</div>
                <div style="font-size: 28px; font-weight: 800; color: {{ $product->isLowStock() ? 'var(--danger)' : 'var(--text-main)' }}; margin-top: 4px;">
                    {{ $product->stock }} <span style="font-size: 14px; font-weight: normal; color: var(--text-muted);">Unit</span>
                </div>
            </div>

            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Batas Min. Stok</div>
                <div style="font-size: 28px; font-weight: 800; color: var(--text-main); margin-top: 4px;">
                    {{ $product->minimum_stock }} <span style="font-size: 14px; font-weight: normal; color: var(--text-muted);">Unit</span>
                </div>
            </div>

            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Harga Beli Standar</div>
                <div style="font-size: 22px; font-weight: 800; color: #475569; margin-top: 8px;">
                    Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                </div>
            </div>

            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Harga Jual Satuan</div>
                <div style="font-size: 22px; font-weight: 800; color: var(--primary); margin-top: 8px;">
                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Transactions Tabs / Grid -->
<div class="grid-2">
    <!-- Riwayat Pembelian (Stock-In) -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">📥 Riwayat Pembelian Produk Ini</span>
            <span class="badge badge-info">{{ $product->purchaseItems->count() }} Kali Masuk</span>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Kuantitas</th>
                        <th>Biaya Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->purchaseItems as $pItem)
                        <tr>
                            <td>
                                @if($pItem->purchase)
                                    <a href="{{ route('purchases.show', $pItem->purchase) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        {{ $pItem->purchase->purchase_number }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $pItem->purchase->supplier->name ?? '-' }}</td>
                            <td><strong style="color: var(--success);">+{{ $pItem->quantity }}</strong></td>
                            <td>Rp {{ number_format($pItem->unit_cost, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pItem->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 25px;">
                                Belum ada catatan pembelian untuk produk ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Penjualan (Stock-Out) -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">📤 Riwayat Penjualan Produk Ini</span>
            <span class="badge badge-success">{{ $product->saleItems->count() }} Kali Keluar</span>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. SO</th>
                        <th>Kasir</th>
                        <th>Kuantitas</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->saleItems as $sItem)
                        <tr>
                            <td>
                                @if($sItem->sale)
                                    <a href="{{ route('sales.show', $sItem->sale) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        {{ $sItem->sale->sale_number }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $sItem->sale->user->name ?? '-' }}</td>
                            <td><strong style="color: var(--danger);">-{{ $sItem->quantity }}</strong></td>
                            <td>Rp {{ number_format($sItem->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($sItem->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 25px;">
                                Belum ada catatan penjualan untuk produk ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
