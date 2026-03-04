@if ($paginator->hasPages())
    <div style="text-align: center; margin-top: 16px;">
        <span style="font-size: 13px; color: #6b7280;">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>
        <br>
        <div style="display: inline-flex; gap: 6px; margin-top: 8px;">
            @if ($paginator->onFirstPage())
                <span style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; color: #9ca3af; cursor: not-allowed; font-size: 12px;">← Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; color: #2563eb; text-decoration: none; font-size: 12px; display: inline-block;">← Previous</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; color: #2563eb; text-decoration: none; font-size: 12px; display: inline-block;">Next →</a>
            @else
                <span style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; color: #9ca3af; cursor: not-allowed; font-size: 12px;">Next →</span>
            @endif
        </div>
    </div>
@endif
