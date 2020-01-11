<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        props: {
            value: {
                type: [String, Number],
                default: null
            },
            type: {
                type: String,
                default: 'text'
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
        <input
            class="form-control"
            v-bind="attrs"
            :value="value"
            :type="type"
            :id="name"
            :name="name"
            :class="{ 'is-invalid': invalid }"
            @input="update"
        >
        <span v-if="help || invalid" class="form-text" :class="{ 'text-danger': invalid }">
            {{ error || help }}
        </span>
    </div>
</template>
