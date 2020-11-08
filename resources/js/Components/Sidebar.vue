<script>
    export default {
        mounted() {
            this.setActiveLinks();

            this.$inertia.on('success', event => {
                this.setActiveLinks();
            });
        },

        methods: {
            setActiveLinks() {
                const path = window.location.pathname.replace(/\/$/, '');

                this.$el.querySelectorAll('.app-sidebar__inside a.is-active').forEach(el => {
                    el.classList.remove('is-active');
                });

                this.$el.querySelectorAll('.app-sidebar-menu-item.is-open').forEach(el => {
                    el.classList.remove('is-open');
                });

                this.$el.querySelectorAll(`.app-sidebar__inside a[href$="${path}"]`).forEach(el => {
                    el.classList.add('is-active');
                    el.closest('.app-sidebar-menu-item').classList.add('is-open');
                });
            }
        }
    }
</script>

<template>
    <aside class="app__sidebar app-sidebar" :class="{ 'is-open': $parent.isSidebarOpen }">
        <div class="app-logo-wrapper">
            <inertia-link href="/bazar" class="app-logo">
                <img src="/vendor/bazar/img/bazar-logo.svg" alt="">
            </inertia-link>
        </div>
        <div id="app-sidebar-inside" class="app-sidebar__inside" data-simplebar>
            <section>
                <ul class="app-sidebar-menu">
                    <li class="app-sidebar-menu-item">
                        <inertia-link href="/bazar" class="app-sidebar-menu-link">
                            <span class="app-sidebar-menu-link__icon"><icon icon="dashboard"></icon></span>
                            <span class="app-sidebar-menu-link__caption">{{ __('Dashboard') }}</span>
                        </inertia-link>
                    </li>
                </ul>
            </section>
            <section class="mt-4">
                <h2 class="app-sidebar__title">{{ __('Shop') }}</h2>
                <ul class="app-sidebar-menu">
                    <li class="app-sidebar-menu-item">
                        <inertia-link href="/bazar/orders" class="app-sidebar-menu-link">
                            <span class="app-sidebar-menu-link__icon"><icon icon="shop-basket"></icon></span>
                            <span class="app-sidebar-menu-link__caption">{{ __('Orders') }}</span>
                        </inertia-link>
                        <ul class="app-sidebar-submenu">
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/orders" class="app-sidebar-submenu-link">
                                    {{ __('All Orders') }}
                                </inertia-link>
                            </li>
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/orders/create" class="app-sidebar-submenu-link">
                                    {{ __('Create Order') }}
                                </inertia-link>
                            </li>
                        </ul>
                    </li>
                    <li class="app-sidebar-menu-item">
                        <inertia-link href="/bazar/products" class="app-sidebar-menu-link">
                            <span class="app-sidebar-menu-link__icon"><icon icon="product"></icon></span>
                            <span class="app-sidebar-menu-link__caption">{{ __('Products') }}</span>
                        </inertia-link>
                        <ul class="app-sidebar-submenu">
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/products" class="app-sidebar-submenu-link">
                                    {{ __('All Products') }}
                                </inertia-link>
                            </li>
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/products/create" class="app-sidebar-submenu-link">
                                    {{ __('Create Product') }}
                                </inertia-link>
                            </li>
                        </ul>
                    </li>
                    <li class="app-sidebar-menu-item">
                        <inertia-link href="/bazar/categories" class="app-sidebar-menu-link">
                            <span class="app-sidebar-menu-link__icon"><icon icon="category"></icon></span>
                            <span class="app-sidebar-menu-link__caption">{{ __('Categories') }}</span>
                        </inertia-link>
                        <ul class="app-sidebar-submenu">
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/categories" class="app-sidebar-submenu-link">
                                    {{ __('All Categories') }}
                                </inertia-link>
                            </li>
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/categories/create" class="app-sidebar-submenu-link">
                                    {{ __('Create Category') }}
                                </inertia-link>
                            </li>
                        </ul>
                    </li>
                    <li class="app-sidebar-menu-item">
                        <inertia-link href="/bazar/users" class="app-sidebar-menu-link">
                            <span class="app-sidebar-menu-link__icon"><icon icon="customer"></icon></span>
                            <span class="app-sidebar-menu-link__caption">{{ __('Users') }}</span>
                        </inertia-link>
                        <ul class="app-sidebar-submenu">
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/users" class="app-sidebar-submenu-link">
                                    {{ __('All Users') }}
                                </inertia-link>
                            </li>
                            <li class="app-sidebar-submenu-item">
                                <inertia-link href="/bazar/users/create" class="app-sidebar-submenu-link">
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
                        <inertia-link href="/bazar/support" class="app-sidebar-menu-link">
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
                            {{ $page.admin.name }}
                        </span>
                        <img class="loggedin-user__avatar" :src="$page.admin.avatar" :alt="$page.admin.name">
                    </button>
                </template>
                <template #default>
                    <h6 class="dropdown-header">{{ __('User') }}</h6>
                    <inertia-link href="/bazar/profile" class="dropdown-item">
                        {{ __('Profile') }}
                    </inertia-link>
                    <inertia-link href="/bazar/password" class="dropdown-item">
                        {{ __('Password') }}
                    </inertia-link>
                    <div class="dropdown-divider"></div>
                    <button type="submit" form="logout-form" class="dropdown-item">
                        {{ __('Logout') }}
                    </button>
                </template>
            </dropdown>
            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                <input type="hidden" name="_token" :value="$page.csrf_token">
            </form>
        </div>
    </aside>
</template>
