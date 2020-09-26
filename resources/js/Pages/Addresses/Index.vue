<script>
    export default {
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
    <layout :title="__('Addresses')">
        <card :title="__('Addresses')">
            <template #header>
                <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                    {{ __('Create Address') }}
                </inertia-link>
            </template>
            <data-table :response="$page.results" searchable>
                <data-column :label="__('Alias')" sort="alias">
                    <template #default="item">
                        <inertia-link :href="`${url}/${item.id}`">
                            {{ item.alias }}
                        </inertia-link>
                    </template>
                </data-column>
                <data-column :label="__('Name')">
                    <template #default="item">
                        {{ item.name }}
                    </template>
                </data-column>
                <data-column :label="__('Address')">
                    <template #default="item">
                        {{ item.country_name }}<br>
                        {{ item.postcode }}, {{ item.city }}<br>
                        {{ item.address }}
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
