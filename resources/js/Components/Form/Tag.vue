<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        props: {
            value: {
                type: Array,
                default: () => []
            }
        },

        watch: {
            tags(n, o) {
                this.$emit('input', n);
            }
        },

        data() {
            return {
                tag: '',
                tags: this.value || []
            }
        },

        methods: {
            add() {
                if (this.tag && ! this.tags.includes(this.tag)) {
                    this.tags.push(this.tag);
                    this.tag = '';
                }
            },
            remove(index) {
                this.tags.splice(index, 1);
            },
            removeLast() {
                if (! this.tag) {
                    this.tags.pop();
                }
            }
        }
    }
</script>

<template>
    <div class="form-group">
        <div class="tag-control" :class="{ 'is-invalid': invalid }" @click.self="$refs.input.focus()">
            <span v-for="(tag, index) in tags" :key="index" class="tag is-small">
                <span class="tag__label">{{ tag }}</span>
                <button type="button" class="tag__remove" @click.prevent="remove(index)">
                    <icon name="close"></icon>
                </button>
            </span>
            <input
                ref="input"
                type="text"
                v-model="tag"
                v-bind="attrs"
                class="form-control-plaintext"
                style="width: 150px;"
                :placeholder="__('Add value')"
                :name="name"
                :id="name"
                @blur="add"
                @keydown.enter="add"
                @keydown.backspace="removeLast"
            >
        </div>
        <span v-if="help || invalid" class="form-text" :class="{ 'text-danger': invalid }">
            {{ error || help }}
        </span>
    </div>
</template>
