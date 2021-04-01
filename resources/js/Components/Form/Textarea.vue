<template>
    <div class="form-group">
        <label v-if="$attrs.label" :for="$attrs.name">{{ $attrs.label }}</label>
        <textarea
            class="form-control"
            v-bind="$attrs"
            :id="$attrs.name"
            :name="$attrs.name"
            :class="{ 'is-invalid': $attrs.invalid }"
            :value="modelValue"
            @input="update"
        ></textarea>
        <span v-if="invalid" class="form-text text-danger">
            {{ error }}
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Number,
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
