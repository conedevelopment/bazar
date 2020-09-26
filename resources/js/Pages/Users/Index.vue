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
    <layout :title="__('Users')">
        <card :title="__('Users')">
            <template #header>
                <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                    {{ __('Create User') }}
                </inertia-link>
            </template>
            <data-table :response="$page.results" :filters="filters" searchable>
                <data-column :label="__('Avatar')">
                    <template #default="item">
                        <img class="table-preview-image" :src="item.avatar" :alt="item.name">
                    </template>
                </data-column>
                <data-column :label="__('Name')" sort="name">
                    <template #default="item">
                        <inertia-link :href="`/bazar/users/${item.id}`">
                            {{ item.name }}
                        </inertia-link>
                    </template>
                </data-column>
                <data-column :label="__('Email')" sort="email">
                    <template #default="item">
                        {{ item.email }}
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
