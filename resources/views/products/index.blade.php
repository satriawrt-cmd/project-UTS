@extends('layouts.app')

@section('title', 'Daftar Produk')
@section('page-title', 'Katalog & Master Produk')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title">🏷️ Daftar Master Produk</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Total {{ $products->total() }} produk terdaftar dalam sistem.</span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('products.low-stock') }}" class="btn btn-secondary btn-sm" style="color: #b91c1c; border-color: #fca5a5;">
                ⚠️ Stok Menipis
            </a>
            @if(auth()->user()->isOwner())
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    + Tambah Produk Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="panel-body" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 16px 24px;">
        <form method="GET" action="{{ route('products.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 12px;">Cari SKU atau Nama Produk</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Ketik SKU atau nama produk..."
                    value="{{ request('search') }}"
                >
            </div>

            <div style="flex: 1; min-width: 180px;">
                <label class="form-label" style="font-size: 12px;">Filter Kategori</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 12px;">Kondisi Stok</label>
                <select name="filter" class="form-control">
                    <option value="">-- Semua Kondisi --</option>
                    <option value="low_stock" {{ request('filter') === 'low_stock' ? 'selected' : '' }}>⚠️ Stok Menipis</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Terapkan Filter</button>
                @if(request()->anyFilled(['search', 'category_id', 'filter']))
                    <a href="{{ route('products.index') }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
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
                    <th style="text-align: center;">Stok</th>
                    <th style="text-align: center;">Min. Stok</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
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
                        <td><strong>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</strong></td>
                        <td style="text-align: center;">
                            <span style="font-weight: 700; font-size: 15px; {{ $product->isLowStock() ? 'color: var(--danger);' : 'color: var(--success);' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td style="text-align: center; color: var(--text-muted);">
                            {{ $product->minimum_stock }}
                        </td>
                        <td>
                            @if($product->isLowStock())
                                <span class="badge badge-danger">Stok Menipis</span>
                            @else
                                <span class="badge badge-success">Aman</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary btn-sm">Lihat</a>
                            @if(auth()->user()->isOwner())
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('products.destroy', $product) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk \'{{ $product->name }}\'? Produk dengan riwayat transaksi tidak dapat dihapus.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 35px;">
                            Tidak ada produk yang sesuai dengan kriteria pencarian.
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
