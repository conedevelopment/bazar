<template>
    <data-form class="row" method="PATCH" :action="action" :data="category" #default="form">
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
                        <inertia-link
                            as="button"
                            method="DELETE"
                            class="btn btn-outline-danger"
                            :href="action"
                            :disabled="form.busy"
                        >
                            {{ category.deleted_at ? __('Delete') : __('Trash') }}
                        </inertia-link>
                        <inertia-link
                            v-if="category.deleted_at"
                            as="button"
                            method="PATCH"
                            class="btn btn-warning"
                            :href="`${action}/restore`"
                            :disabled="form.busy"
                        >
                            {{ __('Restore') }}
                        </inertia-link>
                        <button v-else type="submit" class="btn btn-primary" :disabled="form.busy">
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
            this.$parent.icon = 'category';
            this.$parent.title = this.category.name;
        },

        computed: {
            action() {
                return `/bazar/categories/${this.category.id}`;
            },
        },
    }
</script>
