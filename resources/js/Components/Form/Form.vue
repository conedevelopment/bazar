<script>
    import Errors from './../../Support/Errors';

    export default {
        props: {
            action: {
                type: String,
                required: true
            },
            ajax: {
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
            config() {
                return {
                    data: this.fields,
                    method: this.method
                };
            }
        },

        methods: {
            submit() {
                this.errors.clear();
                this.$emit('submit');

                if (this.ajax) {
                    this.submitAjax();
                } else {
                    this.$inertia.visit(this.action, this.config);
                }
            },
            submitAjax() {
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
        <slot v-bind="form"></slot>
    </form>
</template>
