@extends('layouts.app')

@section('title', 'Daftar Supplier')
@section('page-title', 'Kelola Pemasok (Supplier)')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div>
            <span class="panel-title">🏭 Daftar Mitra Pemasok (Supplier)</span>
            <span style="font-size: 13px; color: var(--text-muted); display: block;">Total {{ $suppliers->total() }} supplier terdaftar.</span>
        </div>

        @if(auth()->user()->isOwner())
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
                + Tambah Supplier Baru
            </a>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="panel-body" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 14px 24px;">
        <form method="GET" action="{{ route('suppliers.index') }}" style="display: flex; gap: 10px; max-width: 500px;">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Cari nama, telepon, atau email..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-primary">Cari</button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nama Supplier</th>
                    <th>No. Telepon / WhatsApp</th>
                    <th>Alamat Email</th>
                    <th>Jumlah PO Pembelian</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                {{ $supplier->name }}
                            </a>
                        </td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $supplier->purchases_count }} Transaksi</span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @if(auth()->user()->isOwner())
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('suppliers.destroy', $supplier) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier \'{{ $supplier->name }}\'? Supplier dengan riwayat pembelian tidak dapat dihapus.');"
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
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 35px;">
                            Tidak ada supplier yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
        <div class="panel-body" style="padding: 14px 24px;">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection
