import Errors from '../Components/Form/Errors';

export default {
    props: {
        endpoint: {
            type: String,
            required: true,
        },
    },

    watch: {
        query: {
            handler(value, oldValue) {
                this.fetch();
            },
            deep: true,
        },
    },

    data() {
        return {
            processing: false,
            errors: new Errors(),
            response: { data: [] },
            query: {
                'sort[by]': 'created_at',
                'sort[order]': 'desc',
                page: 1,
                per_page: null,
                search: null,
            },
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
                params: this.query,
            };
        },
    },

    methods: {
        fetch() {
            this.processing = true;
            this.errors.clear();

            this.$http(this.config).then((response) => {
                this.response = response.data;
            }).catch((error) => {
                this.errors.fill(error.response.data.errors);
            }).finally(() => {
                this.processing = false;
            });
        },
    },
}
