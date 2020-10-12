<nav class="app-header__breadcrumb" aria-label="{{ __('Breadcrumbs') }}">
    <ol class="breadcrumb">
        @foreach ($items as $path => $label)
            <li class="breadcrumb-item {{ $loop->last ? 'is-active' : '' }}">
                @if ($loop->last)
                    <span>{{ $label }}</span>
                @else
                    <inertia-link href="{{ URL::to($path) }}">
                        {{ $label }}
                    </inertia-link>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
