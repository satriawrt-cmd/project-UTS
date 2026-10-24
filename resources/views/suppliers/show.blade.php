@extends('layouts.app')

@section('title', 'Detail Supplier: ' . $supplier->name)
@section('page-title', 'Profil Pemasok & Riwayat PO')

@section('content')
<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Daftar Supplier</a>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('purchases.create') }}" class="btn btn-success btn-sm">+ Buat PO Pembelian ke Supplier Ini</a>
        @if(auth()->user()->isOwner())
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary btn-sm">Edit Profil</a>
        @endif
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title" style="font-size: 20px;">🏭 {{ $supplier->name }}</span>
        <span class="badge badge-info">{{ $supplier->purchases->count() }} Total Riwayat PO</span>
    </div>

    <div class="panel-body">
        <div class="grid-3">
            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">No. Telepon / WhatsApp</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-top: 4px;">
                    {{ $supplier->phone ?? 'Belum diisi' }}
                </div>
            </div>

            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Alamat Email</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-top: 4px;">
                    {{ $supplier->email ?? 'Belum diisi' }}
                </div>
            </div>

            <div style="padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Terdaftar Pada</div>
                <div style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-top: 4px;">
                    {{ $supplier->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat PO -->
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">📥 10 Transaksi Pembelian Terbaru dari Supplier Ini</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. PO</th>
                    <th>Tanggal</th>
                    <th>Dibuat Oleh</th>
                    <th>Total Transaksi</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->purchases as $purchase)
                    <tr>
                        <td><code>{{ $purchase->purchase_number }}</code></td>
                        <td>{{ $purchase->purchased_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $purchase->user->name ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($purchase->total, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($purchase->isCompleted())
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada riwayat transaksi pembelian dari supplier ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
