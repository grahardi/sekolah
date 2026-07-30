@if ($paginator->hasPages())
<div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;">
    <p style="color:#64748b;margin:0;">
        Menampilkan
        <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
        dari <strong>{{ $paginator->total() }}</strong> data
    </p>
    <div style="display:flex;align-items:center;gap:4px;">
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#f1f5f9;color:#cbd5e1;cursor:not-allowed;">
                <i class="ti ti-chevron-left" style="font-size:14px;"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#fff;border:1px solid #e9ecef;color:#374151;text-decoration:none;">
                <i class="ti ti-chevron-left" style="font-size:14px;"></i>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;color:#94a3b8;font-size:13px;">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#2563EB;color:#fff;font-weight:700;font-size:13px;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#fff;border:1px solid #e9ecef;color:#374151;text-decoration:none;font-size:13px;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#fff;border:1px solid #e9ecef;color:#374151;text-decoration:none;">
                <i class="ti ti-chevron-right" style="font-size:14px;"></i>
            </a>
        @else
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;background:#f1f5f9;color:#cbd5e1;cursor:not-allowed;">
                <i class="ti ti-chevron-right" style="font-size:14px;"></i>
            </span>
        @endif
    </div>
</div>
@endif
