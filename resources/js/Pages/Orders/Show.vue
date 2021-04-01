<template>
    <div>
        <data-form class="row" :action="$page.props.action" :data="order" #default="form">
            <div class="col-12 col-lg-7 col-xl-8 form__body">
                <card :title="__('General')" class="mb-5">
                    <order-info :order="order"></order-info>
                </card>
                <card :title="__('Products')">
                    <div v-if="! order.products.length" class="alert alert-info  mb-0">
                        {{ __('There are no products yet.') }}
                    </div>
                    <products-table v-else :order="order"></products-table>
                </card>
            </div>

            <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                <div class="sticky-helper">
                    <card :title="__('Settings')" class="mb-5">
                        <data-form-input
                            type="select"
                            name="status"
                            :label="__('Status')"
                            :options="statuses"
                            v-model="form.data.status"
                        ></data-form-input>
                    </card>
                    <card :title="__('Actions')">
                        <div class="form-group d-flex justify-content-between mb-0">
                            <button type="submit" class="btn btn-primary" :disabled="form.busy">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </card>
                </div>
            </div>
        </data-form>
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 mt-5">
                <transactions :order="order"></transactions>
            </div>
        </div>
    </div>
</template>

<script>
    import OrderInfo from './../../Components/Order/OrderInfo';
    import Transactions from './../../Components/Order/Transactions';
    import ProductsTable from './../../Components/Order/ProductsTable';

    export default {
        components: {
            OrderInfo,
            Transactions,
            ProductsTable,
        },

        props: {
            order: {
                type: Object,
                required: true,
            },
            statuses: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                title: this.__('Order #:id', { id: this.order.id }),
            };
        },
    }
</script>
