@if ($paginator->hasPages())
    <nav class="custom-pagination" role="navigation" aria-label="Navigasi Halaman">
        <div class="pagination-info">
            Menampilkan
            <strong>{{ $paginator->firstItem() }}</strong>
            sampai
            <strong>{{ $paginator->lastItem() }}</strong>
            dari
            <strong>{{ $paginator->total() }}</strong>
            data
        </div>

        <ul class="pagination-links">
            {{-- Tombol Halaman Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&larr; Sebelumnya</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&larr; Sebelumnya</a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Tanda Titik-Titik (...) --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Halaman Berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &rarr;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Berikutnya &rarr;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
