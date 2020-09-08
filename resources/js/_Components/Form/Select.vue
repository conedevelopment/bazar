<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        props: {
            value: {
                type: [Object, String, Number],
                default: null
            },
            options: {
                type: Object,
                required: true
            }
        },

        watch: {
            value(n, o) {
                if (n !== this.selected) {
                    this.selected = n;
                }
            }
        },

        data() {
            return {
                selected: this.value
            };
        },

        methods: {
            update(event) {
                this.$emit('input', event.target.value);
            }
        }
    }
</script>

<template>
    <div class="form-group">
        <label v-if="label" :for="name">{{ label }}</label>
        <select
            class="custom-select form-control"
            v-model="selected"
            v-bind="attrs"
            :id="name"
            :name="name"
            :class="{ 'is-invalid': invalid }"
            @change="update"
        >
            <option :value="null" disabled>--- {{ label }} ---</option>
            <option v-for="(label, option) in options" :value="option" :key="option">
                {{ label }}
            </option>
        </select>
        <span v-if="invalid" class="form-text text-danger">
            {{ error }}
        </span>
    </div>
</template>
