<template>
    <data-form class="row" method="PATCH" :action="$page.props.action" :data="passwords" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('Password')">
                <data-form-input
                    type="password"
                    name="current_password"
                    :label="__('Current Password')"
                    v-model="form.data.current_password"
                ></data-form-input>
                <data-form-input
                    type="password"
                    name="password"
                    :label="__('New Password')"
                    v-model="form.data.password"
                ></data-form-input>
                <data-form-input
                    type="password"
                    name="password_confirmation"
                    :label="__('New Password Confirmation')"
                    v-model="form.data.password_confirmation"
                ></data-form-input>
            </card>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
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
        mounted() {
            this.$parent.title = this.__('Password');
        },

        inheritAttrs: false,

        data() {
            return {
                passwords: {
                    current_password: null,
                    password: null,
                    password_confirmation: null,
                },
            };
        },
    }
</script>
