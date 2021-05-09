<template>
    <div>
        <teleport v-if="mounted" to=".app-header__actions">
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Variant') }}
            </inertia-link>
        </teleport>

        <data-table :response="response" :filters="filters">
            <data-table-column :label="__('Photo')" #default="item">
                <img
                    class="table-preview-image"
                    :src="item.media[0] ? item.media[0].urls.thumb : '/vendor/bazar/img/placeholder.svg'"
                    alt=""
                >
            </data-table-column>
            <data-table-column :label="__('Alias')" sort="alias" #default="item">
                <inertia-link :href="`${url}/${item.id}`">
                    {{ item.alias }}
                </inertia-link>
            </data-table-column>
            <data-table-column :label="__('Price')" #default="item">
                {{ item.price ? item.formatted_price : item.product.formatted_price }}
            </data-table-column>
            <data-table-column :label="__('Variation')" #default="item">
                <span v-for="(value, key) in item.variation" :key="key" class="badge badge-primary mr-1">
                    {{ __(key) }}: {{ __(value) }}
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

        mounted() {
            this.$parent.icon = 'product';
            this.$parent.title = this.__('Variants');
        },

        computed: {
            url() {
                return window.location.href.replace(window.location.search, '').replace(/\/$/, '');
            },
        },
    }
</script>
