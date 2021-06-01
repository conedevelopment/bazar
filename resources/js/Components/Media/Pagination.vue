<template>
    <div class="d-flex justify-content-end align-items-center">
        <div class="form-group mr-3">
            <div class="input-group input-group-sm">
                <select
                    id="media-pagination"
                    class="custom-select form-control"
                    v-model="$parent.query.per_page"
                    :disabled="$parent.busy"
                >
                    <option :value="undefined">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label for="media-pagination" class="input-group-append mb-0">
                    <span class="input-group-text">
                        <span>{{ __(':count items', { count: total }) }}</span>
                    </span>
                </label>
            </div>
        </div>
        <nav :aria-label="__('Pagination')">
            <ul class="pagination pagination-sm">
                <li
                    v-for="(link, index) in links"
                    class="page-item"
                    :key="index"
                    :class="{ 'active': link.active, 'disabled': ! link.url || $parent.processing }"
                >
                    <button
                        type="button"
                        class="page-link"
                        :disabled="link.active || $parent.processing"
                        @click="to(link.url)"
                    >
                        <span v-html="link.label"></span>
                        <span v-if="link.active" class="sr-only">(current)</span>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
        computed: {
            total() {
                return this.$parent.response.total || 0;
            },
            links() {
                return this.$parent.response.links;
            },
        },

        methods: {
            to(url) {
                let query = {};
                const params = new URL(url);

                for (const [key, value] of params.searchParams) {
                    query[key] = value;
                }

                Object.assign(this.$parent.query, query);
            },
        },
    }
</script>
