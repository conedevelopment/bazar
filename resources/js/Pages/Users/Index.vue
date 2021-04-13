<template>
    <div>
        <teleport v-if="mounted" to=".app-header__actions">
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create User') }}
            </inertia-link>
        </teleport>

        <data-table :response="response" :filters="filters">
            <data-table-column :label="__('Avatar')" #default="item">
                <img class="table-preview-image" :src="item.avatar" :alt="item.name">
            </data-table-column>
            <data-table-column :label="__('Name')" sort="name" #default="item">
                <inertia-link :href="`${url}/${item.id}`">
                    {{ item.name }}
                </inertia-link>
            </data-table-column>
            <data-table-column :label="__('Email')" sort="email" #default="item">
                {{ item.email }}
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
                required: true,
            },
            filters: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'customer';
            this.$parent.title = this.__('Users');
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
