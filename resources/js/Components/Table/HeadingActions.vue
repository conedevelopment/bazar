<script>
    export default {
        computed: {
            url() {
                return window.location.pathname.replace(/\/$/, '');
            },
            deletable() {
                return this.$parent.$parent.selection.some((item) => {
                    return item.hasOwnProperty('deleted_at');
                });
            },
            restorable() {
                return this.$parent.$parent.selection.some((item) => {
                    return item.hasOwnProperty('deleted_at') && item.deleted_at;
                });
            },
            id() {
                return this.$parent.$parent.selection.map((item) => item.id);
            },
        },

        methods: {
            destroy() {
                this.$inertia.visit(`${this.url}/batch-destroy`, {
                    method: 'DELETE',
                    data: { id: this.id },
                    only: ['results', 'message'],
                });
            },
            restore() {
                this.$inertia.visit(`${this.url}/batch-restore`, {
                    method: 'PATCH',
                    data: { id: this.id },
                    only: ['results', 'message'],
                });
            },
        },
    }
</script>

<template>
    <dropdown ref="dropdown">
        <h6 class="dropdown-header">
            {{ __(':items selected', { items: this.id.length }) }}
        </h6>
        <button type="button" class="dropdown-item" :disabled="! deletable" @click="destroy">
            {{ __('Delete') }}
        </button>
        <button type="button" class="dropdown-item" :disabled="! restorable" @click="restore">
            {{ __('Restore') }}
        </button>
    </dropdown>
</template>
