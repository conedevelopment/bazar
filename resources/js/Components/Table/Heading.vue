<template>
    <tr>
        <th scope="col" style="width: 80px;">
            <div class="d-flex">
                <label class="custom-control custom-checkbox mb-0">
                    <input
                        ref="input"
                        handler="checkbox"
                        class="custom-control-input"
                        :checked="selected"
                        :disabled="$parent.busy"
                        @input.prevent="toggle"
                    >
                    <span class="custom-control-label"></span>
                </label>
                <actions></actions>
            </div>
        </th>
        <th
            scope="col"
            v-for="(column, index) in $parent.columns"
            :key="index"
            :class="{ 'is-sortable': column.$props.sort }"
            @click="sort(column)"
        >
            <span>{{ column.$props.label }}</span>
            <button type="button" class="table-sort-btn" v-if="$parent.query['sort[by]'] === column.$props.sort">
                <icon :name="icon"></icon>
            </button>
        </th>
        <th scope="col" style="width: 40px;"></th>
    </tr>
</template>

<script>
    import Actions from './HeadingActions';

    export default {
        components: {
            Actions,
        },

        watch: {
            indeterminate(value, oldValue) {
                this.$refs.input.indeterminate = value;
            },
        },

        computed: {
            selected() {
                return this.$parent.selection.length > 0
                    && this.$parent.selection.length === this.$parent.response.data.length;
            },
            indeterminate() {
                return this.$parent.selection.length > 0
                    && this.$parent.selection.length < this.$parent.response.data.length;
            },
            isDesc() {
                return this.$parent.query['sort[order]'] === 'desc';
            },
            icon() {
                return this.isDesc ? 'keyboard-arrow-down' : 'keyboard-arrow-up';
            },
        },

        methods: {
            select() {
                this.$parent.selection = this.$parent.response.data.map((item) => item.id);
            },
            deselect() {
                this.$parent.selection = [];
            },
            toggle() {
                if (! this.$parent.busy) {
                    this.selected ? this.deselect() : this.select();
                }
            },
            sort(column) {
                if (column.sort) {
                    Object.assign(this.$parent.query, {
                        'sort[by]': column.$props.sort,
                        'sort[order]': this.isDesc ? 'asc' : 'desc',
                    });
                }
            },
        },
    }
</script>
