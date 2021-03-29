export default {
    data() {
        return {
            isOpen: false,
        };
    },

    methods: {
        open() {
            if (! this.isOpen) {
                this.isOpen = true;
                this.$dispatcher.emit('open');
            }
        },
        close() {
            if (this.isOpen) {
                this.isOpen = false;
                this.$dispatcher.emit('close');
            }
        },
        toggle() {
            this.isOpen ? this.close() : this.open();
        },
    },
}
