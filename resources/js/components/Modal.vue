<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            title: {
                type: String,
                default: null
            }
        },

        watch: {
            isOpen(n, o) {
                if (n) {
                    this.$root.$el.classList.add('has-modal-open');
                } else {
                    this.$root.$el.classList.remove('has-modal-open');
                }
            }
        },

        data() {
            return {
                closeOnClick: false
            };
        },

        computed: {
            modal() {
                return this;
            }
        }
    }
</script>

<template>
    <div v-show="isOpen" class="modal" tabindex="-1" role="dialog" aria-hidden="true" @click.self="close">
        <div role="document" class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 v-if="title" class="modal-title">{{ title }}</h3>
                    <button type="button" class="close" :aria-label="__('Close')" @click.prevent="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body flex-column">
                    <slot v-bind="modal"></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer" v-bind="modal">
                        <button type="button" class="btn btn-outline-primary" @click.prevent="close">
                            {{ __('Close') }}
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>
