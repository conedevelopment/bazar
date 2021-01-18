export default {
    data() {
        return {
            isOpen: false
        };
    },

    methods: {
        open() {
            if (! this.isOpen) {
                this.isOpen = true;
                this.$emit('open');
            }
        },
        close() {
            if (this.isOpen) {
                this.isOpen = false;
                this.$emit('close');
            }
        },
        toggle() {
            this.isOpen ? this.close() : this.open();
        }
    }
}
