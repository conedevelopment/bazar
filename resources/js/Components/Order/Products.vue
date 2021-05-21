<template>
    <div>
        <data-form-input
            handler="autocomplete"
            name="products"
            endpoint="/bazar/products"
            placeholder="Hoodie"
            multiple
            v-model="products"
            #default="item"
        >
            <span>{{ item.name }}</span>
        </data-form-input>
        <div class="table-responsive">
            <table v-show="products.length" class="table table-hover has-filled-header mb-0 mt-3">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">
                            {{ __('Price') }}
                            <span class="badge badge-light text-uppercase">{{ currency }}</span>
                        </th>
                        <th scope="col">
                            {{ __('Tax') }}
                            <span class="badge badge-light text-uppercase">{{ currency }}</span>
                        </th>
                        <th scope="col">{{ __('Qty') }}</th>
                        <th scope="col">{{ __('Total') }}
                            <span class="badge badge-light text-uppercase">{{ currency }}</span>
                        </th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in modelValue" :key="index">
                        <td>{{ products[index].name }}</td>
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
        </div>
    </div>
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

        watch: {
            products: {
                handler(newValue, oldValue) {
                    const items = newValue.map((product) => {
                        return {
                            tax: 0,
                            quantity: 1,
                            product_id: product.id,
                            price: this.price(product),
                        };
                    });

                    this.$emit('update:modelValue', items);
                },
                deep: true,
            },
        },

        data() {
            return {
                products: [],
            };
        },

        methods: {
            price(product) {
                return product.prices[this.currency].default || 0;
            },
            total(item) {
                const total = (Number(item.price) + Number(item.tax || 0)) * Number(item.quantity || 1);

                return Number(total).toFixed(2);
            },
            remove(index) {
                this.products.splice(index, 1);
            },
        },
    }
</script>
