@php
    $query = request()->query();
    $nextDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
    $isActive = $sort === $column;
@endphp

<a
    href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => 1]) }}"
    class="inline-flex items-center gap-1 transition hover:text-slate-900"
>
    <span>{{ $label }}</span>

    @if ($isActive)
        <span class="text-[10px] font-bold">
            {{ $direction === 'asc' ? '▲' : '▼' }}
        </span>
    @else
        <span class="text-[10px] text-slate-300">↕</span>
    @endif
</a>
