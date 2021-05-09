<template>
    <table class="table table-sm is-headless mb-0 vertical-align-top">
        <tr>
            <th>{{ __('ID') }}:</th>
            <td>#{{ order.id }}</td>
        </tr>
        <tr>
            <th>{{ __('Date') }}:</th>
            <td>{{ createdAt }}</td>
        </tr>
        <tr>
            <th>{{ __('Customer') }}:</th>
            <td>{{ order.address.name }}</td>
        </tr>
        <tr>
            <th>{{ __('Tax') }}:</th>
            <td>{{ order.formatted_tax }}</td>
        </tr>
        <tr>
            <th>{{ __('Shipping') }}:</th>
            <td>
                {{ order.shipping.formatted_total }}
                ({{ order.shipping.driver_name }})
            </td>
        </tr>
        <tr>
            <th>{{ __('Discount') }}:</th>
            <td>{{ order.formatted_discount }}</td>
        </tr>
        <tr>
            <th>{{ __('Total') }}:</th>
            <td>{{ order.formatted_total }}</td>
        </tr>
        <tr>
            <th>{{ __('Billing Address') }}:</th>
            <td>
                <address>
                    <div>{{ order.address.name }}</div>
                    <div v-if="order.address.company">{{ order.address.company }}</div>
                    <div>{{ order.address.address }}</div>
                    <div v-if="order.address.address_second">{{ order.address.address_second }}</div>
                    <div>{{ order.address.postcode }}, {{ order.address.city }}, {{ order.address.state }}</div>
                    <div>{{ order.address.country_name }}</div>
                    <div>{{ order.address.phone }}</div>
                    <div>{{ order.address.email }}</div>
                </address>
            </td>
        </tr>
        <tr>
            <th>{{ __('Shipping Address') }}:</th>
            <td>
                <address>
                    <div>{{ order.shipping.address.name }}</div>
                    <div v-if="order.shipping.address.company">{{ order.shipping.address.company }}</div>
                    <div>{{ order.shipping.address.address }}</div>
                    <div v-if="order.shipping.address.address_second">{{ order.shipping.address.address_second }}</div>
                    <div>
                        {{ order.shipping.address.postcode }},
                        {{ order.shipping.address.city }},
                        {{ order.shipping.address.state }}
                    </div>
                    <div>{{ order.shipping.address.country_name }}</div>
                    <div>{{ order.shipping.address.phone }}</div>
                    <div>{{ order.shipping.address.email }}</div>
                </address>
            </td>
        </tr>
    </table>
</template>

<script>
    export default {
        props: {
            order: {
                type: Object,
                required: true,
            },
        },

        computed: {
            createdAt() {
                return this.$date(this.order.created_at).format('YYYY-MM-DD HH:mm');
            },
        },
    }
</script>
