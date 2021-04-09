<template>
    <div class="app__sidebar">
        <div class="modal app-sidebar-overlay" v-show="isOpen" @click.self="close"></div>
        <aside class="app-sidebar" :class="{ 'is-open': isOpen }">
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
                                <span class="app-sidebar-menu-link__icon"><icon name="dashboard"></icon></span>
                                <span class="app-sidebar-menu-link__caption">{{ __('Dashboard') }}</span>
                            </inertia-link>
                        </li>
                    </ul>
                </section>
                <section v-for="(items, group) in groups" :key="group" class="mt-4">
                    <h2 class="app-sidebar__title">{{ group }}</h2>
                    <ul class="app-sidebar-menu">
                        <li v-for="(item, route) in items" :key="route" class="app-sidebar-menu-item">
                            <inertia-link :href="route" class="app-sidebar-menu-link">
                                <span class="app-sidebar-menu-link__icon"><icon :name="item.icon"></icon></span>
                                <span class="app-sidebar-menu-link__caption">{{ item.label }}</span>
                            </inertia-link>
                            <ul v-if="Object.keys(item.items).length > 0" class="app-sidebar-submenu">
                                <li v-for="(label, route) in item.items" :key="route" class="app-sidebar-submenu-item">
                                    <inertia-link :href="route" class="app-sidebar-submenu-link">
                                        {{ label }}
                                    </inertia-link>
                                </li>
                            </ul>
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
                                {{ $parent.user.name }}
                            </span>
                            <img class="loggedin-user__avatar" :src="$parent.user.avatar" :alt="$parent.user.name">
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
                    <input type="hidden" name="_token" :value="$page.props.csrf_token">
                </form>
            </div>
        </aside>
    </div>
</template>

<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        mounted() {
            this.setActiveLinks();

            this.$inertia.on('success', (event) => {
                this.setActiveLinks();
            });
        },

        computed: {
            groups() {
                return window.Bazar.menu;
            },
        },

        methods: {
            setActiveLinks() {
                const path = window.location.pathname.replace(/\/$/, '');

                this.$el.querySelectorAll('a.app-sidebar-menu-link').forEach((el) => {
                    if (el.pathname !== '/bazar' && path.includes(el.pathname)) {
                        el.closest('.app-sidebar-menu-item').classList.add('is-open', 'is-active');
                    } else {
                        el.closest('.app-sidebar-menu-item').classList.remove('is-open', 'is-active');
                    }
                });

                this.$el.querySelectorAll('.app-sidebar__inside a').forEach((el) => {
                    if (path === el.pathname) {
                        el.classList.add('is-active');
                    } else {
                        el.classList.remove('is-active');
                    }
                });
            }
        }
    }
</script>
