<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Demo Accounts)
        $owner = User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner Admin',
                'password' => Hash::make('OwnerDemo2026!'),
                'role' => 'owner',
            ]
        );

        $staff1 = User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('StaffDemo2026!'),
                'role' => 'staff',
            ]
        );

        $staff2 = User::updateOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Staff Kasir',
                'password' => Hash::make('StaffDemo2026!'),
                'role' => 'staff',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Pelanggan Demo',
                'password' => Hash::make('UserDemo2026!'),
                'role' => 'user',
            ]
        );


        // 2. Seed Categories
        $categoriesData = [
            'Makanan & Minuman',
            'Perlengkapan Kantor',
            'Elektronik & Gadget',
            'Kebutuhan Pokok',
            'Perawatan & Kebersihan',
        ];

        $categories = [];
        foreach ($categoriesData as $catName) {
            $categories[$catName] = Category::firstOrCreate(['name' => $catName]);
        }

        // 3. Seed Suppliers
        $suppliersData = [
            [
                'name' => 'PT Sumber Makmur Sejahtera',
                'phone' => '0812-3456-7890',
                'email' => 'sales@sumbermakmur.co.id',
            ],
            [
                'name' => 'CV Berkah Pangan Mandiri',
                'phone' => '0821-9876-5432',
                'email' => 'order@berkahpangan.com',
            ],
            [
                'name' => 'PT Distribusi Logistik Nusantara',
                'phone' => '0813-7788-9900',
                'email' => 'info@nusantaradistribusi.id',
            ],
            [
                'name' => 'UD ATK Prima Sentosa',
                'phone' => '0856-1122-3344',
                'email' => 'support@primasentosa.com',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[] = Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Seed Products
        $productsData = [
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-001',
                'name' => 'Kopi Arabika Toraja 250g',
                'purchase_price' => 38000,
                'selling_price' => 52000,
                'stock' => 30,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-002',
                'name' => 'Teh Celup Melati Premium Box',
                'purchase_price' => 16000,
                'selling_price' => 24000,
                'stock' => 20,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-003',
                'name' => 'Biskuit Gandum Cokelat Kaleng',
                'purchase_price' => 28000,
                'selling_price' => 40000,
                'stock' => 4, // Low stock trigger
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-001',
                'name' => 'Kertas HVS A4 80gsm 1 Rim',
                'purchase_price' => 47000,
                'selling_price' => 60000,
                'stock' => 45,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-002',
                'name' => 'Bolpoin Gel Hitam 0.5 Box isi 12',
                'purchase_price' => 22000,
                'selling_price' => 32000,
                'stock' => 3, // Low stock trigger
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-001',
                'name' => 'Mouse Wireless Ergonomic',
                'purchase_price' => 65000,
                'selling_price' => 95000,
                'stock' => 14,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-002',
                'name' => 'Keyboard Mechanical TKL Blue Switch',
                'purchase_price' => 185000,
                'selling_price' => 265000,
                'stock' => 7,
                'minimum_stock' => 4,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-001',
                'name' => 'Beras Pandan Wangi 5kg',
                'purchase_price' => 68000,
                'selling_price' => 82000,
                'stock' => 50,
                'minimum_stock' => 20,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-002',
                'name' => 'Minyak Goreng Sawit 2 Liter',
                'purchase_price' => 31000,
                'selling_price' => 37500,
                'stock' => 5, // Low stock trigger
                'minimum_stock' => 12,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-001',
                'name' => 'Sabun Cuci Piring Jeruk Nipis 750ml',
                'purchase_price' => 12500,
                'selling_price' => 18000,
                'stock' => 25,
                'minimum_stock' => 10,
            ],
            // Additional Products to enrich catalog
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-004',
                'name' => 'Susu UHT Full Cream 1 Liter',
                'purchase_price' => 15000,
                'selling_price' => 21000,
                'stock' => 35,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-005',
                'name' => 'Sirup Rasa Karamel Vanila 750ml',
                'purchase_price' => 24000,
                'selling_price' => 34000,
                'stock' => 18,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-006',
                'name' => 'Keripik Kentang Balado Pedas 150g',
                'purchase_price' => 11000,
                'selling_price' => 16500,
                'stock' => 40,
                'minimum_stock' => 12,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-003',
                'name' => 'Binder Clip No. 260 Kotak isi 12',
                'purchase_price' => 8500,
                'selling_price' => 13000,
                'stock' => 50,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-004',
                'name' => 'Spidol Whiteboard Marker Set 4 Warna',
                'purchase_price' => 26000,
                'selling_price' => 36000,
                'stock' => 22,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-005',
                'name' => 'Stapler HD-50 & Isi Ulang Staples',
                'purchase_price' => 19000,
                'selling_price' => 28000,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-003',
                'name' => 'Flashdisk USB 3.2 64GB High Speed',
                'purchase_price' => 52000,
                'selling_price' => 75000,
                'stock' => 28,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-004',
                'name' => 'Kabel Data Fast Charging Type-C 100W 1.5m',
                'purchase_price' => 22000,
                'selling_price' => 35000,
                'stock' => 45,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-005',
                'name' => 'Stop Kontak Multi-Plug 4 Lubang + Dual USB',
                'purchase_price' => 48000,
                'selling_price' => 69000,
                'stock' => 16,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-006',
                'name' => 'Earphone In-Ear Bass Hi-Res 3.5mm',
                'purchase_price' => 35000,
                'selling_price' => 52000,
                'stock' => 3, // Low stock trigger
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-003',
                'name' => 'Gula Pasir Tebu Kristal Putih 1kg',
                'purchase_price' => 14500,
                'selling_price' => 18000,
                'stock' => 60,
                'minimum_stock' => 20,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-004',
                'name' => 'Tepung Terigu Serbaguna Segitiga 1kg',
                'purchase_price' => 11000,
                'selling_price' => 14500,
                'stock' => 40,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-005',
                'name' => 'Garam Beryodium Halus Dapur 500g',
                'purchase_price' => 4500,
                'selling_price' => 7000,
                'stock' => 55,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-002',
                'name' => 'Deterjen Cair Konsentrat Anti Bakteri 800ml',
                'purchase_price' => 18000,
                'selling_price' => 25500,
                'stock' => 32,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-003',
                'name' => 'Pembersih Lantai Lavender Segar 750ml',
                'purchase_price' => 11500,
                'selling_price' => 16000,
                'stock' => 28,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-004',
                'name' => 'Tisu Wajah 2-Ply Lembut Pack 250 Lembar',
                'purchase_price' => 9500,
                'selling_price' => 14000,
                'stock' => 48,
                'minimum_stock' => 15,
            ],
            // 24 Extra Products to make catalog abundant (50 total)
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-007',
                'name' => 'Cokelat Batang Dark 65% Premium 100g',
                'purchase_price' => 18000,
                'selling_price' => 25000,
                'stock' => 30,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-008',
                'name' => 'Kopi Bubuk Robusta Dampit Asli 200g',
                'purchase_price' => 22000,
                'selling_price' => 32000,
                'stock' => 25,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-009',
                'name' => 'Madu Murni Hutan Liar 350ml',
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-010',
                'name' => 'Air Mineral Botol Jumbo 1500ml Karton isi 12',
                'purchase_price' => 36000,
                'selling_price' => 48000,
                'stock' => 20,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-011',
                'name' => 'Kacang Almond Panggang Roasted Salted 250g',
                'purchase_price' => 38000,
                'selling_price' => 55000,
                'stock' => 18,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Makanan & Minuman']->id,
                'sku' => 'PRD-MKN-012',
                'name' => 'Minuman Sari Apel Murni Segar 1 Liter',
                'purchase_price' => 19000,
                'selling_price' => 28000,
                'stock' => 22,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-006',
                'name' => 'Buku Tulis Spiral A5 Hardcover 100 Lembar',
                'purchase_price' => 16000,
                'selling_price' => 24000,
                'stock' => 35,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-007',
                'name' => 'Gunting Kantor Stainless Steel 8 Inch Ergonomis',
                'purchase_price' => 12000,
                'selling_price' => 18500,
                'stock' => 24,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-008',
                'name' => 'Sticky Notes Memo Pastel Pad Set 5 Warna',
                'purchase_price' => 9000,
                'selling_price' => 14000,
                'stock' => 40,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-009',
                'name' => 'Map Dokumen Clear Holder Folio isi 20 Lembar',
                'purchase_price' => 14000,
                'selling_price' => 22000,
                'stock' => 28,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Perlengkapan Kantor']->id,
                'sku' => 'PRD-ATK-010',
                'name' => 'Kalkulator Elektronik Meja Bisnis 12 Digit',
                'purchase_price' => 42000,
                'selling_price' => 62000,
                'stock' => 14,
                'minimum_stock' => 4,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-007',
                'name' => 'Powerbank Fast Charge 10000mAh Dual Output USB-C',
                'purchase_price' => 110000,
                'selling_price' => 165000,
                'stock' => 12,
                'minimum_stock' => 4,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-008',
                'name' => 'Adaptor Kepala Charger 30W GaN Fast Charging',
                'purchase_price' => 55000,
                'selling_price' => 85000,
                'stock' => 19,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-009',
                'name' => 'Mousepad Desk Mat Gaming XL 80x30cm Anti-Slip',
                'purchase_price' => 32000,
                'selling_price' => 48000,
                'stock' => 26,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-010',
                'name' => 'Lampu Meja Kerja Belajar LED Eye Care Touch Switch',
                'purchase_price' => 48000,
                'selling_price' => 72000,
                'stock' => 15,
                'minimum_stock' => 4,
            ],
            [
                'category_id' => $categories['Elektronik & Gadget']->id,
                'sku' => 'PRD-ELK-011',
                'name' => 'USB 3.0 Hub 4 Port High Speed Splitter',
                'purchase_price' => 28000,
                'selling_price' => 42000,
                'stock' => 20,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-006',
                'name' => 'Kecap Manis Kental Gurih Alami Botol 550ml',
                'purchase_price' => 16500,
                'selling_price' => 22000,
                'stock' => 38,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-007',
                'name' => 'Saus Sambal Ekstra Pedas Botol 335ml',
                'purchase_price' => 12000,
                'selling_price' => 16500,
                'stock' => 42,
                'minimum_stock' => 12,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-008',
                'name' => 'Mie Telur Kering Keriting Cap Burung 200g',
                'purchase_price' => 5500,
                'selling_price' => 8000,
                'stock' => 50,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Kebutuhan Pokok']->id,
                'sku' => 'PRD-POK-009',
                'name' => 'Margarin Serbaguna Sachet 200g',
                'purchase_price' => 7000,
                'selling_price' => 10500,
                'stock' => 45,
                'minimum_stock' => 12,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-005',
                'name' => 'Sabun Mandi Cair Body Wash Antibakteri 450ml',
                'purchase_price' => 20000,
                'selling_price' => 28000,
                'stock' => 30,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-006',
                'name' => 'Shampoo Ginseng Kuat Anti Ketombe 340ml',
                'purchase_price' => 27000,
                'selling_price' => 38000,
                'stock' => 22,
                'minimum_stock' => 6,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-007',
                'name' => 'Pasta Gigi Herbal Perlindungan Gigi Sensitif 150g',
                'purchase_price' => 13500,
                'selling_price' => 19500,
                'stock' => 36,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Perawatan & Kebersihan']->id,
                'sku' => 'PRD-KBS-008',
                'name' => 'Hand Sanitizer Gel Pump 500ml 75% Alkohol',
                'purchase_price' => 22000,
                'selling_price' => 32000,
                'stock' => 25,
                'minimum_stock' => 8,
            ],
        ];

        $products = [];
        foreach ($productsData as $prod) {
            $products[$prod['sku']] = Product::firstOrCreate(
                ['sku' => $prod['sku']],
                $prod
            );
        }

        // 5. Seed Sample Completed Purchase
        if (Purchase::count() === 0) {
            $purchase = Purchase::create([
                'supplier_id' => $suppliers[0]->id,
                'user_id' => $staff1->id,
                'purchase_number' => 'PO-' . now()->format('Ymd') . '-0001',
                'total' => 0,
                'status' => 'completed',
                'purchased_at' => now()->subDays(2),
            ]);

            $item1Qty = 15;
            $cost1 = (float) $products['PRD-MKN-001']->purchase_price;
            $sub1 = $cost1 * $item1Qty;

            $item2Qty = 20;
            $cost2 = (float) $products['PRD-POK-001']->purchase_price;
            $sub2 = $cost2 * $item2Qty;

            $purchase->items()->createMany([
                [
                    'product_id' => $products['PRD-MKN-001']->id,
                    'quantity' => $item1Qty,
                    'unit_cost' => $cost1,
                    'subtotal' => $sub1,
                ],
                [
                    'product_id' => $products['PRD-POK-001']->id,
                    'quantity' => $item2Qty,
                    'unit_cost' => $cost2,
                    'subtotal' => $sub2,
                ],
            ]);

            $purchase->update(['total' => $sub1 + $sub2]);
        }

        // 6. Seed Sample Completed Sale
        if (Sale::count() === 0) {
            $sale = Sale::create([
                'user_id' => $staff2->id,
                'sale_number' => 'SO-' . now()->format('Ymd') . '-0001',
                'total' => 0,
                'status' => 'completed',
                'sold_at' => now()->subDay(),
            ]);

            $sQty1 = 2;
            $price1 = (float) $products['PRD-MKN-001']->selling_price;
            $sSub1 = $price1 * $sQty1;

            $sQty2 = 1;
            $price2 = (float) $products['PRD-POK-001']->selling_price;
            $sSub2 = $price2 * $sQty2;

            $sale->items()->createMany([
                [
                    'product_id' => $products['PRD-MKN-001']->id,
                    'quantity' => $sQty1,
                    'unit_price' => $price1,
                    'subtotal' => $sSub1,
                ],
                [
                    'product_id' => $products['PRD-POK-001']->id,
                    'quantity' => $sQty2,
                    'unit_price' => $price2,
                    'subtotal' => $sSub2,
                ],
            ]);

            $sale->update(['total' => $sSub1 + $sSub2]);
        }
    }
}
