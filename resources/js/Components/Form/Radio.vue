<script>
    export default {
        props: {
            modelValue: {
                type: [Object, String, Number, Boolean],
                default: null
            },
        },

        computed: {
            checked() {
                return JSON.stringify(this.$attrs.value) === JSON.stringify(this.modelValue);
            },
        },

        methods: {
            update() {
                this.$emit('input', this.value);
            },
        },
    }
</script>

<template>
    <div class="custom-control custom-radio">
        <label class="mb-0">
            <input
                type="radio"
                class="custom-control-input"
                v-bind="attrs"
                :checked="checked"
                :value="modelValue"
                :id="name"
                :name="name"
                :class="{ 'is-invalid': invalid }"
                @change="update"
            >
            <span v-if="label" class="custom-control-label">{{ label }}</span>
        </label>
    </div>
</template>
