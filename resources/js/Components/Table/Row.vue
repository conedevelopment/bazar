<template>
    <tr :class="{ 'table-active': selected, 'table-danger': trashed }">
        <th scope="row" style="width: 40px;">
            <label class="custom-control custom-checkbox mb-0" @click.prevent="toggle">
                <input handler="checkbox" class="custom-control-input" :checked="selected" :disabled="$parent.busy">
                <span class="custom-control-label"></span>
            </label>
        </th>
        <td v-for="(column, index) in $parent.columns" :key="index" :class="column.$attrs.class" :style="column.$attrs.style">
            <cell :column="column"></cell>
        </td>
        <td style="width: 40px;">
            <actions></actions>
        </td>
    </tr>
</template>

<script>
    import Cell from './Cell';
    import Actions from './RowActions';

    export default {
        components: {
            Cell,
            Actions,
        },

        props: {
            item: {
                type: Object,
                required: true,
            },
        },

        computed: {
            selected() {
                const value = JSON.stringify(this.item);

                return this.$parent.selection.some((item) => JSON.stringify(item) === value);
            },
            trashed() {
                return !! this.item.deleted_at;
            },
        },

        methods: {
            select() {
                this.$parent.selection.push(this.item);
            },
            deselect() {
                const index = this.$parent.selection.indexOf(this.item);

                this.$parent.selection.splice(index, 1);
            },
            toggle() {
                if (! this.$parent.busy) {
                    this.selected ? this.deselect() : this.select();
                }
            },
        },
    }
</script>
