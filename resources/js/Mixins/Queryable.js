import Errors from '../Components/Form/Errors';

export default {
    props: {
        endpoint: {
            type: String,
            required: true,
        },
    },

    mounted() {
        this.$watch('query', (newValue, oldValue) => {
            this.fetch();
        }, { deep: true });
    },

    data() {
        return {
            busy: false,
            errors: new Errors(),
            response: { data: [] },
            query: {},
        };
    },

    methods: {
        fetch() {
            this.busy = true;
            this.errors.clear();

            this.$http.get(this.endpoint, { params: this.query }).then((response) => {
                this.response = response.data;
            }).catch((error) => {
                this.errors.fill(error.response.data.errors);
            }).finally(() => {
                this.busy = false;
            });
        },
    },
}
