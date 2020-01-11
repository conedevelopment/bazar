<script>
    export default {
        props: {
            icon: {
                type: String,
                required: true
            },
            title: {
                type: String,
                default: null
            },
            description: {
                type: String,
                default: null
            }
        }
    }
</script>

<template>
    <svg class="icon" :class="`icon-${icon}`" aria-hidden="true" role="img" fill="currentColor">
        <title v-if="title">{{ title }}</title>
        <desc v-if="description">{{ description }}</desc>
        <use :href="`#icon-${icon}`" :xlink:href="`#icon-${icon}`"></use>
    </svg>
</template>
