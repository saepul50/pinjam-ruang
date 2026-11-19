<header class="page-header">
    <div>
        <div class="eyebrow">{{ $eyebrow }}</div>
        <h1 class="page-title">{{ $title }}</h1>
        @if (! empty($subtitle))
            <p class="page-intro">{{ $subtitle }}</p>
        @endif
    </div>
</header>
