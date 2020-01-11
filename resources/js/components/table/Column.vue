<script>
    export default {
        props: {
            label: {
                type: String,
                required: true
            },
            sort: {
                type: String,
                default: null
            }
        }
    }
</script>

<template>
    <div v-if="false">
        <slot></slot>
    </div>
</template>
