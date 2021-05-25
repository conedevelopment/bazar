<template>
    <div>
        <data-form class="row" method="PATCH" :action="action" :data="order" #default="form">
            <div class="col-12 col-lg-7 col-xl-8 form__body">
                <card :title="__('General')" class="mb-5">
                    <order-info :order="order"></order-info>
                </card>
                <card :title="__('Items')">
                    <div v-if="! order.items.length" class="alert alert-info mb-0">
                        {{ __('There are no items.') }}
                    </div>
                    <items-table v-else :order="order"></items-table>
                </card>
            </div>

            <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                <div class="sticky-helper">
                    <card :title="__('Settings')" class="mb-5">
                        <data-form-input
                            handler="select"
                            name="status"
                            :label="__('Status')"
                            :options="statuses"
                            v-model="form.data.status"
                        ></data-form-input>
                    </card>
                    <card :title="__('Actions')">
                        <div class="form-group d-flex justify-content-between mb-0">
                            <inertia-link
                                as="button"
                                method="DELETE"
                                class="btn btn-outline-danger"
                                :href="action"
                                :disabled="form.busy"
                            >
                                {{ order.deleted_at ? __('Delete') : __('Trash') }}
                            </inertia-link>
                            <inertia-link
                                v-if="order.deleted_at"
                                as="button"
                                method="PATCH"
                                class="btn btn-warning"
                                :href="`${action}/restore`"
                                :disabled="form.busy"
                            >
                                {{ __('Restore') }}
                            </inertia-link>
                            <button v-else type="submit" class="btn btn-primary" :disabled="form.busy">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </card>
                </div>
            </div>
        </data-form>
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 mt-5">
                <transactions :order="order" :drivers="drivers"></transactions>
            </div>
        </div>
    </div>
</template>

<script>
    import OrderInfo from './../../Components/Order/OrderInfo';
    import ItemsTable from './../../Components/Order/ItemsTable';
    import Transactions from './../../Components/Order/Transactions';

    export default {
        components: {
            OrderInfo,
            ItemsTable,
            Transactions,
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
            drivers: {
                type: Object,
                required: true,
            },
        },

        mounted() {
            this.$parent.icon = 'order';
            this.$parent.title = this.__('Order #:id', { id: this.order.id });
        },

        computed: {
            action() {
                return `/bazar/orders/${this.order.id}`;
            },
        },
    }
</script>
