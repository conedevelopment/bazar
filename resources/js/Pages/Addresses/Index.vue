<script>
    export default {
        data() {
            return {
                title: this.__('Addresses')
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
                {{ __('Create Address') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" searchable>
            <data-column :label="__('Alias')" sort="alias">
                <template #default="item">
                    <inertia-link :href="`${$inertia.page.url}/${item.id}`">
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
</template>
