<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            direction: {
                type: String,
                default: 'down'
            },
            align: {
                type: String,
                default: 'left'
            }
        }
    }
</script>

<template>
    <div :class="[`drop${direction}`, { 'more-actions': ! $slots.trigger }]">
        <div @click="toggle">
            <slot name="trigger">
                <button type="button" class="btn btn-link dropdown-ellipses px-0 py-0" @click.prevent>
                    <svg class="icon icon-more-vertical" aria-hidden="true" role="img" fill="currentColor">
                        <use href="#icon-more-vertical" xlink:href="#icon-more-vertical"></use>
                    </svg>
                </button>
            </slot>
        </div>
         <div class="dropdown-menu" :class="`dropdown-menu-${align}`" :style="{ display: isOpen ? 'block' : 'none' }">
            <slot></slot>
        </div>
    </div>
</template>
