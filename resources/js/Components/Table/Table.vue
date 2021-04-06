<template>
    <div>
        <filters></filters>
        <div class="table-responsive">
            <table v-if="response.data.length > 0" class="table table-hover">
                <thead>
                    <heading></heading>
                </thead>
                <tbody>
                    <row v-for="(item, index) in response.data" :key="index" :item="item"></row>
                </tbody>
                <tfoot>
                    <heading></heading>
                </tfoot>
            </table>
            <div v-else class="alert alert-info mb-0">
                {{ __('No results found.') }}
            </div>
        </div>
        <pagination v-if="response.data.length > 0"></pagination>
        <div style="display: none;">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    import Row from './Row';
    import Filters from './Filters';
    import Heading from './Heading';
    import Pagination from './Pagination';

    export default {
        components: {
            Row,
            Filters,
            Heading,
            Pagination,
        },

        props: {
            response: {
                type: Object,
                required: true,
            },
            filters: {
                type: Object,
                default: () => {},
            },
        },

        remember: {
            data: ['query'],
            key: window.location.pathname,
        },

        created() {
            Object.assign(
                this.query,
                Object.fromEntries(new URLSearchParams(window.location.search))
            );
        },

        mounted() {
            this.$watch('query', (value, oldValue) => {
                const query = Object.fromEntries(
                    Object.entries(value).filter(([_, v]) => v)
                );

                this.$inertia.get(window.location.pathname, query, {
                    replace: true,
                    only: ['response'],
                    preserveState: true,
                    onStart: (event) => {
                        this.selection = [];
                        this.busy = true;
                    },
                    onFinish: (event) => {
                        this.busy = false;
                    },
                });
            }, { deep: true });
        },

        data() {
            return {
                columns: [],
                selection: [],
                busy: false,
                query: {
                    page: 1,
                    search: null,
                    per_page: null,
                    'sort[order]': 'desc',
                    'sort[by]': 'created_at',
                },
            };
        },
    }
</script>
