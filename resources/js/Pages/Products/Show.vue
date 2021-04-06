<template>
    <data-form class="row" ref="form" method="PATCH" :action="action" :data="product" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('General')" class="mb-5">
                <data-form-input
                    type="text"
                    name="name"
                    :label="__('Name')"
                    v-model="form.data.name"
                ></data-form-input>
                <data-form-input
                    type="text"
                    name="slug"
                    :label="__('Slug')"
                    v-model="form.data.slug"
                ></data-form-input>
                <data-form-input
                    handler="editor"
                    name="description"
                    :label="__('Description')"
                    v-model="form.data.description"
                ></data-form-input>
            </card>
            <card :title="__('Properties')" class="mb-5">
                <template #header>
                    <div class="form-group" style="max-width: 200px;">
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                class="form-control"
                                :placeholder="__('Property')"
                                v-model="property"
                                @keydown.enter="addProperty"
                            >
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" :disabled="! property" @click="addProperty">
                                    {{ __('Add') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <inertia-link :href="`/bazar/products/${product.id}/variants`" class="btn btn-primary btn-sm">
                        {{ __('Variants') }}
                    </inertia-link>
                </template>
                <div class="form-group" v-for="(properties, key) in form.data.properties" :key="key">
                    <div class="d-flex">
                        <label :for="`properties.${key}`">{{ __(key) }}</label>
                        <button type="button" class="ml-2 icon-btn icon-btn-danger" @click="removeProperty(key)">
                            <icon name="close"></icon>
                        </button>
                    </div>
                    <data-form-input
                        handler="tag"
                        :name="`properties.${key}`"
                        v-model="form.data.properties[key]"
                    ></data-form-input>
                </div>
                <div v-if="form.data.properties.length === 0" class="mb-0 alert alert-info">
                    {{ __('No properties.') }}
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
                    name="inventory.quantity"
                    min="0"
                    type="number"
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
                            name="inventory.length"
                            min="0"
                            type="number"
                            :label="__('Length (:unit)', { unit: config.dimension_unit })"
                            v-model="form.data.inventory.length"
                        ></data-form-input>
                    </div>
                    <div class="col">
                        <data-form-input
                            name="inventory.width"
                            min="0"
                            type="number"
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
                <files
                    v-model="form.data.inventory.files"
                    v-show="form.data.inventory.downloadable"
                ></files>
            </card>
        </div>

        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('Categories')" class="mb-5">
                    <div class="form-group is-checkbox-list">
                        <data-form-input
                            v-for="(category, index) in categories"
                            handler="checkbox"
                            name="categories"
                            :key="index"
                            :label="category.name"
                            :value="category"
                            v-model="form.data.categories"
                        ></data-form-input>
                        <span v-if="form.errors.has('categories')" class="form-text text-danger">
                            {{ form.errors.get('categories') }}
                        </span>
                    </div>
                </card>
                <card :title="__('Media')" class="mb-5">
                    <data-form-input handler="media" name="media" multiple v-model="form.data.media"></data-form-input>
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
            product: {
                type: Object,
                required: true,
            },
            currencies: {
                type: Object,
                required: true,
            },
            categories: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'product';
            this.$parent.title = this.product.name;
        },

        data() {
            return {
                property: null,
            };
        },

        computed: {
            config() {
                return window.Bazar.config;
            },
            action() {
                return `/bazar/products/${this.product.id}`;
            },
        },

        methods: {
            addProperty() {
                if (this.property && ! this.$refs.form.fields.properties.hasOwnProperty(this.property)) {
                    Object.assign(
                        this.$refs.form.fields,
                        { properties: Object.assign({}, this.$refs.form.fields.properties, { [this.property]: [] }) }
                    );

                    this.property = null;
                }
            },
            removeProperty(property) {
                let properties = Object.assign({}, this.$refs.form.fields.properties);

                properties = Object.keys(properties).reduce((items, key) => {
                    return key === property ? items : Object.assign(items, { [key]: properties[key] });
                }, {});

                Object.assign(this.$refs.form.fields, { properties });
            },
        },
    }
</script>
