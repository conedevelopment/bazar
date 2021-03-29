<template>
    <form @submit.prevent="submit" @reset.prevent="reset" @keydown.enter.prevent>
        <slot v-bind="{ data: fields, errors, busy }"></slot>
    </form>
</template>

<script>
    import Errors from './Errors';

    export default {
        props: {
            action: {
                type: String,
                required: true,
            },
            method: {
                type: String,
                default: 'POST',
            },
            data: {
                type: Object,
                default: () => {},
            },
            preserveState: {
                type: Boolean,
                default: true,
            },
        },

        // remember: {
        //     data: ['fields'],
        //     key: window.location.pathname,
        // },

        data() {
            return {
                busy: false,
                errors: new Errors(this.$page.props.errors),
                fields: JSON.parse(JSON.stringify(this.data)),
            };
        },

        methods: {
            submit() {
                this.$inertia.visit(this.action, {
                    data: this.fields,
                    preserveState: this.preserveState,
                    method: this.method.toLowerCase(),
                    onStart: (event) => {
                        this.busy = true;
                    },
                    onFinish: (event) => {
                        this.errors.fill(this.$page.props.errors);
                        this.busy = false;
                    },
                });
            },
            reset() {
                this.errors.clear();
                this.busy = false;
                this.fields = JSON.parse(JSON.stringify(this.data));
            },
        },
    }
</script>
