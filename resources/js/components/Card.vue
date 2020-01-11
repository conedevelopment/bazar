<script>
    export default {
        props: {
            title: {
                type: String,
                default: null
            }
        },

        computed: {
            hasHeader() {
                return (!! this.title) || this.$scopedSlots.hasOwnProperty('header');
            },
            hasFooter() {
                return this.$scopedSlots.hasOwnProperty('footer');
            }
        }
    }
</script>

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
            <slot name="header"></slot>
        </div>
    </section>
</template>
