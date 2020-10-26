<script>
    export default {
        props: {
            order: {
                type: Object,
                reqired: true
            }
        },

        watch: {
            'model.type'(n, o) {
                if (n === 'payment') {
                    this.model.driver = 'manual';
                }
            }
        },

        data() {
            return {
                model: {
                    amount: null,
                    type: 'refund',
                    driver: 'manual'
                }
            };
        },

        computed: {
            action() {
                return `/bazar/orders/${this.order.id}/transactions`;
            },
            types() {
                return {
                    payment: this.__('Payment'),
                    refund: this.__('Refund')
                };
            },
            drivers() {
                const manual = { manual: this.__('Manual') };

                return this.model.type === 'refund' ? this.order.transactions.reduce((options, transaction) => {
                    return Object.assign(options, {
                        [transaction.driver]: transaction.driver_name
                    });
                }, manual) : manual;
            }
        },

        methods: {
            add(transaction) {
                this.order.transactions.push(transaction);
                this.$refs.refund.close();
                this.model = {
                    amount: null,
                    type: 'refund',
                    driver: 'manual'
                };
                this.$refs.form.reset();
            }
        }
    }
</script>

<template>
    <data-form json ref="form" :action="action" :model="model" @success="add">
        <template #default="form">
            <button type="button" class="btn btn-sm btn-primary" @click.prevent="$refs.refund.open">
                {{ __('Create Transaction') }}
            </button>
            <modal ref="refund" :title="__('Create Transaction')">
                <template #default>
                    <alert v-if="form.errors.any()" type="danger" closable>
                        {{ __('Something went wrong!') }}
                    </alert>
                    <form-select
                        v-model="form.fields.type"
                        name="type"
                        :label="__('Type')"
                        :options="types"
                    ></form-select>
                    <form-select
                        v-model="form.fields.driver"
                        name="driver"
                        :label="__('Driver')"
                        :options="drivers"
                    ></form-select>
                    <form-input
                        v-model="form.fields.amount"
                        name="amount"
                        type="number"
                        min="0"
                        step="0.01"
                        :label="__('Amount (:CURRENCY)', { currency: order.currency })"
                        :help="__('Leave it empty to apply the remaining amount automatically.')"
                    ></form-input>
                </template>
                <template #footer="modal">
                    <button type="submit" class="btn btn-primary" :disabled="form.busy">
                        {{ __('Create') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary" @click.prevent="modal.close" :disabled="form.busy">
                        {{ __('Cancel') }}
                    </button>
                </template>
            </modal>
        </template>
    </data-form>
</template>
