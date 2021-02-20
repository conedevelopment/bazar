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
        },

        mounted() {
            window.addEventListener('keyup', event => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });

            window.addEventListener('click', event => {
                if (this.isOpen && ! this.$el.contains(event.target)) {
                    this.close();
                }
            });
        }
    }
</script>

<template>
    <div :class="[`drop${direction}`, { 'more-actions': ! $slots.trigger }]">
        <div @click="toggle">
            <slot name="trigger">
                <button type="button" class="btn btn-link dropdown-ellipses px-0 py-0" @click.prevent>
                    <icon name="more-vertical"></icon>
                </button>
            </slot>
        </div>
         <div class="dropdown-menu" :class="`dropdown-menu-${align}`" :style="{ display: isOpen ? 'block' : 'none' }">
            <slot></slot>
        </div>
    </div>
</template>
