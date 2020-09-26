<script>
    import Filterable from '../../Mixins/Filterable';

    export default {
        mixins: [Filterable],

        computed: {
            url() {
                return window.location.pathname.replace(/\/$/, '');
            }
        },

        methods: {
            formatDate(date) {
                return date.substr(0, 16).replace('T', ' ');
            }
        }
    }
</script>

<template>
    <layout :title="__('Variations')">
        <card :title="__('Variations')">
            <template #header>
                <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                    {{ __('Create Variation') }}
                </inertia-link>
            </template>
            <data-table :response="$page.results" :filters="filters" searchable>
                <data-column :label="__('Photo')">
                    <template #default="item">
                        <img
                            class="table-preview-image"
                            :src="item.media[0] ? item.media[0].urls.thumb : '/vendor/bazar/img/placeholder.svg'"
                            alt=""
                        >
                    </template>
                </data-column>
                <data-column :label="__('Alias')" sort="alias">
                    <template #default="item">
                        <inertia-link :href="`${url}/${item.id}`">
                            {{ item.alias }}
                        </inertia-link>
                    </template>
                </data-column>
                <data-column :label="__('Price')">
                    <template #default="item">
                        {{ item.price ? item.formatted_price : item.product.formatted_price }}
                    </template>
                </data-column>
                <data-column :label="__('Option')">
                    <template #default="item">
                        <span v-for="(value, key) in item.option" :key="key" class="badge badge-primary mr-1">
                            {{ __(key) }}: {{ __(value) }}
                        </span>
                    </template>
                </data-column>
                <data-column :label="__('Created at')" sort="created_at">
                    <template #default="item">
                        {{ formatDate(item.created_at) }}
                    </template>
                </data-column>
            </data-table>
        </card>
    </layout>
</template>
