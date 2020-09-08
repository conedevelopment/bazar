export default {
    props: {
        column: {
            type: Object,
            required: true
        }
    },

    render(createElement) {
        return createElement('div', {}, this.column.$scopedSlots.default(this.$parent.item));
    }
}
