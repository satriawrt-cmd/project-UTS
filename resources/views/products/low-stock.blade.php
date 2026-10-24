@extends('layouts.app')

@section('title', 'Daftar Stok Menipis')
@section('page-title', 'Peringatan Produk Stok Menipis')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title" style="color: var(--danger);">⚠️ Daftar Produk Mencapai Batas Minimum</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">
                Produk-produk ini memiliki jumlah stok fisik kurang dari atau sama dengan batas minimum stok.
            </span>
        </div>

        <div style="display: flex; gap: 8px;">
            <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">
                + Buat Pembelian Stok Baru
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                Lihat Semua Produk
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th style="text-align: center;">Stok Saat Ini</th>
                    <th style="text-align: center;">Batas Minimum</th>
                    <th>Defisit</th>
                    <th style="text-align: right;">Aksi Restock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $products->firstItem() + $loop->index }}</td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger" style="font-size: 14px;">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td style="text-align: center; color: var(--text-muted); font-weight: 600;">
                            {{ $product->minimum_stock }}
                        </td>
                        <td>
                            @php
                                $deficit = $product->minimum_stock - $product->stock;
                            @endphp
                            @if($deficit > 0)
                                <span style="color: var(--danger); font-size: 12px; font-weight: 600;">Kurang {{ $deficit }} Unit</span>
                            @else
                                <span style="color: var(--warning); font-size: 12px; font-weight: 600;">Tepat di batas</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">
                                ➕ Restock (PO)
                            </a>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            🎉 Kabar baik! Saat ini tidak ada produk yang berada di bawah batas minimum stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="panel-body" style="padding: 14px 24px;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
