<template>
    <div class="form-group">
        <label v-if="$attrs.label" :for="$attrs.name">{{ $attrs.label }}</label>
        <input
            class="form-control"
            v-bind="$attrs"
            :value="modelValue"
            :id="$attrs.name"
            :class="{ 'is-invalid': $attrs.invalid }"
            @input="update"
        >
        <span v-if="$attrs.invalid" class="form-text text-danger">
            {{ $attrs.error }}
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [String, Number],
                default: null,
            },
        },

        emits: ['update:modelValue'],

        methods: {
            update(event) {
                this.$emit('update:modelValue', event.target.value);
            },
        },
    }
</script>
