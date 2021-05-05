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
        },
    },

    methods: {
        next() {
            this.to(this.$parent.query.page + 1);
        },
        prev() {
            this.to(this.$parent.query.page - 1);
        },
        to(page) {
            Object.assign(this.$parent.query, { page });
        },
        isCurrent(page) {
            return this.$parent.response.current_page === page;
        },
    },
}
