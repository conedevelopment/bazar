<script>
    export default {
        inheritAttrs: false,
    }
</script>

<template>
    <data-form class="row" method="PATCH" :action="$page.props.action" :data="$parent.user" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('Profile')">
                <data-form-input
                    type="text"
                    name="name"
                    :label="__('Name')"
                    v-model="form.data.name"
                ></data-form-input>
                <data-form-input
                    type="email"
                    name="email"
                    :label="__('Email')"
                    v-model="form.data.email"
                ></data-form-input>
            </card>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('Actions')">
                    <div class="form-group d-flex justify-content-between mb-0">
                        <button type="submit" class="btn btn-primary" :disabled="form.busy">
                            {{ __('Update') }}
                        </button>
                    </div>
                </card>
            </div>
        </div>
    </data-form>
</template>
