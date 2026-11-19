<div class="empty">
    <div class="empty-icon">⌂</div>
    <p>{{ $text ?? 'Belum ada data.' }}</p>
    @if (! empty($actionLabel) && ! empty($actionUrl))
        <a class="btn btn-primary" href="{{ $actionUrl }}">{{ $actionLabel }}</a>
    @endif
</div>
