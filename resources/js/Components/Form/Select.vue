<template>
    <div class="form-group">
        <label v-if="$attrs.label" :for="$attrs.name">{{ $attrs.label }}</label>
        <select
            class="form-control custom-select"
            v-bind="$attrs"
            :modelValue="modelValue"
            :id="$attrs.name"
            :class="{ 'is-invalid': $attrs.invalid }"
            @change="update"
        >
            <option :value="null" disabled>--- {{ $attrs.label }} ---</option>
            <option v-for="(label, option) in options" :key="label" :value="option">
                {{ label }}
            </option>
        </select>
        <span v-if="$attrs.invalid" class="form-text text-danger">
            {{ $attrs.error }}
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Object, String, Number],
                default: null
            },
            options: {
                type: Object,
                required: true,
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
