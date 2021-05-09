<template>
    <div>
        <teleport v-if="mounted" to=".app-header__actions">
            <inertia-link :href="`${url}/create`" class="btn btn-primary btn-sm">
                {{ __('Create Address') }}
            </inertia-link>
        </teleport>

        <data-table :response="response">
            <data-table-column :label="__('Alias')" sort="alias" #default="item">
                <inertia-link :href="`${url}/${item.id}`">
                    {{ item.alias }}
                </inertia-link>
            </data-table-column>
            <data-table-column :label="__('Name')" #default="item">
                {{ item.name }}
            </data-table-column>
            <data-table-column :label="__('Address')" #default="item">
                {{ item.country_name }}<br>
                {{ item.postcode }}, {{ item.city }}<br>
                {{ item.address }}
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
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'address';
            this.$parent.title = this.__('Addresses');
        },

        computed: {
            url() {
                return window.location.href.replace(window.location.search, '').replace(/\/$/, '');
            },
        },
    }
</script>
