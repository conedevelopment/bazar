<script>
    export default {
        data() {
            return {
                title: this.__('Password'),
                passwords: {
                    current_password: null,
                    password: null,
                    password_confirmation: null
                }
            };
        }
    }
</script>

<template>
    <data-form :action="$page.action" :model="passwords" custom method="PATCH">
        <template #default="form">
            <div class="row">
                <div class="col-12 col-lg-7 col-xl-8 form__body">
                    <card :title="__('Password')">
                        <form-input name="current_password" type="password" :label="__('Current Password')" v-model="form.fields.current_password"></form-input>
                        <form-input name="password" type="password" :label="__('New Password')" v-model="form.fields.password"></form-input>
                        <form-input name="password_confirmation" type="password" :label="__('New Password Confirmation')" v-model="form.fields.password_confirmation"></form-input>
                    </card>
                </div>
                <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                    <div class="sticky-helper">
                        <card :title="__('Actions')">
                            <div class="form-group d-flex justify-content-between mb-0">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </card>
                    </div>
                </div>
            </div>
        </template>
    </data-form>
</template>
