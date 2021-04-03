<template>
    <data-form class="row" :action="action" :data="category" #default="form">
        <div class="col-12 col-lg-7 col-xl-8 form__body">
            <card :title="__('General')">
                <data-form-input
                    type="text"
                    name="name"
                    :label="__('Name')"
                    v-model="form.data.name"
                ></data-form-input>
                <data-form-input
                    type="text"
                    name="slug"
                    :label="__('Slug')"
                    v-model="form.data.slug"
                ></data-form-input>
                <data-form-input
                    handler="editor"
                    name="description"
                    :label="__('Description')"
                    v-model="form.data.description"
                ></data-form-input>
            </card>
        </div>
        <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
            <div class="sticky-helper">
                <card :title="__('Media')" class="mb-5">
                    <data-form-input
                        handler="media"
                        name="media"
                        v-model="form.data.media"
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
            category: {
                type: Object,
                required: true,
            },
        },

        mounted() {
            this.$parent.title = this.__('Create Category');
        },

        computed: {
            action() {
                return '/bazar/categories';
            },
        },
    }
</script>
