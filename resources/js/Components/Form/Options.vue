<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        inheritAttrs: false,

        props: {
            value: {
                type: [Array, Object],
                default: () => {}
            },
            name: {
                type: String,
                default: 'options'
            },
            schema: {
                type: [Array, Object],
                default: () => {}
            }
        },

        watch: {
            options: {
                handler(n, o) {
                    this.$emit('input', n);
                },
                deep: true
            }
        },

        data() {
            return {
                key: null,
                options: (! this.value || Array.isArray(this.value)) ? {} : this.value
            };
        },

        computed: {
            canAdd() {
                return !! this.key && ! this.options.hasOwnProperty(this.key);
            }
        },

        methods: {
            add() {
                if (this.canAdd) {
                    this.$set(this.options, this.key, this.schema);

                    this.reset();
                }
            },
            remove(key) {
                this.$delete(this.options, key);
            },
            reset() {
                this.key = null;
            }
        }
    }
</script>

<template>
    <div class="form-option-list">
        <div v-for="(option, key) in options" :key="key">
            <div class="tag mb-3">
                <label class="tag__label" :for="`${name}.${key}`">{{ __(key) }}</label>
                <button type="button" class="tag__remove" @click.prevent="remove(key)">
                    <svg aria-hidden="true" role="img" fill="currentColor" class="icon icon-close">
                        <use href="#icon-close" xlink:href="#icon-close"></use>
                    </svg>
                </button>
            </div>
            <slot v-bind="{ key, option }"></slot>
        </div>
        <div class="form-group">
            <label for="option-name">{{ __('Option') }}</label>
            <div class="input-group">
                <input
                    v-model="key"
                    type="text"
                    id="option-name"
                    class="form-control"
                    :class="{ 'is-invalid': invalid }"
                    @keydown.enter="add"
                >
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @click.prevent="add" :disabled="! canAdd">
                        {{ __('Add') }}
                    </button>
                </div>
            </div>
            <span v-if="help || invalid" class="form-text" :class="{ 'text-danger': invalid }">
                {{ error || help }}
            </span>
        </div>
    </div>
</template>
