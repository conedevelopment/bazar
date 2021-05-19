<template>
    <card :title="__('Transactions')">
        <template #header>
            <create-transaction :order="order" :drivers="drivers"></create-transaction>
        </template>
        <div v-if="order.transactions.length" class="table-responsive">
            <table class="table table-hover has-filled-header mb-0">
                <thead>
                    <tr>
                        <th scope="col">
                            {{ __('Amount') }}
                            <span class="badge badge-light text-uppercase">{{ order.currency }}</span>
                        </th>
                        <th scope="col">{{ __('Driver') }}</th>
                        <th scope="col">{{ __('Completed at') }}</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <transaction
                        v-for="(transaction, index) in order.transactions"
                        :key="index"
                        :transaction="transaction"
                        @delete="remove(index)"
                    ></transaction>
                </tbody>
            </table>
        </div>
        <div v-else class="alert alert-info mb-0">
            {{ __('There are no transactions yet.') }}
        </div>
    </card>
</template>

<script>
    import Transaction from './Transaction';
    import CreateTransaction from './CreateTransaction';

    export default {
        components: {
            Transaction,
            CreateTransaction,
        },

        props: {
            order: {
                type: Object,
                required: true,
            },
            drivers: {
                type: Object,
                required: true,
            },
        },

        methods: {
            remove(index) {
                this.order.transactions.splice(index, 1);
            },
        },
    }
</script>
