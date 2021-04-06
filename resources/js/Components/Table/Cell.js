import { h } from 'vue';

export default {
    props: {
        column: {
            type: Object,
            required: true,
        },
    },

    functional: true,

    render() {
        return h('div', {}, this.column.$slots.default(this.$parent.item));
    },
}
