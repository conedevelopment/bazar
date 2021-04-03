<template>
    <data-form class="row" :action="action" :data="address" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('General')">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="first_name"
                            :label="__('First Name')"
                            v-model="form.data.first_name"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="last_name"
                            :label="__('Last Name')"
                            v-model="form.data.last_name"
                        ></data-form-input>
                    </div>
                </div>
                <data-form-input
                    type="text"
                    name="company"
                    :label="__('Company')"
                    v-model="form.data.company"
                ></data-form-input>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="email"
                            name="email"
                            :label="__('Email')"
                            v-model="form.data.email"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="tel"
                            name="phone"
                            :label="__('Phone')"
                            v-model="form.data.phone"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            handler="select"
                            name="country"
                            :label="__('Country')"
                            :options="countries"
                            v-model="form.data.country"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="state"
                            :label="__('State')"
                            v-model="form.data.state"
                        ></data-form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="city"
                            :label="__('City')"
                            v-model="form.data.city"
                        ></data-form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <data-form-input
                            type="text"
                            name="postcode"
                            :label="__('Postcode')"
                            v-model="form.data.postcode"
                        ></data-form-input>
                    </div>
                </div>
                <data-form-input
                    type="text"
                    name="address"
                    :label="__('Address')"
                    v-model="form.data.address"
                ></data-form-input>
                <data-form-input
                    type="text"
                    name="address_secondary"
                    :label="__('Secondary Address')"
                    v-model="form.data.address_secondary"
                ></data-form-input>
            </card>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('Settings')" class="mb-5">
                    <data-form-input
                        type="text"
                        name="alias"
                        :label="__('Alias')"
                        v-model="form.data.alias"
                    ></data-form-input>
                    <data-form-input
                        handler="checkbox"
                        name="default"
                        :label="__('Default Address')"
                        v-model="form.data.default"
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
    export default {
        props: {
            address: {
                type: Object,
                required: true,
            },
            user: {
                type: Object,
                required: true,
            },
            countries: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.title = this.__('Create Address');
        },

        computed: {
            action() {
                return `/bazar/users/${this.user.id}/addresses`;
            },
        },
    }
</script>
