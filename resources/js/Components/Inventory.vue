<template>

</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],
    }
</script>
