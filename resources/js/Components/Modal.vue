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
                    <slot></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-outline-primary" @click.prevent="close">
                            {{ __('Close') }}
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            title: {
                type: String,
                default: null,
            },
        },

        mounted() {
            window.addEventListener('keyup', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });
        },

        watch: {
            isOpen(newValue, oldValue) {
                const className = this.$el.closest('.form__sidebar') ? 'sidebar' : 'body';

                if (newValue) {
                    this.$root.$el.classList.add(`has-modal-open--${className}`);
                } else {
                    this.$root.$el.classList.remove(`has-modal-open--${className}`);
                }
            },
        },
    }
</script>
