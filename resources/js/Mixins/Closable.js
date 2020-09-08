export default {
    data() {
        return {
            isOpen: false,
            closeOnEsc: true,
            closeOnClick: true
        };
    },

    mounted() {
        window.addEventListener('keyup', event => {
            if (this.closeOnEsc && this.isOpen && event.keyCode === 27) {
                this.close();
            }
        });

        window.addEventListener('click', event => {
            if (this.closeOnClick && this.isOpen && ! this.$el.contains(event.target)) {
                this.close();
            }
        });
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
