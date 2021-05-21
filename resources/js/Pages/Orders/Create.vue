<template>
    <data-form class="row" ref="form" :action="action" :data="order" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('User')" class="mb-5">
                <data-form-input
                    handler="autocomplete"
                    name="user"
                    endpoint="/bazar/users"
                    placeholder="Jane Doe"
                    :modelValue="form.data.user ? [form.data.user] : []"
                    @update:modelValue="($event) => form.data.user = $event[0] || {}"
                    v-show="! form.data.user.id"
                    #default="user"
                >
                    <span>{{ user.name }}</span><br>
                    <small>{{ user.email }}</small>
                </data-form-input>
                <div v-if="form.data.user.id" class="form-group mt-0">
                    <label>{{ __('User') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" disabled :value="form.data.user.name">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" @click.prevent="clear">
                                {{ __('Remove') }}
                            </button>
                        </div>
                    </div>
                </div>
            </card>

            <card :title="__('Billing')" class="mb-5">
                <template #header>
                    <div class="form-group">
                        <select
                            class="custom-select custom-select-sm"
                            :disabled="! form.data.user.addresses.length"
                            @input.prevent="(event) => copy(event.target.value, 'billing')"
                        >
                            <option :value="null" selected>{{ __('Address') }}</option>
                            <option v-for="address in form.data.user.addresses" :key="address.id" :value="JSON.stringify(address)">
                                {{ address.alias }}
                            </option>
                        </select>
                    </div>
                </template>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="address.first_name"
                            :label="__('First Name')"
                            v-model="form.data.address.first_name"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="address.last_name"
                            :label="__('Last Name')"
                            v-model="form.data.address.last_name"
                        ></data-form-input>
                    </div>
                </div>
                <data-form-input
                    type="text"
                    name="address.company"
                    :label="__('Company')"
                    v-model="form.data.address.company"
                ></data-form-input>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="email"
                            name="address.email"
                            :label="__('Email')"
                            v-model="form.data.address.email"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="tel"
                            name="address.phone"
                            :label="__('Phone')"
                            v-model="form.data.address.phone"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            handler="select"
                            name="address.country"
                            :label="__('Country')"
                            :options="countries"
                            v-model="form.data.address.country"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            :label="__('State')"
                            name="address.state"
                            type="text"
                            v-model="form.data.address.state"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="address.city"
                            :label="__('City')"
                            v-model="form.data.address.city"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="address.postcode"
                            :label="__('Postcode')"
                            v-model="form.data.address.postcode"
                        ></data-form-input>
                    </div>
                </div>
                <data-form-input
                    type="text"
                    name="address.address"
                    :label="__('Address')"
                    v-model="form.data.address.address"
                ></data-form-input>
                <data-form-input
                    type="text"
                    name="address.secondary_address"
                    :label="__('Secondary Address')"
                    v-model="form.data.address.secondary_address"
                ></data-form-input>
            </card>

            <card :title="__('Shipping')" class="mb-5">
                <template #header>
                    <div class="form-group">
                        <select
                            class="custom-select custom-select-sm"
                            :disabled="! form.data.user.addresses.length"
                            @input.prevent="(event) => copy(event.target.value, 'shipping')"
                        >
                            <option :value="null" selected>{{ __('Address') }}</option>
                            <option v-for="address in form.data.user.addresses" :key="address.id" :value="JSON.stringify(address)">
                                {{ address.alias }}
                            </option>
                        </select>
                    </div>
                </template>
                <div class="row">
                    <div class="col">
                        <data-form-input
                            handler="select"
                            name="shipping.driver"
                            :label="__('Driver')"
                            :options="drivers"
                            v-model="form.data.shipping.driver"
                        ></data-form-input>
                    </div>
                    <div class="col">
                        <data-form-input
                            type="number"
                            name="shipping.cost"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            :label="__('Cost (:UNIT)', { unit: form.data.currency })"
                            v-model="form.data.shipping.cost"
                        ></data-form-input>
                    </div>
                    <div class="col">
                        <data-form-input
                            type="number"
                            name="shipping.tax"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            :label="__('Tax (:UNIT)', { unit: form.data.currency })"
                            v-model="form.data.shipping.tax"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="shipping.address.first_name"
                            :label="__('First Name')"
                            v-model="form.data.shipping.address.first_name"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="shipping.address.last_name"
                            :label="__('Last Name')"
                            v-model="form.data.shipping.address.last_name"
                        ></data-form-input>
                    </div>
                </div>
                <data-form-input
                    type="text"
                    name="shipping.address.company"
                    :label="__('Company')"
                    v-model="form.data.shipping.address.company"
                ></data-form-input>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="email"
                            name="shipping.address.email"
                            :label="__('Email')"
                            v-model="form.data.shipping.address.email"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="tel"
                            name="shipping.address.phone"
                            :label="__('Phone')"
                            v-model="form.data.shipping.address.phone"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            handler="select"
                            name="shipping.address.country"
                            :label="__('Country')"
                            :options="countries"
                            v-model="form.data.shipping.address.country"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            :label="__('State')"
                            name="shipping.address.state"
                            type="text"
                            v-model="form.data.shipping.address.state"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="shipping.address.city"
                            :label="__('City')"
                            v-model="form.data.shipping.address.city"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="shipping.address.postcode"
                            :label="__('Postcode')"
                            v-model="form.data.shipping.address.postcode"
                        ></data-form-input>
                    </div>
                </div>
                <data-form-input
                    type="text"
                    name="shipping.address.address"
                    :label="__('Address')"
                    v-model="form.data.shipping.address.address"
                ></data-form-input>
                <data-form-input
                    type="text"
                    name="shipping.address.secondary_address"
                    :label="__('Secondary Address')"
                    v-model="form.data.shipping.address.secondary_address"
                ></data-form-input>
            </card>
            <card :title="__('Products')">
                <products :currency="form.data.currency" v-model="form.data.items"></products>
            </card>
        </div>

        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('Settings')" class="mb-5">
                    <data-form-input
                        type="number"
                        name="discount"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                        :label="__('Discount')"
                        v-model="form.data.discount"
                    ></data-form-input>
                    <data-form-input
                        handler="select"
                        name="currency"
                        :label="__('Currency')"
                        :options="currencies"
                        v-model="form.data.currency"
                    ></data-form-input>
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
    import Products from './../../Components/Order/Products';

    export default {
        components: {
            Products,
        },

        props: {
            order: {
                type: Object,
                required: true,
            },
            countries: {
                type: Object,
                required: true,
            },
            drivers: {
                type: Object,
                required: true,
            },
            currencies: {
                type: Object,
                required: true,
            },
            statuses: {
                type: Object,
                required: true,
            },
        },

        mounted() {
            this.$parent.icon = 'order';
            this.$parent.title = this.__('Create Order');
        },

        computed: {
            action() {
                return '/bazar/orders';
            },
        },

        methods: {
            clear() {
                this.$refs.form.fields.user = { addresses: [] };
            },
            copy(address, to) {
                if (to === 'billing') {
                    Object.assign(this.$refs.form.fields.address, JSON.parse(address));
                } else if (to === 'shipping') {
                    Object.assign(this.$refs.form.fields.shipping.address, JSON.parse(address));
                }
            },
        },
    }
</script>
