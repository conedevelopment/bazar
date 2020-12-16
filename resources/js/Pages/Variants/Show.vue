<script>
    export default {
        data() {
            return {
                title: this.$page.variant.alias
            };
        },

        computed: {
            hasOptions() {
                return Object.keys(this.$page.variant.product.options).length;
            }
        },

        methods: {
            selection(options) {
                return options.reduce((stack, option) => {
                    return Object.assign(stack, { [option]: this.__(option) });
                }, { '*': this.__('Any') });
            }
        }
    }
</script>

<template>
    <data-form :action="$page.action" :model="$page.variant">
        <template #default="form">
            <card :title="__('Options')" class="mb-5">
                <div v-if="hasOptions">
                    <div class="row">
                        <div v-for="(options, name) in $page.variant.product.options" :key="name" class="col">
                            <form-select
                                v-model="form.fields.option[name]"
                                :name="`option.${name}`"
                                :label="__(name)"
                                :options="selection(options)"
                            ></form-select>
                        </div>
                    </div>
                    <div v-if="form.errors.has('option')" class="row">
                        <div class="col">
                            <span class="form-text text-danger">
                                {{ form.errors.get('option') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-else class="alert alert-info mb-0">
                    {{ __('No variable options are available for the product.') }}
                </div>
            </card>
            <card :title="__('Pricing')" class="mb-5">
                <div v-for="(symbol, currency) in $page.currencies" :key="currency" class="row">
                    <div class="col">
                        <form-input
                            v-model="form.fields.prices[currency].default"
                            type="number"
                            min="0"
                            step="0.1"
                            :name="`form.fields.prices.${currency}.default`"
                            :label="__('Price (:CURRENCY)', { currency })"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            v-model="form.fields.prices[currency].sale"
                            type="number"
                            min="0"
                            step="0.1"
                            :name="`form.fields.prices.${currency}.sale`"
                            :label="__('Sale Price (:CURRENCY)', { currency })"
                        ></form-input>
                    </div>
                </div>
            </card>
            <card :title="__('Inventory')">
                <template #header>
                    <form-checkbox name="inventory.virtual" :label="__('Virtual')" v-model="form.fields.inventory.virtual"></form-checkbox>
                </template>
                <form-input name="inventory.sku" :label="__('SKU')" v-model="form.fields.inventory.sku"></form-input>
                <form-input
                    name="inventory.quantity"
                    min="0"
                    type="number"
                    :label="__('Quantity')"
                    :help="__('Leave it empty for disabling quantity tracking.')"
                    v-model="form.fields.inventory.quantity"
                    v-show="! form.fields.inventory.virtual"
                ></form-input>
                <form-input
                    name="inventory.weight"
                    min="0"
                    type="number"
                    :label="__('Weight (:unit)', { unit: $page.config.weight_unit })"
                    v-model="form.fields.inventory.weight"
                    v-show="! form.fields.inventory.virtual"
                ></form-input>
                <div class="row align-items-end" v-show="! form.fields.inventory.virtual">
                    <div class="col">
                        <form-input
                            name="inventory.length"
                            min="0"
                            type="number"
                            :label="__('Length (:unit)', { unit: $page.config.dimension_unit })"
                            v-model="form.fields.inventory.length"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="inventory.width"
                            min="0"
                            type="number"
                            :label="__('Width (:unit)', { unit: $page.config.dimension_unit })"
                            v-model="form.fields.inventory.width"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="inventory.height"
                            min="0"
                            type="number"
                            :label="__('Height (:unit)', { unit: $page.config.dimension_unit })"
                            v-model="form.fields.inventory.height"
                        ></form-input>
                    </div>
                </div>
                <div class="form-group">
                    <form-checkbox name="inventory.virtual" :label="__('Downloadable')" v-model="form.fields.inventory.downloadable"></form-checkbox>
                </div>
                <form-downloads name="inventory.files" v-model="form.fields.inventory.files" v-show="form.fields.inventory.downloadable"></form-downloads>
            </card>
        </template>
        <template #aside="form">
            <card :title="__('General')" class="mb-5">
                <form-input name="alias" :label="__('Alias')" v-model="form.fields.alias"></form-input>
            </card>
            <card :title="__('Media')" class="mb-5">
                <form-media name="media" multiple v-model="form.fields.media"></form-media>
            </card>
        </template>
    </data-form>
</template>
