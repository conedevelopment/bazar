<template>
    <section class="card">
        <div v-if="hasHeader" class="card__header">
            <h2 class="card__title">{{ title }}</h2>
            <slot name="header"></slot>
        </div>
        <div class="card__inner">
            <slot></slot>
        </div>
        <div v-if="hasFooter" class="card__footer">
            <slot name="footer"></slot>
        </div>
    </section>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: null,
            },
        },

        computed: {
            hasHeader() {
                return (!! this.title) || this.$slots.hasOwnProperty('header');
            },
            hasFooter() {
                return this.$slots.hasOwnProperty('footer');
            },
        },
    }
</script>
