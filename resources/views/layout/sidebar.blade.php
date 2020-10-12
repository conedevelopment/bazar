<div class="modal app-sidebar-overlay" v-cloak v-show="$root.isSidebarOpen" @click.self="$root.isSidebarOpen = false"></div>
<aside class="app__sidebar app-sidebar" :class="{ 'is-open': $root.isSidebarOpen }">
    <div class="app-logo-wrapper">
        <inertia-link href="{{ URL::route('bazar.dashboard') }}" class="app-logo">
            <img src="{{ URL::asset('vendor/bazar/img/bazar-logo.svg') }}" alt="">
        </inertia-link>
    </div>
    <div id="app-sidebar-inside" class="app-sidebar__inside" data-simplebar>
        <section>
            <ul class="app-sidebar-menu">
                <li class="app-sidebar-menu-item">
                    <inertia-link
                        href="{{ URL::route('bazar.dashboard') }}"
                        class="app-sidebar-menu-link {{ Route::currentRouteName() === 'bazar.dashboard' ? 'is-active' : '' }}"
                    >
                        <span class="app-sidebar-menu-link__icon"><icon icon="dashboard"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Dashboard') }}</span>
                    </inertia-link>
                </li>
            </ul>
        </section>
        <section class="mt-4">
            <h2 class="app-sidebar__title">{{ __('Shop') }}</h2>
            <ul class="app-sidebar-menu">
                <li class="app-sidebar-menu-item {{ Str::is('bazar.orders.*', Route::currentRouteName()) ? 'is-open' : '' }}">
                    <inertia-link href="{{ URL::route('bazar.orders.index') }}" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="shop-basket"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Orders') }}</span>
                    </inertia-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.orders.index') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.orders.index' ? 'is-active' : '' }}"
                            >
                                {{ __('All Orders') }}
                            </inertia-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.orders.create') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.orders.create' ? 'is-active' : '' }}"
                            >
                                {{ __('Create Order') }}
                            </inertia-link>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar-menu-item {{ Str::is('bazar.products.*', Route::currentRouteName()) ? 'is-open' : '' }}">
                    <inertia-link href="{{ URL::route('bazar.products.index') }}" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="product"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Products') }}</span>
                    </inertia-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.products.index') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.products.index' ? 'is-active' : '' }}"
                            >
                                {{ __('All Products') }}
                            </inertia-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.products.create') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.products.create' ? 'is-active' : '' }}"
                            >
                                {{ __('Create Product') }}
                            </inertia-link>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar-menu-item {{ Str::is('bazar.categories.*', Route::currentRouteName()) ? 'is-open' : '' }}">
                    <inertia-link href="{{ URL::route('bazar.categories.index') }}" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="category"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Categories') }}</span>
                    </inertia-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.categories.index') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.categories.index' ? 'is-active' : '' }}"
                            >
                                {{ __('All Categories') }}
                            </inertia-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.categories.create') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.categories.create' ? 'is-active' : '' }}"
                            >
                                {{ __('Create Category') }}
                            </inertia-link>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar-menu-item {{ Str::is('bazar.users.*', Route::currentRouteName()) ? 'is-open' : '' }}">
                    <inertia-link href="{{ URL::route('bazar.users.index') }}" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="customer"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Users') }}</span>
                    </inertia-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.users.index') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.users.index' ? 'is-active' : '' }}"
                            >
                                {{ __('All Users') }}
                            </inertia-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <inertia-link
                                href="{{ URL::route('bazar.users.create') }}"
                                class="app-sidebar-submenu-link {{ Route::currentRouteName() === 'bazar.users.create' ? 'is-active' : '' }}"
                            >
                                {{ __('Create User') }}
                            </inertia-link>
                        </li>
                    </ul>
                </li>
            </ul>
        </section>
        <section class="mt-4">
            <h2 class="app-sidebar__title">{{ __('Tools') }}</h2>
            <ul class="app-sidebar-menu">
                <li class="app-sidebar-menu-item">
                    <inertia-link
                        href="{{ URL::route('bazar.support') }}"
                        class="app-sidebar-menu-link {{ Route::currentRouteName() === 'bazar.support' ? 'is-active' : '' }}"
                    >
                        <span class="app-sidebar-menu-link__icon"><icon icon="support"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Support') }}</span>
                    </inertia-link>
                </li>
            </ul>
        </section>
    </div>
    <div class="app-sidebar__footer">
        <dropdown direction="up" style="max-width: 100%;">
            <template #trigger>
                <button class="loggedin-user dropdown-toggle" type="button" style="max-width: 100%;">
                    <span class="loggedin-user__welcome">{{ __('Hi') }},</span>
                    <span class="loggedin-user__name" style="max-width: 100%; overflow: hidden; text-overflow: ellipsis;">
                        {{ $admin->name }}
                    </span>
                    <img class="loggedin-user__avatar" src="{{ $admin->avatar }}" alt="{{ $admin->name }}">
                </button>
            </template>
            <template #default>
                <h6 class="dropdown-header">{{ __('User') }}</h6>
                <button type="submit" form="logout-form" class="dropdown-item">{{ __('Logout') }}</button>
            </template>
        </dropdown>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</aside>
