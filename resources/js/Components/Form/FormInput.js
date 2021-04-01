import { h } from 'vue';
import Input from './Input';
import Editor from './Editor';
import Select from './Select';
import Autocomplete from './Autocomplete';

export default {
    name: 'FormInput',

    props: {
        modelValue: {
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    inject: ['form'],

    render() {
        return h(this.getComponent(this.type), Object.assign({}, this.$attrs, {
            name: this.name,
            type: this.type,
            error: this.error,
            invalid: this.invalid,
            modelValue: this.modelValue,
            'onUpdate:modelValue': (event) => {
                this.update(event);
            },
        }), this.$slots.default);
    },

    computed: {
        invalid() {
            return this.form.errors.has(this.name);
        },
        error() {
            return this.invalid ? this.form.errors.get(this.name) : null;
        },
    },

    methods: {
        update(event) {
            this.$emit('update:modelValue', event);

            if (this.invalid) {
                this.form.errors.clear(this.name);
            }
        },
        getComponent(type) {
            switch (type) {
                case 'autocomplete':
                    return Autocomplete;
                case 'select':
                    return Select;
                case 'editor':
                    return Editor;
                default:
                    return Input;
            }
        },
    },
}
