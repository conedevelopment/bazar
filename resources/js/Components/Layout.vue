<script>
    import AppHeader from './Header';
    import AppSidebar from './Sidebar';

    export default {
        components: {
            AppHeader,
            AppSidebar,
        },

        mounted() {
            document.title = `Bazar | ${this.title}`;

            this.$inertia.on('success', (event) => {
                this.key = (new Date).getTime();
            });
        },

        watch: {
            title(newValue, oldValue) {
                document.title = `Bazar | ${newValue}`;
            },
        },

        data() {
            return {
                icon: 'dashboard',
                key: (new Date).getTime(),
                title: this.__('Dashboard'),
            };
        },

        computed: {
            user() {
                return window.Bazar.user;
            },
            message() {
                return this.$page.props.message;
            },
            hasErrors() {
                return Object.keys(this.$page.props.errors).length > 0;
            },
        },
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
                    <button type="button" class="app-mobile-header__menu-toggle" @click="$refs.sidebar.toggle">
                        <icon name="menu"></icon>
                    </button>
                </div>
                <app-header></app-header>
                <div class="app__messages">
                    <alert v-if="message" :key="`message-${$parent.key}`" closable>{{ message }}</alert>
                    <alert v-if="hasErrors" type="danger" :key="`error-${$parent.key}`" closable>
                        {{ __('There is an error!') }}
                    </alert>
                </div>
                <slot></slot>
            </div>
        </div>
    </div>
</template>
