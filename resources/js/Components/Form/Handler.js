import { h } from 'vue';
import Tag from './Tag';
import Input from './Input';
import Media from './Media';
import Radio from './Radio';
import Editor from './Editor';
import Select from './Select';
import DateTime from './DateTime';
import Checkbox from './Checkbox';
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
        handler: {
            type: String,
            default: 'input',
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    inject: ['form'],

    render() {
        return h(this.getComponent(this.handler), Object.assign({}, this.$attrs, {
            name: this.name,
            error: this.error,
            invalid: this.invalid,
            modelValue: this.modelValue,
            'onUpdate:modelValue': (value) => {
                this.update(value);
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
        update(value) {
            this.$emit('update:modelValue', value);

            if (this.invalid) {
                this.form.errors.clear(this.name);
            }
        },
        getComponent(handler) {
            switch (handler) {
                case 'autocomplete':
                    return Autocomplete;
                case 'select':
                    return Select;
                case 'editor':
                    return Editor;
                case 'tag':
                    return Tag;
                case 'checkbox':
                    return Checkbox;
                case 'media':
                    return Media;
                case 'radio':
                    return Radio;
                case 'datetime':
                    return DateTime;
                default:
                    return Input;
            }
        },
    },
}
