<script>
    import OrderInfo from './../../Components/Order/OrderInfo';
    import Transactions from './../../Components/Order/Transactions';
    import ProductsTable from './../../Components/Order/ProductsTable';

    export default {
        components: {
            OrderInfo,
            Transactions,
            ProductsTable
        }
    }
</script>

<template>
    <layout :title="`Order #${$page.order.id}`">
        <data-form :action="$page.action" :model="$page.order">
            <template #default>
                <card :title="__('General')" class="mb-5">
                    <order-info :order="$page.order"></order-info>
                </card>
                <card :title="__('Products')">
                    <div v-if="! $page.order.products.length" class="alert alert-info  mb-0">
                        {{ __('There are no products yet.') }}
                    </div>
                    <products-table v-else :order="$page.order"></products-table>
                </card>
            </template>
            <template #aside="form">
                <card :title="__('Settings')" class="mb-5">
                    <form-select name="status" :label="__('Status')" :options="$page.statuses" v-model="form.fields.status"></form-select>
                </card>
            </template>
        </data-form>
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 mt-5">
                <transactions :order="$page.order"></transactions>
            </div>
        </div>
    </layout>
</template>
