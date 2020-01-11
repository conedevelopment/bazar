<script>
    import Field from './../../Mixins/Field';
    import Closable from './../../Mixins/Closable';
    import Queryable from './../../Mixins/Queryable';

    export default {
        mixins: [Closable, Field, Queryable],

        props: {
            value: {
                type: [Array, Object],
                default: () => {}
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },

        mounted() {
            this.$once('open', this.fetch);
        },

        watch: {
            selection: {
                handler(n, o) {
                    this.$emit('input', this.multiple ? n : (n[0] || null));

                    let exclude = n.map(item => item.id);

                    if (JSON.stringify(exclude) !== JSON.stringify(this.query.exclude)) {
                        this.query.exclude = exclude;
                    }
                },
                deep: true
            }
        },

        data() {
            return {
                active: -1,
                query: {
                    exclude: Array.isArray(this.value) ? this.value.map(item => item.id) : ([this.value ? this.value.id : null]),
                },
                selection: Array.isArray(this.value) ? this.value : (this.value ? [this.value] : []),
            };
        },

        methods: {
            commit() {
                this.close();
                this.multiple
                    ? this.selection.push(this.items[this.active])
                    : this.selection = [this.items[this.active]];
            },
            select(index) {
                this.highlight(index);
                this.commit();
            },
            highlight(index) {
                this.open();

                this.active = index;

                if (this.$refs.option) {
                    this.$nextTick(() => {
                        this.$refs.option[index].scrollIntoView({ block: 'nearest' });
                    });
                }
            },
            highlightNext(event) {
                if (this.isOpen) {
                    this.highlight(
                        this.active + 1 >= this.items.length ? 0 : this.active + 1
                    );
                }
            },
            highlightPrev(event) {
                if (this.isOpen) {
                    this.highlight(
                        this.active === 0 ? this.items.length - 1 : this.active - 1
                    );
                }
            },
            clear() {
                this.selection = [];
            }
        }
    }
</script>

<template>
    <div class="form-group position-relative">
        <label v-if="label" :for="name">{{ label }}</label>
        <input
            class="form-control"
            type="text"
            autocomplete="off"
            :name="name"
            :class="{ 'is-invalid': invalid }"
            v-bind="attrs"
            v-model.lazy="query.term"
            v-debounce="300"
            @focus="open"
            @keydown.up="highlightPrev"
            @keydown.down="highlightNext"
            @keydown.enter.prevent="commit"
        >
        <span v-if="help || invalid" class="form-text" :class="{ 'text-danger': invalid }">
            {{ error || help }}
        </span>
        <div class="card position-absolute overflow-auto" style="max-height: 200px; width: 100%; z-index: 1000;">
            <div v-show="isOpen" class="card-content">
                <div class="list-group">
                    <div
                        ref="option"
                        class="list-group-item list-group-item-action"
                        v-for="(item, index) in items"
                        :key="index"
                        :class="[index === active ? 'active' : '']"
                        @mousedown="select(index)"
                    >
                        <slot v-bind="item"></slot>
                    </div>
                    <div v-if="items.length === 0" class="list-group-item disabled" aria-disabled="true">
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
