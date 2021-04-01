<template>
    <div class="table-responsive">
        <table class="table table-hover has-filled-header mb-0">
            <thead>
                <tr>
                    <th scope="col">{{ __('Name') }}</th>
                    <th scope="col">
                        {{ __('Price') }}
                        <span class="badge badge-light text-uppercase">{{ order.currency }}</span>
                    </th>
                    <th scope="col">
                        {{ __('Tax') }}
                        <span class="badge badge-light text-uppercase">{{ order.currency }}</span>
                    </th>
                    <th scope="col">{{ __('Qty') }}</th>
                    <th scope="col">
                        {{ __('Total') }}
                        <span class="badge badge-light text-uppercase">{{ order.currency }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(product, index) in order.products" :key="index">
                    <td>{{ product.name }}</td>
                    <td>{{ product.item.price }}</td>
                    <td>{{ product.item.tax }}</td>
                    <td>{{ product.item.quantity }}</td>
                    <td>{{ total(product.item.total) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: {
            order: {
                type: Object,
                required: true,
            },
        },

        methods: {
            total(total) {
                return (new Number(total)).toFixed(2);
            },
        },
    }
</script>
