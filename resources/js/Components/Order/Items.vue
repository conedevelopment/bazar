<template>
    <card :title="__('Items')">
        <template #header>
            <button type="button" class="btn btn-primary btn-sm" @click="add">
                {{ __('Add Item') }}
            </button>
        </template>
        <div class="table-responsive">
            <table v-if="modelValue.length" class="table table-hover has-filled-header mb-0">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Price') }}</th>
                        <th scope="col">{{ __('Tax') }}</th>
                        <th scope="col">{{ __('Qty') }}</th>
                        <th scope="col">
                            {{ __('Total') }}
                            <span class="badge badge-light text-uppercase">{{ currency }}</span>
                        </th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in modelValue" :key="index">
                        <td style="max-width: 160px;">
                            <data-form-input
                                class="mb-0 form-group-sm"
                                :name="`items.${index}.name`"
                                v-model="item.name"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                placeholder="0"
                                step="0.1"
                                min="0"
                                size="3"
                                :name="`items.${index}.price`"
                                v-model="item.price"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                placeholder="0"
                                step="0.1"
                                min="0"
                                size="3"
                                :name="`items.${index}.tax`"
                                v-model="item.tax"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                placeholder="1"
                                step="0.1"
                                min="0"
                                size="3"
                                :name="`items.${index}.quantity`"
                                v-model="item.quantity"
                            ></data-form-input>
                        </td>
                        <td>{{ total(item) }}</td>
                        <td>
                            <button
                                type="button"
                                class="icon-btn icon-btn-danger"
                                :aria-label="__('Remove')"
                                @click="remove(index)"
                            >
                                <icon name="close"></icon>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="alert alert-info mb-0">
                {{ __('Add an item to the order.') }}
            </div>
        </div>
    </card>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
            currency: {
                type: String,
                required: true,
            },
        },

        emits: ['update:modelValue'],

        methods: {
            total(item) {
                const total = (Number(item.price) + Number(item.tax || 0)) * Number(item.quantity || 1);

                return Number(total).toFixed(2);
            },
            add() {
                this.modelValue.push({
                    tax: 0,
                    name: null,
                    price: null,
                    quantity: 1,
                });

                this.$emit('update:modelValue', this.modelValue);
            },
            remove(index) {
                this.modelValue.splice(index, 1);

                this.$emit('update:modelValue', this.modelValue);
            },
        },
    }
</script>
