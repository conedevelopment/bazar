<template>
    <div class="custom-control" :class="{ 'custom-checkbox': ! isSwitch, 'custom-switch': isSwitch }">
        <label class="mb-0">
            <input
                type="checkbox"
                class="custom-control-input"
                v-bind="$attrs"
                :checked="checked"
                :id="$attrs.name"
                :name="$attrs.name"
                :class="{ 'is-invalid': $attrs.invalid }"
                @change="update"
            >
            <span v-if="$attrs.label" class="custom-control-label font-weight-bold">{{ $attrs.label }}</span>
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
            isSwitch() {
                return ! Array.isArray(this.modelValue);
            },
            checked() {
                const json = JSON.stringify(this.$attrs.value);

                return this.isSwitch ? this.modelValue : this.modelValue.some((value) => {
                    return JSON.stringify(value) === json;
                });
            },
        },

        methods: {
            update() {
                if (this.isSwitch) {
                    this.$emit('update:modelValue', ! this.modelValue);
                } else if (! this.checked) {
                    let value = Array.from(this.modelValue);

                    value.push(this.$attrs.value);

                    this.$emit('update:modelValue', value);
                } else {
                    const json = JSON.stringify(this.$attrs.value);
                    let value = Array.from(this.modelValue);

                    value.splice(this.modelValue.findIndex((value) => {
                        return JSON.stringify(value) === json;
                    }), 1);

                    this.$emit('update:modelValue', value);
                }
            },
        },
    }
</script>
