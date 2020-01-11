<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        props: {
            value: {
                type: [String, Number],
                default: null
            }
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
        <textarea
            class="form-control"
            v-bind="attrs"
            :value="value"
            :id="name"
            :name="name"
            :class="{ 'is-invalid': invalid }"
            @input="update"
        ></textarea>
        <span v-if="invalid" class="form-text text-danger">
            {{ error }}
        </span>
    </div>
</template>
