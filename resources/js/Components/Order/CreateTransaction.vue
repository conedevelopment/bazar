<template>
    <div>
        <button type="button" class="btn btn-sm btn-primary" @click="$refs.refund.open">
            {{ __('Create Transaction') }}
        </button>
        <modal ref="refund" :title="__('Create Transaction')">
            <template #default>
                <alert v-if="errors.any()" type="danger" closable>
                    {{ __('Something went wrong!') }}
                </alert>
                <div class="form-group">
                    <label for="type">{{ __('Type') }}</label>
                    <select
                        class="form-control custom-select"
                        id="type"
                        name="type"
                        :class="{ 'is-invalid': errors.has('type') }"
                        v-model="transaction.type"
                        @change="errors.clear('type')"
                    >
                        <option :value="null" disabled>{{ __('Type') }}</option>
                        <option v-for="(label, type) in types" :key="type" :value="type">
                            {{ label }}
                        </option>
                    </select>
                    <span v-if="errors.has('type')" class="form-text text-danger">
                        {{ errors.get('type') }}
                    </span>
                </div>
                <div class="form-group">
                    <label for="driver">{{ __('Driver') }}</label>
                    <select
                        class="form-control custom-select"
                        id="driver"
                        name="driver"
                        :class="{ 'is-invalid': errors.has('driver') }"
                        v-model="transaction.driver"
                        @change="errors.clear('driver')"
                    >
                        <option :value="null" disabled>{{ __('Driver') }}</option>
                        <option v-for="(driver, label) in drivers" :key="driver" :value="driver">
                            {{ label }}
                        </option>
                    </select>
                    <span v-if="errors.has('driver')" class="form-text text-danger">
                        {{ errors.get('driver') }}
                    </span>
                </div>
                <div class="form-group">
                    <label for="amount">{{ __('Amount (:CURRENCY)', { currency: order.currency }) }}</label>
                    <input
                        class="form-control"
                        type="number"
                        min="0"
                        step="0.01"
                        id="amount"
                        name="amount"
                        v-model="transaction.amount"
                        :class="{ 'is-invalid': errors.has('amount') }"
                        @input="errors.clear('amount')"
                    >
                    <span v-if="errors.has('amount')" class="form-text text-danger">
                        {{ errors.get('amount') }}
                    </span>
                    <span v-else class="form-text">
                        {{ __('Leave it empty to apply the remaining amount automatically.') }}
                    </span>
                </div>
                <div v-if="transaction.type === 'payment'" class="form-group">
                    <label for="key">{{ __('Reference') }}</label>
                    <input
                        class="form-control"
                        type="text"
                        id="key"
                        name="key"
                        v-model="transaction.key"
                        :class="{ 'is-invalid': errors.has('key') }"
                        @input="errors.clear('key')"
                    >
                    <span v-if="errors.has('key')" class="form-text text-danger">
                        {{ errors.get('key') }}
                    </span>
                </div>
            </template>

            <template #footer>
                <button type="button" class="btn btn-primary" :disabled="busy" @click="submit">
                    {{ __('Create') }}
                </button>
                <button type="button" class="btn btn-outline-primary" :disabled="busy" @click="$refs.refund.close">
                    {{ __('Cancel') }}
                </button>
            </template>
        </modal>
    </div>
</template>

<script>
    import Errors from './../Form/Errors';

    export default {
        props: {
            order: {
                type: Object,
                reqired: true,
            },
            drivers: {
                type: Object,
                reqired: true,
            },
        },

        data() {
            return {
                busy: false,
                errors: new Errors(),
                transaction: {
                    key: null,
                    amount: null,
                    type: 'refund',
                    driver: null,
                },
            };
        },

        computed: {
            types() {
                return {
                    payment: this.__('Payment'),
                    refund: this.__('Refund'),
                };
            },
        },

        methods: {
            submit() {
                this.busy = true;
                this.$http.post(`/bazar/orders/${this.order.id}/transactions`, this.transaction).then((response) => {
                    this.add(response.data);
                }).catch((error) => {
                    this.errors.fill(error.response.data.errors);
                }).finally(() => {
                    this.busy = false;
                });
            },
            add(transaction) {
                this.order.transactions.push(transaction);
                this.$refs.refund.close();
                this.transaction = {
                    key: null,
                    amount: null,
                    type: 'refund',
                    driver: null,
                };
            },
        },
    }
</script>
