<template>
    <div class="row mt-3">
        <div class="col-12 col-md-6 d-md-flex align-items-center mb-3 mb-md-0">
            <div class="form-group mb-0">
                <div class="input-group input-group-sm">
                    <select
                        id="per-page"
                        class="custom-select form-control"
                        v-model="$parent.query.per_page"
                        :disabled="$parent.processing"
                    >
                        <option :value="undefined">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label for="per-page" class="input-group-append mb-0">
                        <span class="input-group-text">
                            <span>{{ __('of :count items', { count: total }) }}</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 d-md-flex align-items-center justify-content-end">
            <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0">
                    <li
                        v-for="(link, index) in links"
                        class="page-item"
                        :key="index"
                        :class="{ 'active': link.active, 'disabled': ! link.url }"
                    >
                        <inertia-link v-if="link.url" class="page-link" :href="link.url" :disabled="link.active">
                            <span v-html="link.label"></span>
                            <span v-if="link.active" class="sr-only">(current)</span>
                        </inertia-link>
                        <button v-else type="button" class="page-link" disabled>
                            <span v-html="link.label"></span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
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
    }
</script>
