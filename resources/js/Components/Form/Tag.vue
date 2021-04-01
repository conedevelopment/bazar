<template>
    <div class="form-group">
        <div class="tag-control" :class="{ 'is-invalid': invalid }" @click.self="$refs.input.focus()">
            <span v-for="(tag, index) in tags" :key="index" class="tag is-small">
                <span class="tag__label">{{ tag }}</span>
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

        data() {
            return {
                tag: null,
            };
        },

        methods: {
            add() {
                if (this.tag && ! this.modelValue.includes(this.tag)) {
                    let values = Array.from(this.modelValue);
                    values.push(this.tag);
                    this.$emit('update:modelValue', values);
                    this.tag = '';
                }
            },
            remove(index) {
                let values = Array.from(this.modelValue);
                values.splice(index, 1);
                this.$emit('update:modelValue', values);
            },
            removeLast() {
                if (! this.tag) {
                    let values = Array.from(this.modelValue);
                    values.pop();
                    this.$emit('update:modelValue', values);
                }
            },
        },
    }
</script>
