<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        props: {
            value: {
                type: [Object, String, Number, Boolean],
                default: null
            }
        },

        model: {
            prop: 'model',
            event: 'input'
        },

        computed: {
            checked() {
                return JSON.stringify(this.$attrs.model) === JSON.stringify(this.value);
            }
        },

        methods: {
            update(event) {
                this.$emit('input', this.value);
            }
        }
    }
</script>

<template>
    <div class="custom-control custom-radio mb-2">
        <label class="mb-0">
            <input
                type="radio"
                class="custom-control-input"
                v-bind="attrs"
                :checked="checked"
                :value="value"
                :id="name"
                :name="name"
                :class="{ 'is-invalid': invalid }"
                @change="update"
            >
            <span v-if="label" class="custom-control-label">{{ label }}</span>
        </label>
        <span v-if="help" class="form-text mt-0">{{ help }}</span>
    </div>
</template>
