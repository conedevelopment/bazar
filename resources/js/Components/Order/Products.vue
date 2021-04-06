<template>
    <div>
        <data-form-input
            handler="autocomplete"
            name="products"
            endpoint="/bazar/products"
            placeholder="Hoodie"
            multiple
            :modelValue="products"
            @update:modelValue="update"
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
                    <tr v-for="(product, index) in products" :key="index">
                        <td>{{ product.name }}</td>
                        <td>
                            <data-form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                placeholder="0"
                                step="0.01"
                                min="0"
                                size="3"
                                :name="`products.${index}.price`"
                                v-model="product.item.price"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                placeholder="0"
                                step="0.01"
                                min="0"
                                size="3"
                                :name="`products.${index}.tax`"
                                v-model="product.item.tax"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                placeholder="1"
                                step="0.01"
                                min="0"
                                size="3"
                                :name="`products.${index}.quantity`"
                                v-model="product.item.quantity"
                            ></data-form-input>
                        </td>
                        <td>{{ total(product) }}</td>
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
                    this.$emit('update:modelValue', newValue);
                },
                deep: true,
            },
        },

        data() {
            return {
                products: Array.from(this.modelValue),
            };
        },

        methods: {
            price(product) {
                return product.prices[this.currency].default || 0;
            },
            total(product) {
                const total = (Number(product.item.price) + Number(product.item.tax || 0))
                    * Number(product.item.quantity || 1);

                return Number(total).toFixed(2);
            },
            remove(index) {
                this.products.splice(index, 1);
            },
            update(value) {
                this.products = value.map((product) => {
                    return Object.assign({
                        item: {
                            tax: 0,
                            quantity: 1,
                            price: this.price(product),
                        },
                    }, product);
                });
            },
        },
    }
</script>
