<template>
    <div>
        <teleport v-if="mounted" to=".app-header__actions">
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Order') }}
            </inertia-link>
        </teleport>

        <data-table :response="response" :filters="filters">
            <data-table-column :label="__('ID')" sort="id" #default="item">
                <inertia-link :href="`${url}/${item.id}`">
                    #{{ item.id }}
                </inertia-link>
            </data-table-column>
            <data-table-column :label="__('Total')" #default="item">
                {{ item.formatted_total }}
            </data-table-column>
            <data-table-column :label="__('Customer')" #default="item">
                {{ item.address.name }}
            </data-table-column>
            <data-table-column :label="__('Status')" sort="status" #default="item">
                <span class="badge" :class="badgeClass(item.status)">
                    {{ item.status_name }}
                </span>
            </data-table-column>
            <data-table-column :label="__('Created at')" sort="created_at" #default="item">
                {{ $date(item.created_at).format('YYYY-MM-DD HH:mm') }}
            </data-table-column>
        </data-table>
    </div>
</template>

<script>
    import InteractsWithTeleport from './../../Mixins/InteractsWithTeleport';

    export default {
        mixins: [InteractsWithTeleport],

        props: {
            response: {
                type: Object,
                required: true,
            },
            filters: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'order';
            this.$parent.title = this.__('Orders');
        },

        computed: {
            url() {
                return window.location.href.replace(window.location.search, '').replace(/\/$/, '');
            },
        },

        methods: {
            badgeClass(status) {
                switch (status) {
                    case 'completed':
                        return 'badge-success';
                    case 'failed':
                    case 'cancelled':
                        return 'badge-danger';
                    case 'on_hold':
                        return 'badge-light';
                    default:
                        return 'badge-warning';
                }
            },
        },
    }
</script>
