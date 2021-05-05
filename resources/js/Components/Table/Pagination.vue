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
                    <li class="page-item" :class="{ 'disabled': ! hasPrev }">
                        <button type="button" class="page-link" :disabled="! hasPrev" @click="prev">
                            {{ __('Previous') }}
                        </button>
                    </li>
                    <li v-for="page in pages" class="page-item" :key="page" :class="{ 'active': isCurrent(page) }">
                        <button type="button" class="page-link" :disabled="isCurrent(page)" @click="to(page)">
                            {{ page }}
                            <span v-if="isCurrent(page)" class="sr-only">(current)</span>
                        </button>
                    </li>
                    <li class="page-item" :class="{ 'disabled': ! hasNext }">
                        <button type="button" class="page-link" :disabled="! hasNext" @click="next">
                            {{ __('Next') }}
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
    import Pagable from './../../Mixins/Pagable';

    export default {
        mixins: [Pagable],
    }
</script>
