<script>
    export default {
        data() {
            return {
                title: this.__('Users')
            };
        },

        methods: {
            formatDate(date) {
                return date.substr(0, 16).replace('T', ' ');
            }
        }
    }
</script>

<template>
    <card :title="title">
        <template #header>
            <inertia-link :href="`${$inertia.page.url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create User') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" :filters="$page.filters" searchable>
            <data-column :label="__('Avatar')">
                <template #default="item">
                    <img class="table-preview-image" :src="item.avatar" :alt="item.name">
                </template>
            </data-column>
            <data-column :label="__('Name')" sort="name">
                <template #default="item">
                    <inertia-link :href="`${$inertia.page.url}/${item.id}`">
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
</template>
