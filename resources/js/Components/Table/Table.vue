<script>
    import Row from './Row';
    import Heading from './Heading';
    import Filters from './Filters';
    import Pagination from './Pagination';
    import Queryable from './../../Mixins/Queryable';

    export default {
        components: {
            Row,
            Heading,
            Filters,
            Pagination
        },

        props: {
            response: {
                type: Object,
                required: true
            },
            searchable: {
                type: Boolean,
                default: false
            },
            filters: {
                type: Object,
                default: () => {}
            }
        },

        created() {
            if (window.location.search) {
                this.query = Object.assign(
                    {}, this.query, Object.fromEntries(new URLSearchParams(window.location.search))
                );
            }
        },

        mounted() {
            this.columns = (this.$slots.default || []).filter(column => {
                return column.componentInstance && column.componentOptions.tag === 'data-column';
            }).map(column => column.componentInstance);

            this.$watch('query', (n, o) => {
                this.$inertia.visit(this.url);
            }, { deep: true })
        },

        data() {
            return {
                columns: [],
                selection: [],
                query: this.buildQuery()
            };
        },

        computed: {
            items() {
                return this.response.data || [];
            },
            hasFilters() {
                return Object.keys(this.filters || {}).length > 0;
            },
            url() {
                let query = Object.assign({}, this.query);

                for (let key in query) {
                    if (query[key] === '' || query[key] === null || query[key] === undefined) {
                        delete query[key];
                    }
                }

                let url = new URL(window.location.href);
                url.search = new URLSearchParams(query);

                return url;
            }
        },

        methods: {
            buildQuery() {
                let query = {
                    'sort[by]': 'created_at',
                    'sort[order]': 'desc',
                    page: 1,
                    per_page: null,
                    search: null
                };

                return Object.keys(this.filters || {}).reduce((query, key) => {
                    return Object.assign(query, { [key]: null });
                }, query);
            }
        }
    }
</script>

<template>
    <div>
        <filters v-if="hasFilters || searchable"></filters>
        <div class="table-responsive">
            <table v-if="items.length > 0" class="table table-hover">
                <thead>
                    <heading></heading>
                </thead>
                <tbody>
                    <row v-for="(item, index) in items" :key="index" :item="item"></row>
                </tbody>
                <tfoot>
                    <heading></heading>
                </tfoot>
            </table>
            <div v-else class="alert alert-info mb-0">
                {{ __('No results found.') }}
            </div>
        </div>
        <pagination v-if="items.length > 0"></pagination>
        <div style="display: none;">
            <slot></slot>
        </div>
    </div>
</template>
