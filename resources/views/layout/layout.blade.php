<div class="app">
    {{-- Sidebar --}}
    <div class="modal app-sidebar-overlay" v-cloak v-show="$parent.isSidebarOpen" @click.self="$parent.closeSidebar"></div>
    @include ('bazar::layout.sidebar')

    {{-- Main --}}
    <div class="app__main">
        <div class="app__body">
            {{-- Header --}}
            <div class="app-mobile-header">
                <bazar-link href="{{ URL::route('bazar.dashboard') }}" class="app-mobile-header__logo">
                    <img src="{{ URL::asset('vendor/bazar/img/bazar-logo.svg') }}" alt="">
                </bazar-link>
                <button type="button" class="app-mobile-header__menu-toggle" @click.prevent="$parent.toggleSidebar">
                    <icon icon="menu"></icon>
                </button>
            </div>
            @include ('bazar::layout.header')

            {{-- Message --}}
            @if (Session::has('message'))
                <alert closable>{{ Session::get('message') }}</alert>
            @endif

            {{-- Error --}}
            @if (Session::has('error'))
                <alert type="danger" closable>{{ Session::get('error') }}</alert>
            @endif

            {{-- Content --}}
            <div class="app__content">
                @yield ('content')
            </div>
        </div>
    </div>
</div>
