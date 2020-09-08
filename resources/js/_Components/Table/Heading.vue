<script>
    import Actions from './HeadingActions';

    export default {
        components: {
            Actions
        },

        watch: {
            indeterminate(n, o) {
                this.$refs.input.indeterminate = n;
            }
        },

        computed: {
            selected() {
                return this.$parent.items.length > 0 && this.$parent.selection.length === this.$parent.items.length
            },
            indeterminate() {
                return this.$parent.selection.length > 0 && this.$parent.selection.length < this.$parent.items.length;
            },
            isDesc() {
                return this.$parent.query['sort[order]'] === 'desc';
            },
            icon() {
                return this.isDesc ? 'keyboard-arrow-down' : 'keyboard-arrow-up';
            }
        },

        methods: {
            select() {
                this.$parent.selection = Array.from(this.$parent.items);
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
                    this.$parent.query = Object.assign(this.$parent.query, {
                        'sort[by]': column.sort,
                        'sort[order]': this.isDesc ? 'asc' : 'desc'
                    });
                }
            }
        }
    }
</script>

<template>
    <tr>
        <th scope="col" style="width: 80px;">
            <div class="d-flex">
                <label class="custom-control custom-checkbox mb-0" :disabled="$parent.busy" @click.prevent="toggle">
                    <input
                        ref="input"
                        type="checkbox"
                        class="custom-control-input"
                        :checked="selected"
                        :disabled="$parent.busy"
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
            :class="{ 'is-sortable': column.sort }"
            @click.prevent="sort(column)"
        >
            <span>{{ column.label }}</span>
            <button type="button" class="table-sort-btn" v-if="column.sort && $parent.query['sort[by]'] === column.sort">
                <icon :icon="icon"></icon>
            </button>
        </th>
        <th scope="col" style="width: 40px;"></th>
    </tr>
</template>
