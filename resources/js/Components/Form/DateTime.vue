<template>
    <div class="form-group">
        <label v-if="$attrs.label" :for="$attrs.name">{{ $attrs.label }}</label>
        <div class="input-group">
            <input type="date" class="form-control" v-model="date" :id="$attrs.name" :name="$attrs.name" v-bind="$attrs">
            <input type="time" step="1" class="form-control" v-model="time" :disabled="$attrs.disabled">
        </div>
        <span v-if="$attrs.invalid" class="form-text text-danger">
            {{ $attrs.error }}
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: String,
                default: '',
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                value: new Date(this.modelValue),
            };
        },

        computed: {
            date: {
                set(value) {
                    value = value.split('-');

                    this.value.setFullYear(value[0]);
                    this.value.setMonth(value[1]);
                    this.value.setDate(value[2]);

                    this.$emit('update:modelValue', this.value.toISOString());
                },
                get() {
                    return this.modelValue ? [
                        this.value.getFullYear(),
                        this.value.getMonth().toString().padStart(2, 0),
                        this.value.getDate().toString().padStart(2, 0),
                    ].join('-') : null;
                },
            },
            time: {
                set(value) {
                    value = value.split(':');

                    this.value.setHours(value[0]);
                    this.value.setMinutes(value[1]);
                    this.value.setSeconds(value[2]);

                    this.$emit('update:modelValue', this.value.toISOString());
                },
                get() {
                    return this.modelValue ? [
                        this.value.getHours().toString().padStart(2, 0),
                        this.value.getMinutes().toString().padStart(2, 0),
                        this.value.getSeconds().toString().padStart(2, 0),
                    ].join(':') : null;
                },
            },
        },
    }
</script>
