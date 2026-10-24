@extends('layouts.app')

@section('title', 'Catat Pembelian Baru (Stock-In)')
@section('page-title', 'Form Transaksi Pembelian Barang (Stock-In)')

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <div>
                <span class="panel-title">📥 Catat Transaksi Pembelian Baru</span>
                <span style="font-size: 13px; color: var(--text-muted); display: block;">
                    Stok produk akan bertambah secara otomatis setelah transaksi disimpan.
                </span>
            </div>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">&larr; Batal & Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                @csrf

                <!-- Header Info -->
                <div class="grid-2" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="supplier_id">Pilih Mitra Pemasok (Supplier) <span style="color: red;">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} {{ $supplier->phone ? '(' . $supplier->phone . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="purchased_at">Tanggal Transaksi Pembelian</label>
                        <input
                            type="datetime-local"
                            name="purchased_at"
                            id="purchased_at"
                            class="form-control"
                            value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}"
                        >
                    </div>
                </div>

                <!-- Multi-Item Table -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text-main);">
                        📦 Daftar Item Produk yang Dibeli
                    </h3>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnAddRow" style="color: var(--primary); border-color: var(--primary);">
                        ➕ Tambah Baris Produk
                    </button>
                </div>

                <div class="table-responsive" style="margin-bottom: 20px;">
                    <table class="data-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="min-width: 280px;">Produk <span style="color: red;">*</span></th>
                                <th style="width: 170px;">Harga Beli Satuan (Rp)</th>
                                <th style="width: 130px;">Jumlah Qty <span style="color: red;">*</span></th>
                                <th style="width: 170px; text-align: right;">Subtotal (Rp)</th>
                                <th style="width: 60px; text-align: center;">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Rows injected by JS or default first row -->
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8fafc; font-weight: bold;">
                                <td colspan="3" style="text-align: right; padding: 14px 16px; font-size: 15px;">
                                    TOTAL PEMBELIAN:
                                </td>
                                <td style="text-align: right; padding: 14px 16px; font-size: 17px; color: var(--primary);" id="grandTotalDisplay">
                                    Rp 0
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success" style="padding: 11px 24px; font-size: 15px;">
                        💾 Konfirmasi & Simpan Pembelian (Stock-In)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const productsData = @json($products);
    let rowIndex = 0;

    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function addRow(productId = '', quantity = 1, unitCost = '') {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let productOptions = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const selected = (p.id == productId) ? 'selected' : '';
            productOptions += `<option value="${p.id}" data-cost="${p.purchase_price}" data-stock="${p.stock}" ${selected}>
                ${p.name} (SKU: ${p.sku} | Stok: ${p.stock})
            </option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-control product-select" required onchange="handleProductChange(${rowIndex})">
                    ${productOptions}
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_cost]" class="form-control unit-cost-input" value="${unitCost}" placeholder="0" required oninput="calculateSubtotal(${rowIndex})">
            </td>
            <td>
                <input type="number" min="1" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="${quantity}" required oninput="calculateSubtotal(${rowIndex})">
            </td>
            <td style="text-align: right; font-weight: 600;" class="subtotal-cell" id="subtotal-${rowIndex}">
                Rp 0
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowIndex})" title="Hapus baris">&times;</button>
            </td>
        `;

        tbody.appendChild(tr);

        if (productId) {
            handleProductChange(rowIndex);
        } else {
            calculateSubtotal(rowIndex);
        }

        rowIndex++;
    }

    function handleProductChange(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const select = row.querySelector('.product-select');
        const costInput = row.querySelector('.unit-cost-input');
        const selectedOption = select.options[select.selectedIndex];

        if (selectedOption && selectedOption.dataset.cost) {
            costInput.value = parseFloat(selectedOption.dataset.cost);
        }

        calculateSubtotal(idx);
    }

    function calculateSubtotal(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const costInput = row.querySelector('.unit-cost-input');
        const qtyInput = row.querySelector('.qty-input');
        const subtotalCell = document.getElementById(`subtotal-${idx}`);

        const cost = parseFloat(costInput.value) || 0;
        const qty = parseInt(qtyInput.value) || 0;
        const subtotal = cost * qty;

        subtotalCell.innerText = formatRupiah(subtotal);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#itemsBody tr');
        rows.forEach(r => {
            const cost = parseFloat(r.querySelector('.unit-cost-input')?.value) || 0;
            const qty = parseInt(r.querySelector('.qty-input')?.value) || 0;
            total += (cost * qty);
        });

        document.getElementById('grandTotalDisplay').innerText = formatRupiah(total);
    }

    function removeRow(idx) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('Minimal harus ada 1 baris produk dalam transaksi pembelian.');
            return;
        }

        const row = document.getElementById(`row-${idx}`);
        if (row) {
            row.remove();
            calculateGrandTotal();
        }
    }

    document.getElementById('btnAddRow').addEventListener('click', () => {
        addRow();
    });

    // Populate initial row
    document.addEventListener('DOMContentLoaded', () => {
        addRow();
    });
</script>
@endsection
