<template>
    <div class="row mb-4">
        <div class="col-12 d-md-flex flex-wrap col-xl-9">
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
                        <option :value="undefined">{{ __('Any') }}</option>
                        <option v-for="(label, value) in filters" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3 d-md-flex justify-content-end">
            <div class="d-md-inline-flex align-items-center">
                <label for="filter-search" class="sr-only">{{ __('Search') }}</label>
                <input
                    id="filter-search"
                    type="text"
                    class="form-control form-control-sm"
                    v-model.lazy="$parent.query.search"
                    v-debounce="300"
                    :placeholder="__('Search')"
                    :readonly="$parent.busy"
                >
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        methods: {
            capitalize(value) {
                value = value.toString();

                return value.charAt(0).toUpperCase() + value.slice(1);
            },
        },
    }
</script>
