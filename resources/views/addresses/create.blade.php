@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.users.addresses.store', $user) }}" :model="{{ $address }}">
        <template #default="form">
            <card :title="__('General')">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="first_name" :label="__('First Name')" v-model="form.fields.first_name"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="last_name" :label="__('Last Name')" v-model="form.fields.last_name"></form-input>
                    </div>
                </div>
                <form-input name="company" :label="__('Company')" v-model="form.fields.company"></form-input>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="phone" type="tel" :label="__('Phone')" v-model="form.fields.phone"></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-select name="country" :label="__('Country')" :options="{{ json_encode($countries) }}" v-model="form.fields.country"></form-select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="state" :label="__('State')" v-model="form.fields.state"></form-input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <form-input name="city" :label="__('City')" v-model="form.fields.city"></form-input>
                    </div>
                    <div class="col-12 col-sm-6">
                        <form-input name="postcode" :label="__('Postcode')" v-model="form.fields.postcode"></form-input>
                    </div>
                </div>
                <form-input name="address" :label="__('Address')" v-model="form.fields.address"></form-input>
                <form-input name="address_secondary" :label="__('Secondary Address')" v-model="form.fields.address_secondary"></form-input>
            </card>
        </template>
        <template #aside="form">
            <card :title="__('Settings')" class="mb-5">
                <form-input name="alias" :label="__('Alias')" v-model="form.fields.alias"></form-input>
                <form-checkbox name="default" :label="__('Default Address')" v-model="form.fields.default"></form-checkbox>
            </card>
        </template>
    </data-form>
@endsection
