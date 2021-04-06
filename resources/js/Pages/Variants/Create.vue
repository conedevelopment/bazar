<template>
    <data-form class="row" :action="action" :data="variant" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('Variation')" class="mb-5">
                <div v-if="hasProperties">
                    <div class="row">
                        <div v-for="(values, name) in variant.product.properties" :key="name" class="col">
                            <data-form-input
                                handler="select"
                                :name="`variation.${name}`"
                                :label="__(name)"
                                :options="selection(values)"
                                v-model="form.data.variation[name]"
                            ></data-form-input>
                        </div>
                    </div>
                    <div v-if="form.errors.has('variation')" class="row">
                        <div class="col">
                            <span class="form-text text-danger">
                                {{ form.errors.get('variation') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-else class="alert alert-info mb-0">
                    {{ __('No variable properties are available for the product.') }}
                </div>
            </card>
            <card :title="__('Pricing')" class="mb-5">
                <div v-for="(symbol, currency) in currencies" :key="currency" class="row">
                    <div class="col">
                        <data-form-input
                            type="number"
                            min="0"
                            step="0.1"
                            :name="`form.data.prices.${currency}.default`"
                            :label="__('Price (:CURRENCY)', { currency })"
                            v-model="form.data.prices[currency].default"
                        ></data-form-input>
                    </div>
                    <div class="col">
                        <data-form-input
                            type="number"
                            min="0"
                            step="0.1"
                            :name="`form.data.prices.${currency}.sale`"
                            :label="__('Sale Price (:CURRENCY)', { currency })"
                            v-model="form.data.prices[currency].sale"
                        ></data-form-input>
                    </div>
                </div>
            </card>
            <card :title="__('Inventory')">
                <template #header>
                    <data-form-input
                        handler="checkbox"
                        name="inventory.virtual"
                        :label="__('Virtual')"
                        v-model="form.data.inventory.virtual"
                    ></data-form-input>
                </template>
                <data-form-input
                    type="text"
                    name="inventory.sku"
                    :label="__('SKU')"
                    v-model="form.data.inventory.sku"
                ></data-form-input>
                <data-form-input
                    type="number"
                    name="inventory.quantity"
                    min="0"
                    :label="__('Quantity')"
                    :help="__('Leave it empty for disabling quantity tracking.')"
                    v-model="form.data.inventory.quantity"
                    v-show="! form.data.inventory.virtual"
                ></data-form-input>
                <data-form-input
                    name="inventory.weight"
                    min="0"
                    type="number"
                    :label="__('Weight (:unit)', { unit: config.weight_unit })"
                    v-model="form.data.inventory.weight"
                    v-show="! form.data.inventory.virtual"
                ></data-form-input>
                <div class="row align-items-end" v-show="! form.data.inventory.virtual">
                    <div class="col">
                        <data-form-input
                            type="number"
                            name="inventory.length"
                            min="0"
                            :label="__('Length (:unit)', { unit: config.dimension_unit })"
                            v-model="form.data.inventory.length"
                        ></data-form-input>
                    </div>
                    <div class="col">
                        <data-form-input
                            type="number"
                            name="inventory.width"
                            min="0"
                            :label="__('Width (:unit)', { unit: config.dimension_unit })"
                            v-model="form.data.inventory.width"
                        ></data-form-input>
                    </div>
                    <div class="col">
                        <data-form-input
                            name="inventory.height"
                            min="0"
                            type="number"
                            :label="__('Height (:unit)', { unit: config.dimension_unit })"
                            v-model="form.data.inventory.height"
                        ></data-form-input>
                    </div>
                </div>
                <div class="form-group">
                    <data-form-input
                        handler="checkbox"
                        name="inventory.virtual"
                        :label="__('Downloadable')"
                        v-model="form.data.inventory.downloadable"
                    ></data-form-input>
                </div>
                <files v-model="form.data.inventory.files" v-show="form.data.inventory.downloadable"></files>
            </card>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('General')" class="mb-5">
                    <data-form-input
                        type="text"
                        name="alias"
                        :label="__('Alias')"
                        v-model="form.data.alias"
                    ></data-form-input>
                </card>
                <card :title="__('Media')" class="mb-5">
                    <data-form-input
                        handler="media"
                        name="media"
                        multiple
                        v-model="form.data.media"
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
</template>

<script>
    import Files from './../../Components/Product/Files';

    export default {
        components: {
            Files,
        },

        props: {
            variant: {
                type: Object,
                required: true,
            },
            product: {
                type: Object,
                required: true,
            },
            currencies: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'product';
            this.$parent.title = this.__('Create Variant');
        },

        computed: {
            config() {
                return window.Bazar.config;
            },
            hasProperties() {
                return Object.keys(this.variant.product.properties).length;
            },
            action() {
                return `/bazar/products/${this.product.id}/variants`;
            },
        },

        methods: {
            selection(properties) {
                return properties.reduce((stack, property) => {
                    return Object.assign(stack, { [property]: this.__(property) });
                }, { '*': this.__('Any') });
            },
        },
    }
</script>
