<template>
    <data-form class="row" method="PATCH" :action="action" :data="user" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('General')">
                <data-form-input
                    type="text"
                    name="name"
                    :label="__('Name')"
                    v-model="form.data.name"
                ></data-form-input>
                <data-form-input
                    name="email"
                    type="email"
                    :label="__('Email')"
                    v-model="form.data.email"
                ></data-form-input>
            </card>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('Addresses')" class="mb-5">
                    <inertia-link :href="`/bazar/users/${user.id}/addresses`" class="btn btn-primary">
                        {{ __('Manage Addresses') }}
                    </inertia-link>
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
            user: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'customer';
            this.$parent.title = this.user.name;
        },

        computed: {
            action() {
                return `/bazar/users/${this.user.id}`;
            },
        },
    }
</script>
