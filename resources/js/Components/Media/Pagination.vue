<script>
    import Pagable from './../../Mixins/Pagable';

    export default {
        mixins: [Pagable]
    }
</script>

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
                    <option :value="null">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label for="media-pagination" class="input-group-append mb-0">
                    <span class="input-group-text">
                        <span>{{ __('of :count items', { count: total }) }}</span>
                    </span>
                </label>
            </div>
        </div>
        <nav :aria-label="__('Pagination')">
            <ul class="pagination pagination-sm">
                <li class="page-item" :class="{ 'disabled': ! hasPrev || $parent.busy }">
                    <button type="button" class="page-link" :disabled="! hasPrev || $parent.busy" @click.prevent="prev">
                        {{ __('Previous') }}
                    </button>
                </li>
                <li
                    v-for="page in pages"
                    class="page-item"
                    aria-current="page"
                    :key="page"
                    :class="{ 'active': isCurrent(page) }"
                >
                    <button type="button" class="page-link" :disabled="isCurrent(page) || $parent.busy" @click.prevent="to(page)">
                        {{ page }}
                        <span v-if="isCurrent(page)" class="sr-only">(current)</span>
                    </button>
                </li>
                <li class="page-item" :class="{ 'disabled': ! hasNext || $parent.busy }">
                    <button type="button" class="page-link" :disabled="! hasNext || $parent.busy" @click.prevent="next">
                        {{ __('Next') }}
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>
