<script>
    export default {
        methods: {
            capitalize(value) {
                value = value.toString();

                return value.charAt(0).toUpperCase() + value.slice(1);
            }
        }
    }
</script>

<template>
    <div class="row mb-4">
        <div
            v-if="$parent.hasFilters"
            class="col-12 d-md-flex flex-wrap"
            :class="{ 'col-md-8 col-lg-9': $parent.searchable }"
        >
            <div v-for="(filters, key) in $parent.filters" :key="key" class="form-group mb-3 mr-3 mb-md-0">
                <div class="input-group input-group-sm">
                    <label :for="`filter-${key}`" class="input-group-prepend mb-0">
                        <span class="input-group-text">{{ __(capitalize(key)) }}</span>
                    </label>
                    <select
                        v-model="$parent.query[key]"
                        class="custom-select form-control"
                        :id="`filter-${key}`"
                        :disabled="$parent.busy"
                    >
                        <option :value="null">--------</option>
                        <option v-for="(label, value) in filters" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div
            v-if="$parent.searchable"
            class="col-12 d-md-flex justify-content-end"
            :class="{ 'col-md-4 col-lg-3': $parent.hasFilters }"
        >
            <div class="d-md-inline-flex align-items-center">
                <label for="filter-search" class="sr-only">{{ __('Search') }}</label>
                <input
                    id="filter-search"
                    type="text"
                    class="form-control form-control-sm"
                    v-model.lazy="$parent.query.search"
                    v-debounce="300"
                    :placeholder="__('Search...')"
                    :disabled="$parent.busy"
                >
            </div>
        </div>
    </div>
</template>
