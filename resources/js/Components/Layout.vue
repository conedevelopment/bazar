<script>
    import AppHeader from './Header';
    import AppSidebar from './Sidebar';

    export default {
        components: {
            AppHeader,
            AppSidebar
        },

        mounted() {
            this.setTitle();

            this.$inertia.on('success', event => {
                this.setTitle();
            });
        },

        data() {
            return {
                title: null
            };
        },

        computed: {
            message() {
                return this.$page.message || null;
            },
            error() {
                return Object.keys(this.$page.errors).length > 0 ? this.__('Something went wrong!') : null;
            }
        },

        methods: {
            setTitle() {
                const title = this.$slots.default[0].componentInstance.title;

                this.title = title;

                document.title = 'Bazar' + (title ? ` | ${title}` : '');
            }
        }
    }
</script>

<template>
    <div class="app">
        <app-sidebar ref="sidebar"></app-sidebar>
        <div class="app__main">
            <div class="app__body">
                <div class="app-mobile-header">
                    <inertia-link href="/bazar" class="app-mobile-header__logo">
                        <img src="/vendor/bazar/img/bazar-logo.svg" alt="">
                    </inertia-link>
                    <button type="button" class="app-mobile-header__menu-toggle" @click.prevent="$refs.sidebar.toggle">
                        <icon icon="menu"></icon>
                    </button>
                </div>
                <app-header></app-header>
                <alert v-if="message" closable>{{ message }}</alert>
                <alert v-if="error" type="danger" closable>{{ error }}</alert>
                <slot></slot>
            </div>
        </div>
    </div>
</template>
