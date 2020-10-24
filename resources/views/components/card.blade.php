<section class="card">
    <div class="card__header">
        <h2 class="card__title">{{ $title }}</h2>
        @isset($header)
            {{ $header }}
        @endif
    </div>
    <div class="card__inner">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card__footer">
            {{ $footer }}
        </div>
    @endif
</section>
