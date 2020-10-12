@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Create Order'))

{{-- Content --}}
@section ('content')
    <data-form ref="form" action="{{ URL::route('bazar.orders.store') }}" :model="$page.order">
        <template #default="form">
            <card :title="__('User')" class="mb-5">
                <form-autocomplete
                    ref="user"
                    name="user"
                    endpoint="{{ URL::route('bazar.users.index') }}"
                    placeholder="Jane Doe"
                    :label="__('User')"
                    v-model="form.fields.user"
                    v-show="! form.fields.user.id"
                >
                    <template #default="item">
                        <span>@{{ item.name }}</span><br>
                        <small>@{{ item.email }}</small>
                    </template>
                </form-autocomplete>
                <div v-if="form.fields.user.id" class="form-group mt-0">
                    <label>{{ __('User') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" disabled :value="form.fields.user.name">
                        <div class="input-group-append">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click.prevent="form.fields.user = { addresses: [] }"
                            >
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
                            :disabled="! form.fields.user.addresses.length"
                            @input="event => Object.assign(form.fields.address, JSON.parse(event.target.value))"
                        >
                            <option :value="null" disabled selected>--- {{ __('Address') }} ---</option>
                            <option v-for="(address, index) in form.fields.user.addresses" :key="index" :value="JSON.stringify(address)">
                                @{{ address.alias }}
                            </option>
                        </select>
                    </div>
                </template>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="address.first_name" :label="__('First Name')" v-model="form.fields.address.first_name"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="address.last_name" :label="__('Last Name')" v-model="form.fields.address.last_name"></form-input>
                    </div>
                </div>
                <form-input name="address.company" :label="__('Company')" v-model="form.fields.address.company"></form-input>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="address.email" type="email" :label="__('Email')" v-model="form.fields.address.email"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="address.phone" type="tel" :label="__('Phone')" v-model="form.fields.address.phone"></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-select name="address.country" :label="__('Country')" :options="$page.countries" v-model="form.fields.address.country"></form-select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="address.state" :label="__('State')" v-model="form.fields.address.state"></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="address.city" :label="__('City')" v-model="form.fields.address.city"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="address.postcode" :label="__('Postcode')" v-model="form.fields.address.postcode"></form-input>
                    </div>
                </div>
                <form-input name="address.address" :label="__('Address')" v-model="form.fields.address.address"></form-input>
                <form-input name="address.address_secondary" :label="__('Secondary Address')" v-model="form.fields.address.address_secondary"></form-input>
            </card>
            <card :title="__('Shipping')" class="mb-5">
                <template #header>
                    <div class="form-group">
                        <select
                            class="custom-select custom-select-sm"
                            :disabled="! form.fields.user.addresses.length"
                            @input="event => Object.assign(form.fields.shipping.address, JSON.parse(event.target.value))"
                        >
                            <option :value="null" disabled selected>--- {{ __('Address') }} ---</option>
                            <option v-for="(address, index) in form.fields.user.addresses" :key="index" :value="JSON.stringify(address)">
                                @{{ address.alias }}
                            </option>
                        </select>
                    </div>
                </template>
                <div class="row">
                    <div class="col">
                        <form-select name="shipping.driver" :label="__('Driver')" :options="$page.drivers" v-model="form.fields.shipping.driver"></form-select>
                    </div>
                    <div class="col">
                        <form-input
                            name="shipping.cost"
                            type="number"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            :label="__('Cost (:UNIT)', { unit: form.fields.currency })"
                            v-model="form.fields.shipping.cost"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="shipping.tax"
                            type="number"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            :label="__('Tax (:UNIT)', { unit: form.fields.currency })"
                            v-model="form.fields.shipping.tax"
                        ></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.first_name" :label="__('First Name')" v-model="form.fields.shipping.address.first_name"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.last_name" :label="__('Last Name')" v-model="form.fields.shipping.address.last_name"></form-input>
                    </div>
                </div>
                <form-input name="shipping.address.company" :label="__('Company')" v-model="form.fields.shipping.address.company"></form-input>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.email" type="email" :label="__('Email')" v-model="form.fields.shipping.address.email"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.phone" type="tel" :label="__('Phone')" v-model="form.fields.shipping.address.phone"></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-select name="shipping.address.country" :label="__('Country')" :options="$page.countries" v-model="form.fields.shipping.address.country"></form-select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.state" :label="__('State')" v-model="form.fields.shipping.address.state"></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.city" :label="__('City')" v-model="form.fields.shipping.address.city"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="shipping.address.postcode" :label="__('Postcode')" v-model="form.fields.shipping.address.postcode"></form-input>
                    </div>
                </div>
                <form-input name="shipping.address.address" :label="__('Address')" v-model="form.fields.shipping.address.address"></form-input>
                <form-input name="shipping.address.address_secondary" :label="__('Secondary Address')" v-model="form.fields.shipping.address.address_secondary"></form-input>
            </card>
            <card :title="__('Products')">
                <form-autocomplete
                    name="products"
                    endpoint="/bazar/products"
                    multiple
                    :placeholder="__('Hoodie')"
                    v-model="form.fields.products"
                >
                    <template #default="item">
                        <span>@{{ item.name }}</span>
                    </template>
                </form-autocomplete>
                <order-products></order-products>
            </card>
        </template>
        <template #aside="form">
            <card :title="__('Settings')" class="mb-5">
                <form-input
                    name="discount"
                    type="number"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    :label="__('Discount')"
                    v-model="form.fields.discount"
                ></form-input>
                <form-select name="currency" :label="__('Currency')" :options="$page.currencies" v-model="form.fields.currency"></form-select>
                <form-select name="status" :label="__('Status')" :options="$page.statuses" v-model="form.fields.status"></form-select>
            </card>
        </template>
    </data-form>
@endsection
