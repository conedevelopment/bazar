<script>
    export default {
        inheritAttrs: false,
    }
</script>

<template>
    <data-form method="PATCH" :action="$page.props.action" :data="$parent.user" #default="form">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 form__body">
                <section class="card">
                    <div class="card__header">
                        <h2 class="card__title">{{ __('Profile') }}</h2>
                    </div>
                    <div class="card__inner">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.has('name') }"
                                v-model="form.data.name"
                            >
                            <span v-if="form.errors.has('name')" class="form-text text-danger">
                                {{ form.errors.get('name') }}
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="name">{{ __('Email') }}</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.has('email') }"
                                v-model="form.data.email"
                            >
                            <span v-if="form.errors.has('email')" class="form-text text-danger">
                                {{ form.errors.get('email') }}
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
