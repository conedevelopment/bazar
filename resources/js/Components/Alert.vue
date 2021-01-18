<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            type: {
                type: String,
                default: 'primary'
            },
            closable: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<template>
    <div v-show="isOpen" class="alert mb-5" :class="[`alert-${type}`, { 'alert-dismissible': closable }]" role="alert">
        <slot></slot>
        <button v-if="closable" type="button" class="close" :aria-label="__('Close')" @click.prevent="close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>
