<script>
    export default {
        data() {
            return {
                title: this.__('Products')
            };
        },

        methods: {
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
    <card :title="title">
        <template #header>
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Product') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" :filters="$page.filters" searchable>
            <data-table-column :label="__('Photo')">
                <template #default="item">
                    <img
                        class="table-preview-image"
                        :src="item.media[0] ? item.media[0].urls.thumb : '/vendor/bazar/img/placeholder.svg'"
                        :alt="item.name"
                    >
                </template>
            </data-table-column>
            <data-table-column :label="__('Name')" sort="name">
                <template #default="item">
                    <inertia-link :href="`${url}/${item.id}`">
                        {{ item.name }}
                    </inertia-link>
                </template>
            </data-table-column>
            <data-table-column :label="__('SKU')" sort="inventory->sku">
                <template #default="item">
                    {{ item.inventory.sku }}
                </template>
            </data-table-column>
            <data-table-column :label="__('Price')">
                <template #default="item">
                    {{ item.formatted_price }}
                </template>
            </data-table-column>
            <data-table-column :label="__('Created at')" sort="created_at">
                <template #default="item">
                    {{ formatDate(item.created_at) }}
                </template>
            </data-table-column>
        </data-table>
    </card>
</template>
