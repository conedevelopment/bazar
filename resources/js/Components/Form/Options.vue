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
        <div v-for="(option, key) in options" :key="key" class="form-group">
            <div class="d-flex">
                <label :for="`${name}.${key}`" class="mr-3">{{ __(key) }}</label>
                <button type="button" class="icon-btn icon-btn-danger" :aria-label="__('Remove')" @click.prevent="remove(key)">
                    <icon name="close"></icon>
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
