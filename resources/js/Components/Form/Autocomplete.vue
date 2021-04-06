<template>
    <div class="form-group position-relative">
        <label v-if="$attrs.label" :for="$attrs.name">{{ $attrs.label }}</label>
        <input
            class="form-control"
            type="text"
            autocomplete="off"
            :name="$attrs.name"
            :id="$attrs.name"
            :class="{ 'is-invalid': $attrs.invalid }"
            v-bind="$attrs"
            v-model.lazy="query.search"
            v-debounce="300"
            @focus="open"
            @keydown.up="highlightPrev"
            @keydown.down="highlightNext"
            @keydown.enter.prevent="commit"
        >
        <span v-if="$attrs.invalid" class="form-text text-danger">
            {{ $attrs.error }}
        </span>
        <div class="card position-absolute overflow-auto" style="max-height: 200px; width: 100%; z-index: 1000;">
            <div v-show="isOpen" class="card-content">
                <div class="list-group">
                    <div
                        class="list-group-item list-group-item-action"
                        v-for="(item, index) in response.data"
                        :key="index"
                        :ref="`option-${index}`"
                        :class="[index === active ? 'active' : '']"
                        @mousedown="select(index)"
                    >
                        <slot v-bind="item"></slot>
                    </div>
                    <div v-if="response.data.length === 0" class="list-group-item disabled" aria-disabled="true">
                        {{ __('No items found for the given keyword.') }}
                    </div>
                    <div v-else-if="errors.any()" class="list-group-item list-group-item-danger">
                        {{ errors.get('*') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { nextTick } from 'vue';
    import Closable from './../../Mixins/Closable';
    import Queryable from './../../Mixins/Queryable';

    export default {
        mixins: [Closable, Queryable],

        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
            multiple: {
                type: Boolean,
                default: false,
            },
        },

        emits: ['update:modelValue'],

        beforeMount() {
            Object.assign(this.query, {
                exclude: this.modelValue.map((item) => item.id),
            });
        },

        mounted() {
            this.$dispatcher.once('open', this.fetch);

            window.addEventListener('keyup', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });

            window.addEventListener('click', (event) => {
                if (this.isOpen && ! this.$el.contains(event.target)) {
                    this.close();
                }
            });
        },

        watch: {
            modelValue: {
                handler(newValue, oldValue) {
                    Object.assign(this.query, {
                        exclude: newValue.map((item) => item.id),
                    });
                },
                deep: true,
            },
        },

        data() {
            return {
                active: -1,
            };
        },

        methods: {
            commit() {
                this.close();

                let value = [];

                if (this.multiple) {
                    value = Array.from(this.modelValue);
                    value.push(this.response.data[this.active]);
                } else {
                    value = [this.response.data[this.active]];
                }

                this.$emit('update:modelValue', value);
            },
            select(index) {
                this.highlight(index);
                this.commit();
            },
            highlight(index) {
                this.open();
                this.active = index;

                if (this.$refs[`option-${index}`]) {
                    nextTick(() => {
                        this.$refs[`option-${index}`].scrollIntoView({ block: 'nearest' });
                    });
                }
            },
            highlightNext() {
                if (this.isOpen) {
                    this.highlight(
                        this.active + 1 >= this.response.data.length ? 0 : this.active + 1
                    );
                }
            },
            highlightPrev() {
                if (this.isOpen) {
                    this.highlight(
                        this.active === 0 ? this.response.data.length - 1 : this.active - 1
                    );
                }
            },
            clear() {
                this.$emit('update:modelValue', []);
            },
        },
    }
</script>
