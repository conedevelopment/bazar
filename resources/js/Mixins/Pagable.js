export default {
    computed: {
        hasNext() {
            return !! this.$parent.response.next_page_url;
        },
        hasPrev() {
            return !! this.$parent.response.prev_page_url;
        },
        pages() {
            return this.$parent.response.last_page || 0;
        },
        total() {
            return this.$parent.response.total || 0;
        }
    },

    methods: {
        next() {
            this.$parent.query.page++;
        },
        prev() {
            this.$parent.query.page--;
        },
        to(page) {
            this.$parent.query.page = page;
        },
        isCurrent(page) {
            return this.$parent.response.current_page === page;
        }
    }
}
