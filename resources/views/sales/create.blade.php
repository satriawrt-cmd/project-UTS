@extends('layouts.app')

@section('title', 'Catat Penjualan Baru (Stock-Out)')
@section('page-title', 'Form Kasir & Penjualan (Stock-Out)')

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">
    <div class="panel">
        <div class="panel-header">
            <div>
                <span class="panel-title">📤 Catat Transaksi Penjualan Baru</span>
                <span style="font-size: 13px; color: var(--text-muted); display: block;">
                    Stok produk akan berkurang secara otomatis dan aman jika stok mencukupi.
                </span>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">&larr; Batal & Kembali</a>
        </div>

        <div class="panel-body">
            <form method="POST" action="{{ route('sales.store') }}" id="saleForm" onsubmit="return validateStockBeforeSubmit();">
                @csrf

                <!-- Header Meta -->
                <div class="grid-2" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Petugas Kasir</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})"
                            disabled
                            style="background: #f1f5f9; color: #475569;"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="sold_at">Tanggal & Waktu Transaksi</label>
                        <input
                            type="datetime-local"
                            name="sold_at"
                            id="sold_at"
                            class="form-control"
                            value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}"
                        >
                    </div>
                </div>

                <!-- Multi-Item Table -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text-main);">
                        🛒 Keranjang Item Produk Penjualan
                    </h3>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnAddRow" style="color: var(--primary); border-color: var(--primary);">
                        ➕ Tambah Baris Penjualan
                    </button>
                </div>

                <div class="table-responsive" style="margin-bottom: 20px;">
                    <table class="data-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="min-width: 280px;">Produk (Stok Tersedia) <span style="color: red;">*</span></th>
                                <th style="width: 170px;">Harga Jual Satuan (Rp)</th>
                                <th style="width: 140px;">Jumlah Qty <span style="color: red;">*</span></th>
                                <th style="width: 170px; text-align: right;">Subtotal (Rp)</th>
                                <th style="width: 60px; text-align: center;">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Rows injected by JS -->
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8fafc; font-weight: bold;">
                                <td colspan="3" style="text-align: right; padding: 14px 16px; font-size: 15px;">
                                    TOTAL PENJUALAN:
                                </td>
                                <td style="text-align: right; padding: 14px 16px; font-size: 18px; color: var(--success);" id="grandTotalDisplay">
                                    Rp 0
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div id="stockErrorMessage" style="display: none; padding: 12px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; color: #b91c1c; font-size: 13px; margin-bottom: 16px;">
                    ⚠️ Terdapat item yang kuantitas penjualannya melebihi stok yang tersedia. Harap periksa kembali baris yang berwarna merah.
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSubmitSale" style="padding: 11px 24px; font-size: 15px;">
                        💳 Proses & Selesaikan Penjualan (Stock-Out)
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

    function addRow(productId = '', quantity = 1) {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let productOptions = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const selected = (p.id == productId) ? 'selected' : '';
            const stockStatus = p.stock > 0 ? `Stok: ${p.stock}` : 'HABIS';
            productOptions += `<option value="${p.id}" data-price="${p.selling_price}" data-stock="${p.stock}" ${selected}>
                ${p.name} (SKU: ${p.sku} | ${stockStatus})
            </option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-control product-select" required onchange="handleProductChange(${rowIndex})">
                    ${productOptions}
                </select>
                <div class="stock-info" id="stock-info-${rowIndex}" style="font-size: 11px; color: var(--text-muted); margin-top: 3px;"></div>
            </td>
            <td>
                <input type="text" class="form-control price-display" id="price-display-${rowIndex}" value="Rp 0" disabled style="background: #f8fafc; font-weight: 600;">
            </td>
            <td>
                <input type="number" min="1" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="${quantity}" required oninput="calculateSubtotal(${rowIndex})">
                <div class="qty-warning" id="qty-warning-${rowIndex}" style="font-size: 11px; color: var(--danger); font-weight: 600; margin-top: 3px; display: none;"></div>
            </td>
            <td style="text-align: right; font-weight: 700; font-size: 15px;" class="subtotal-cell" id="subtotal-${rowIndex}">
                Rp 0
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowIndex})" title="Hapus baris">&times;</button>
            </td>
        `;

        tbody.appendChild(tr);

        if (productId) {
            handleProductChange(rowIndex);
        }

        rowIndex++;
    }

    function handleProductChange(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const select = row.querySelector('.product-select');
        const priceDisplay = document.getElementById(`price-display-${idx}`);
        const stockInfo = document.getElementById(`stock-info-${idx}`);
        const selectedOption = select.options[select.selectedIndex];

        if (selectedOption && selectedOption.dataset.price) {
            const price = parseFloat(selectedOption.dataset.price);
            const stock = parseInt(selectedOption.dataset.stock);

            priceDisplay.value = formatRupiah(price);
            stockInfo.innerHTML = stock > 0 
                ? `Tersedia: <strong>${stock}</strong> unit` 
                : `<span style="color: var(--danger); font-weight: bold;">Stok Habis!</span>`;
        } else {
            priceDisplay.value = 'Rp 0';
            stockInfo.innerHTML = '';
        }

        calculateSubtotal(idx);
    }

    function calculateSubtotal(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const subtotalCell = document.getElementById(`subtotal-${idx}`);
        const warning = document.getElementById(`qty-warning-${idx}`);
        const selectedOption = select.options[select.selectedIndex];

        const price = selectedOption && selectedOption.dataset.price ? parseFloat(selectedOption.dataset.price) : 0;
        const stock = selectedOption && selectedOption.dataset.stock ? parseInt(selectedOption.dataset.stock) : 0;
        const qty = parseInt(qtyInput.value) || 0;

        if (selectedOption && selectedOption.value) {
            if (qty > stock) {
                warning.innerText = `Melebihi stok! (Maks: ${stock})`;
                warning.style.display = 'block';
                qtyInput.style.borderColor = 'var(--danger)';
            } else if (stock === 0) {
                warning.innerText = `Barang habis!`;
                warning.style.display = 'block';
                qtyInput.style.borderColor = 'var(--danger)';
            } else {
                warning.style.display = 'none';
                qtyInput.style.borderColor = 'var(--border)';
            }
        } else {
            warning.style.display = 'none';
            qtyInput.style.borderColor = 'var(--border)';
        }

        const subtotal = price * qty;
        subtotalCell.innerText = formatRupiah(subtotal);

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#itemsBody tr');
        rows.forEach(r => {
            const select = r.querySelector('.product-select');
            const selectedOption = select ? select.options[select.selectedIndex] : null;
            const price = selectedOption && selectedOption.dataset.price ? parseFloat(selectedOption.dataset.price) : 0;
            const qty = parseInt(r.querySelector('.qty-input')?.value) || 0;
            total += (price * qty);
        });

        document.getElementById('grandTotalDisplay').innerText = formatRupiah(total);
    }

    function removeRow(idx) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('Minimal harus ada 1 baris produk dalam transaksi penjualan.');
            return;
        }

        const row = document.getElementById(`row-${idx}`);
        if (row) {
            row.remove();
            calculateGrandTotal();
        }
    }

    function validateStockBeforeSubmit() {
        const rows = document.querySelectorAll('#itemsBody tr');
        let hasError = false;

        rows.forEach(r => {
            const select = r.querySelector('.product-select');
            const selectedOption = select ? select.options[select.selectedIndex] : null;
            if (selectedOption && selectedOption.value) {
                const stock = parseInt(selectedOption.dataset.stock) || 0;
                const qty = parseInt(r.querySelector('.qty-input')?.value) || 0;

                if (qty > stock || stock <= 0) {
                    hasError = true;
                }
            }
        });

        const errorBox = document.getElementById('stockErrorMessage');
        if (hasError) {
            errorBox.style.display = 'block';
            alert('Transaksi penjualan tidak dapat diproses karena ada produk dengan kuantitas melebihi stok tersedia!');
            return false;
        } else {
            errorBox.style.display = 'none';
            return true;
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
