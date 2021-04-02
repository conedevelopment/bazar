<template>
    <div class="custom-control custom-radio">
        <label class="mb-0">
            <input
                type="radio"
                class="custom-control-input"
                v-bind="$attrs"
                :checked="checked"
                :value="$attrs.value"
                :id="$attrs.name"
                :name="$attrs.name"
                :class="{ 'is-invalid': $attrs.invalid }"
                @change="update"
            >
            <span v-if="$attrs.label" class="custom-control-label">{{ $attrs.label }}</span>
        </label>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Object, String, Number, Boolean],
                default: null,
            },
        },

        emits: ['update:modelValue'],

        computed: {
            checked() {
                return JSON.stringify(this.$attrs.value) === JSON.stringify(this.modelValue);
            },
        },

        methods: {
            update() {
                this.$emit('update:modelValue', this.$attrs.value);
            },
        },
    }
</script>
