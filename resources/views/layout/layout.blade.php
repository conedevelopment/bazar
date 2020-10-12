<div class="app">
    {{-- Sidebar --}}
    @include ('bazar::layout.sidebar')

    <div class="app__main">
        <div class="app__body">
            <div class="app-mobile-header">
                <inertia-link href="/bazar" class="app-mobile-header__logo">
                    <img src="{{ URL::asset('vendor/bazar/img/bazar-logo.svg') }}" alt="">
                </inertia-link>
                <button
                    type="button"
                    class="app-mobile-header__menu-toggle"
                    @click.prevent="$root.isSidebarOpen = ! $root.isSidebarOpen"
                >
                    <icon icon="menu"></icon>
                </button>
            </div>

            {{-- Header --}}
            @include ('bazar::layout.header')

            {{-- Alert --}}
            @if ($message)
                <alert @if (! empty((array) $errors)) type="danger" @endif closable>{{ $message }}</alert>
            @endif

            {{-- Content --}}
            @yield ('content')
        </div>
    </div>
</div>
