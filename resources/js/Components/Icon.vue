<script>
    export default {
        props: {
            name: {
                type: String,
                required: true
            }
        }
    }
</script>

<template>
    <svg class="icon" :class="`icon-${name}`" aria-hidden="true" role="img" fill="currentColor">
        <use :href="`#icon-${name}`" :xlink:href="`#icon-${name}`"></use>
    </svg>
</template>
