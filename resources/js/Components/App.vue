<script>
    export default {
        props: {
            page: {
                type: Object,
                required: true
            }
        },

        mounted() {
            this.$router.dispatcher.addEventListener('success', event => {
                this.component = {
                    name: event.detail.component,
                    template: event.detail.html
                };
            });
        },

        data() {
            return {
                loading: false,
                isSidebarOpen: false,
                component: {
                    name: this.page.component,
                    template: this.$root.$el.innerHTML,
                }
            };
        },

        methods: {
            openSidebar() {
                this.isSidebarOpen = true;
            },
            closeSidebar() {
                this.isSidebarOpen = false;
            },
            toggleSidebar() {
                this.isSidebarOpen = ! this.isSidebarOpen;
            }
        }
    }
</script>

<template>
    <div id="app">
        <component :is="component"></component>
    </div>
</template>
