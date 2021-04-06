<template>
    <div v-if="false">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
            label: {
                type: String,
                default: '',
            },
            sort: {
                type: String,
                default: null,
            },
        },

        mounted() {
            this.$parent.columns.push(this);
        },
    }
</script>
