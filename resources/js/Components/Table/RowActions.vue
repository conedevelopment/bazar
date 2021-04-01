<template>
    <dropdown ref="dropdown" align="right">
        <inertia-link :href="url" class="dropdown-item">{{ __('Edit') }}</inertia-link>
        <button type="button" class="dropdown-item" :disabled="! deletable" @click="destroy">
            {{ __('Delete') }}
        </button>
        <button type="button" class="dropdown-item" :disabled="! restorable" @click="restore">
            {{ __('Restore') }}
        </button>
    </dropdown>
</template>

<script>
    export default {
        computed: {
            url() {
                return window.location.pathname.replace(/\/?$/, `/${this.$parent.item.id}`);
            },
            deletable() {
                return this.$parent.item.hasOwnProperty('deleted_at');
            },
            restorable() {
                return this.$parent.item.hasOwnProperty('deleted_at')
                    && this.$parent.item.deleted_at;
            },
        },

        methods: {
            destroy() {
                this.$inertia.delete(this.url, {
                    only: ['results', 'message'],
                });
            },
            restore() {
                this.$inertia.patch(`${this.url}/restore`, {
                    only: ['results', 'message'],
                });
            },
        },
    }
</script>
