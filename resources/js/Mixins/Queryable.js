import Errors from './../Support/Errors';

export default {
    props: {
        endpoint: {
            type: String,
            required: true
        }
    },

    watch: {
        query: {
            handler(n, o) {
                this.fetch();
            },
            deep: true
        }
    },

    data() {
        return {
            busy: false,
            errors: new Errors,
            response: { data: [] },
            query: {
                'sort[by]': 'created_at',
                'sort[order]': 'desc',
                page: 1,
                per_page: null,
                search: null
            }
        };
    },

    computed: {
        items() {
            return this.response.data || [];
        },
        config() {
            return {
                method: 'GET',
                url: this.endpoint,
                params: this.query
            };
        }
    },

    methods: {
        fetch() {
            this.busy = true;
            this.errors.clear();

            this.$http(this.config).then(response => {
                this.response = response.data;
            }).catch(error => {
                this.errors.set(error.response.data.errors || {});
            }).finally(() => {
                this.busy = false;
            });
        }
    }
}
