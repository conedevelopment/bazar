<script>
    export default {
        data() {
            return {
                title: this.__('Orders'),
            };
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
            formatDate(date) {
                return date.substr(0, 16).replace('T', ' ');
            }
        },

        computed: {
            url() {
                return window.location.href.replace(window.location.search, '').replace(/\/$/, '');
            }
        }
    }
</script>

<template>
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ title }}</h2>
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Order') }}
            </inertia-link>
        </div>
        <div class="card__inner">
            <data-table :response="$page.props.response" :filters="$page.props.filters">
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
                    {{ formatDate(item.created_at) }}
                </data-table-column>
            </data-table>
        </div>
    </section>
</template>
