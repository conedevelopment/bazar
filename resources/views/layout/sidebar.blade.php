<aside class="app__sidebar app-sidebar" :class="{ 'is-open': $parent.isSidebarOpen }">
    <div class="app-logo-wrapper">
        <bazar-link href="{{ URL::route('bazar.dashboard') }}" class="app-logo">
            <img src="{{ URL::asset('vendor/bazar/img/bazar-logo.svg') }}" alt="">
        </bazar-link>
    </div>
    <div id="app-sidebar-inside" class="app-sidebar__inside" data-simplebar>
        <section>
            <ul class="app-sidebar-menu">
                <li class="app-sidebar-menu-item">
                    <bazar-link href="{{ URL::route('bazar.dashboard') }}" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="dashboard"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Dashboard') }}</span>
                    </bazar-link>
                </li>
            </ul>
        </section>
        <section class="mt-4">
            <h2 class="app-sidebar__title">{{ __('Shop') }}</h2>
            <ul class="app-sidebar-menu">
                <li class="app-sidebar-menu-item">
                    <bazar-link href="/bazar/orders" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="shop-basket"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Orders') }}</span>
                    </bazar-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/orders" class="app-sidebar-submenu-link">
                                {{ __('All Orders') }}
                            </bazar-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/orders/create" class="app-sidebar-submenu-link">
                                {{ __('Create Order') }}
                            </bazar-link>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar-menu-item">
                    <bazar-link href="/bazar/products" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="product"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Products') }}</span>
                    </bazar-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/products" class="app-sidebar-submenu-link">
                                {{ __('All Products') }}
                            </bazar-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/products/create" class="app-sidebar-submenu-link">
                                {{ __('Create Product') }}
                            </bazar-link>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar-menu-item">
                    <bazar-link href="/bazar/categories" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="category"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Categories') }}</span>
                    </bazar-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/categories" class="app-sidebar-submenu-link">
                                {{ __('All Categories') }}
                            </bazar-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/categories/create" class="app-sidebar-submenu-link">
                                {{ __('Create Category') }}
                            </bazar-link>
                        </li>
                    </ul>
                </li>
                <li class="app-sidebar-menu-item">
                    <bazar-link href="/bazar/users" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="customer"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Users') }}</span>
                    </bazar-link>
                    <ul class="app-sidebar-submenu">
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/users" class="app-sidebar-submenu-link">
                                {{ __('All Users') }}
                            </bazar-link>
                        </li>
                        <li class="app-sidebar-submenu-item">
                            <bazar-link href="/bazar/users/create" class="app-sidebar-submenu-link">
                                {{ __('Create User') }}
                            </bazar-link>
                        </li>
                    </ul>
                </li>
            </ul>
        </section>
        <section class="mt-4">
            <h2 class="app-sidebar__title">{{ __('Tools') }}</h2>
            <ul class="app-sidebar-menu">
                <li class="app-sidebar-menu-item">
                    <bazar-link href="/bazar/support" class="app-sidebar-menu-link">
                        <span class="app-sidebar-menu-link__icon"><icon icon="support"></icon></span>
                        <span class="app-sidebar-menu-link__caption">{{ __('Support') }}</span>
                    </bazar-link>
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
                    <img class="loggedin-user__avatar" src="{{ $admin->avatar }}" alt="">
                </button>
            </template>
            <template #default>
                <h6 class="dropdown-header">{{ __('User') }}</h6>
                <bazar-link href="/bazar/profile" class="dropdown-item">
                    {{ __('Profile') }}
                </bazar-link>
                <bazar-link href="/bazar/password" class="dropdown-item">
                    {{ __('Password') }}
                </bazar-link>
                <div class="dropdown-divider"></div>
                <button type="submit" form="logout-form" class="dropdown-item">
                    {{ __('Logout') }}
                </button>
            </template>
        </dropdown>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</aside>
