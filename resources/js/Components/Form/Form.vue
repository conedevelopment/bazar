<script>
    import Errors from './../../Support/Errors';

    export default {
        props: {
            action: {
                type: String,
                required: true
            },
            json: {
                type: Boolean,
                default: false
            },
            method: {
                type: String,
                default: function () {
                    return this.$options.propsData.model.id ? 'PATCH' : 'POST';
                }
            },
            model: {
                type: Object,
                required: true
            }
        },

        watch: {
            model: {
                handler(n, o) {
                    Object.assign(this.fields, n);
                },
                deep: true
            },
            fields: {
                handler(n, o) {
                    this.$emit('change', n);
                },
                deep: true
            }
        },

        provide() {
            return {
                form: this.form
            };
        },

        data() {
            return {
                busy: false,
                success: false,
                fields: this.model || {},
                errors: new Errors(this.$page.errors)
            };
        },

        computed: {
            form() {
                return this;
            },
            trashed() {
                return !! this.model.deleted_at;
            },
            config() {
                return {
                    data: this.fields,
                    method: this.method,
                    headers: this.json ? {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    } : {}
                };
            }
        },

        methods: {
            submit() {
                this.errors.clear();
                this.$emit('submit');

                if (this.json) {
                    this.submitJson();
                } else {
                    this.$inertia.visit(this.action, this.config);
                }
            },
            submitJson() {
                this.busy = true;
                this.success = false;
                this.$http(Object.assign({ url: this.action }, this.config)).then(response => {
                    this.success = true;
                    this.$emit('success', response.data);
                }).catch(error => {
                    this.success = false;
                    this.errors.set(error.response.data.errors || {});
                    this.$emit('fail', this.errors);
                }).finally(() => {
                    this.busy = false;
                });
            },
            reset() {
                this.busy = this.success = false;
                this.errors.clear();
                this.fields = this.model;
                this.$emit('reset');
            }
        }
    }
</script>

<template>
    <form class="form" @submit.prevent="submit" @reset.prevent="reset" @keydown.enter.prevent>
        <div v-if="! json" class="row">
            <div class="col-12 col-lg-7 col-xl-8 form__body">
                <slot v-bind="form"></slot>
            </div>
            <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                <div class="sticky-helper">
                    <slot name="aside" v-bind="form"></slot>
                    <card :title="__('Actions')">
                        <div class="form-group d-flex justify-content-between mb-0">
                            <inertia-link v-if="model.id" :href="action" method="DELETE" class="btn btn-outline-danger">
                                {{ trashed ? __('Delete Permanently') : __('Trash') }}
                            </inertia-link>
                            <inertia-link v-if="trashed" :href="`${action}/restore`" method="PATCH" class="btn btn-warning">
                                {{ __('Restore') }}
                            </inertia-link>
                            <button v-else type="submit" class="btn btn-primary">
                                {{ model.id ? __('Update') : __('Save') }}
                            </button>
                        </div>
                    </card>
                </div>
            </div>
        </div>
        <slot v-else v-bind="form"></slot>
    </form>
</template>
