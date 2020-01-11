export default {
    props: {
        name: {
            type: String,
            default: null
        },
        label: {
            type: String,
            default: null
        },
        help: {
            type: String,
            default: null
        }
    },

    inheritAttrs: false,

    inject: ['form'],

    mounted() {
        this.$on('input', () => {
            if (this.invalid) {
                this.form.errors.clear(this.name);
            }
        });
    },

    computed: {
        invalid() {
            return this.name && this.form.errors.has(this.name);
        },
        error() {
            return this.invalid ? this.form.errors.get(this.name) : null;
        },
        attrs() {
            return Object.keys(this.$attrs).filter(key => {
                return key !== 'model';
            }).reduce((attrs, key) => {
                return Object.assign(attrs, { [key]: this.$attrs[key] });
            }, {});
        }
    }
}
