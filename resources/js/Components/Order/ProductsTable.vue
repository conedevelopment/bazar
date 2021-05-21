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
                <tr v-for="(item, index) in order.items" :key="index">
                    <td>{{ item.product.name }}</td>
                    <td>{{ item.price }}</td>
                    <td>{{ item.tax }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ total(item.total) }}</td>
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
