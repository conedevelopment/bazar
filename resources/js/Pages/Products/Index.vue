<template>
    <card :title="__('Products')">
        <template #header>
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Product') }}
            </inertia-link>
        </template>
        <data-table :response="response" :filters="filters">
            <data-table-column :label="__('Photo')" #default="item">
                <img
                    class="table-preview-image"
                    :src="item.media[0] ? item.media[0].urls.thumb : '/vendor/bazar/img/placeholder.svg'"
                    :alt="item.name"
                >
            </data-table-column>
            <data-table-column :label="__('Name')" sort="name" #default="item">
                <inertia-link :href="`${url}/${item.id}`">
                    {{ item.name }}
                </inertia-link>
            </data-table-column>
            <data-table-column :label="__('SKU')" sort="inventory->sku" #default="item">
                {{ item.inventory.sku }}
            </data-table-column>
            <data-table-column :label="__('Price')" #default="item">
                {{ item.formatted_price }}
            </data-table-column>
            <data-table-column :label="__('Created at')" sort="created_at" #default="item">
                {{ formatDate(item.created_at) }}
            </data-table-column>
        </data-table>
    </card>
</template>

<script>
    export default {
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
            this.$parent.title = this.__('Products');
        },

        computed: {
            url() {
                return window.location.href.replace(window.location.search, '').replace(/\/$/, '');
            },
        },

        methods: {
            formatDate(date) {
                return date.substr(0, 16).replace('T', ' ');
            },
        },
    }
</script>
