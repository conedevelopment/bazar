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
            json() {
                return JSON.stringify(this.value);
            },
            isSwitch() {
                return ! Array.isArray(this.$attrs.model);
            },
            checked() {
                return this.isSwitch ? this.$attrs.model : this.$attrs.model.some(item => {
                    return JSON.stringify(item) === this.json;
                });
            }
        },

        methods: {
            update(event) {
                if (this.isSwitch) {
                    this.$emit('input', ! this.$attrs.model);
                } else if (! this.checked) {
                    this.$attrs.model.push(this.value);
                    this.$emit('input', this.$attrs.model);
                } else {
                    this.$attrs.model.splice(this.$attrs.model.findIndex(item => {
                        return JSON.stringify(item) === this.json;
                    }), 1);
                    this.$emit('input', this.$attrs.model);
                }
            }
        }
    }
</script>

<template>
    <div class="custom-control" :class="{ 'custom-checkbox': ! isSwitch, 'custom-switch': isSwitch }">
        <label class="mb-0">
            <input
                type="checkbox"
                class="custom-control-input"
                v-bind="attrs"
                :checked="checked"
                :value="value"
                :id="name"
                :name="name"
                :class="{ 'is-invalid': invalid }"
                @change="update"
            >
            <span v-if="label" class="custom-control-label font-weight-bold">{{ label }}</span>
        </label>
        <span v-if="help" class="form-text mt-0">{{ help }}</span>
    </div>
</template>
