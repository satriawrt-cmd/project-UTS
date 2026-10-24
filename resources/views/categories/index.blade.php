@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page-title', 'Kelola Kategori Produk')

@section('content')
<div class="grid-3">
    <!-- Form Tambah Kategori (Owner Only) -->
    @if(auth()->user()->isOwner())
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-header">
                <span class="panel-title">➕ Tambah Kategori Baru</span>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="category_name">Nama Kategori</label>
                        <input
                            type="text"
                            name="name"
                            id="category_name"
                            class="form-control"
                            placeholder="Contoh: Elektronik & Gadget"
                            value="{{ old('name') }}"
                            required
                        >
                        <span class="form-help">Nama kategori harus unik dalam sistem.</span>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Daftar Kategori (2 Col) -->
    <div class="panel" style="{{ auth()->user()->isOwner() ? 'grid-column: span 2;' : 'grid-column: span 3;' }} margin-bottom: 0;">
        <div class="panel-header">
            <span class="panel-title">📁 Daftar Kategori Produk</span>

            <!-- Filter Search -->
            <form method="GET" action="{{ route('categories.index') }}" style="display: flex; gap: 8px;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    style="padding: 6px 12px; font-size: 13px; width: 200px;"
                    placeholder="Cari kategori..."
                    value="{{ request('search') }}"
                >
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
                @if(request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        @if(auth()->user()->isOwner())
                            <th style="text-align: right;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $category->products_count }} Produk</span>
                            </td>
                            @if(auth()->user()->isOwner())
                                <td style="text-align: right; white-space: nowrap;">
                                    <button
                                        type="button"
                                        class="btn btn-secondary btn-sm"
                                        onclick="openEditCategory({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                    >
                                        Edit
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('categories.destroy', $category) }}"
                                        style="display: inline;"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'{{ $category->name }}\'? Kategori yang memiliki produk tidak dapat dihapus.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isOwner() ? 4 : 3 }}" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                Belum ada kategori yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="panel-body" style="padding: 12px 24px;">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Edit Kategori -->
@if(auth()->user()->isOwner())
    <div id="editCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 12px; width: 100%; max-width: 450px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div class="panel-header">
                <span class="panel-title">✏️ Edit Kategori</span>
                <button type="button" onclick="closeEditCategory()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
            </div>
            <form id="editCategoryForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="panel-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_category_name">Nama Kategori</label>
                        <input
                            type="text"
                            name="name"
                            id="edit_category_name"
                            class="form-control"
                            required
                        >
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
                        <button type="button" class="btn btn-secondary" onclick="closeEditCategory()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    function openEditCategory(id, name) {
        const modal = document.getElementById('editCategoryModal');
        const form = document.getElementById('editCategoryForm');
        const nameInput = document.getElementById('edit_category_name');

        form.action = `/categories/${id}`;
        nameInput.value = name;
        modal.style.display = 'flex';
    }

    function closeEditCategory() {
        document.getElementById('editCategoryModal').style.display = 'none';
    }
</script>
@endsection
