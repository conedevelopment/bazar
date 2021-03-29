<script>
    export default {
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

<template>
    <data-form method="PATCH" :action="$page.props.action" :data="passwords" #default="form">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 form__body">
                <section class="card">
                    <div class="card__header">
                        <h2 class="card__title">{{ __('Passwords') }}</h2>
                    </div>
                    <div class="card__inner">
                        <div class="form-group">
                            <label for="current_password">{{ __('Current Password') }}</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.has('current_password') }"
                                v-model="form.data.current_password"
                            >
                            <span v-if="form.errors.has('current_password')" class="form-text text-danger">
                                {{ form.errors.get('current_password') }}
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="password">{{ __('New Password') }}</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.has('password') }"
                                v-model="form.data.password"
                            >
                            <span v-if="form.errors.has('password')" class="form-text text-danger">
                                {{ form.errors.get('password') }}
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">{{ __('New Password') }}</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
                                v-model="form.data.password_confirmation"
                            >
                            <span v-if="form.errors.has('password_confirmation')" class="form-text text-danger">
                                {{ form.errors.get('password_confirmation') }}
                            </span>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                <div class="sticky-helper">
                    <section class="card">
                        <div class="card__header">
                            <h2 class="card__title">{{ __('Actions') }}</h2>
                        </div>
                        <div class="card__inner">
                            <div class="form-group d-flex justify-content-between mb-0">
                                <button type="submit" class="btn btn-primary" :disabled="form.busy">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </data-form>
</template>
