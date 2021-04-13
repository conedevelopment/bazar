<template>
    <div>
        <teleport v-if="mounted" to=".app-header__actions">
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Category') }}
            </inertia-link>
        </teleport>

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
            <data-table-column :label="__('Created at')" sort="created_at" #default="item">
                {{ formatDate(item.created_at) }}
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
                require: true,
            },
            filters: {
                type: Object,
                require: true,
            },
        },

        mounted() {
            this.$parent.icon = 'category';
            this.$parent.title = this.__('Categories');
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
