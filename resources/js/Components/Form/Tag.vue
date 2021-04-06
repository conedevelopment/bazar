<template>
    <div class="form-group">
        <label v-if="$attrs.label" :for="$attrs.name">{{ $attrs.label }}</label>
        <div class="tag-control" :class="{ 'is-invalid': $attrs.invalid }" @click.self="$refs.input.focus">
            <span v-for="(item, index) in modelValue" :key="index" class="tag is-small">
                <span class="tag__label">{{ item }}</span>
                <button type="button" class="tag__remove" @click="remove(index)">
                    <icon name="close"></icon>
                </button>
            </span>
            <input
                ref="input"
                type="text"
                v-model="tag"
                v-bind="$attrs"
                class="form-control-plaintext"
                style="width: 150px;"
                :placeholder="__('Add value')"
                :name="$attrs.name"
                :id="$attrs.name"
                @blur="add"
                @keydown.enter="add"
                @keydown.backspace="removeLast"
            >
        </div>
        <span v-if="$attrs.invalid" class="form-text invalid">
            {{ $attrs.error }}
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['update:modelValue'],

        data() {
            return {
                tag: null,
            };
        },

        methods: {
            add() {
                if (this.tag && ! this.modelValue.includes(this.tag)) {
                    let value = Array.from(this.modelValue);
                    value.push(this.tag);
                    this.$emit('update:modelValue', value);
                    this.tag = null;
                }
            },
            remove(index) {
                let value = Array.from(this.modelValue);
                value.splice(index, 1);
                this.$emit('update:modelValue', value);
            },
            removeLast() {
                if (! this.tag) {
                    let value = Array.from(this.modelValue);
                    value.pop();
                    this.$emit('update:modelValue', value);
                }
            },
        },
    }
</script>
