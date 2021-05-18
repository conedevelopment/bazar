<template>
    <div class="d-flex justify-content-between w-100">
        <div>
            <button
                type="button"
                class="btn btn-danger"
                :disabled="id.length === 0 || $parent.busy"
                @click="destroy"
            >
                {{ __('Delete') }}
            </button>
            <span v-show="id.length" class="modal-help-text ml-3">
                {{ __(':count items selected', { count: id.length }) }}
            </span>
        </div>
        <div>
            <button type="button" :disabled="$parent.busy" class="btn btn-primary" @click="select">
                {{ __('Select') }}
            </button>
            <button type="button" class="btn btn-outline-primary ml-2" :disabled="$parent.busy" @click="$parent.close">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            id() {
                return this.$parent.selection.map((item) => item.id);
            },
        },

        methods: {
            select() {
                this.$parent.$emit('update:modelValue', this.$parent.selection);
                this.$parent.close();
            },
            destroy() {
                this.$parent.busy = true;
                this.$http.delete('/bazar/media/batch-destroy', {
                    data: { id: this.id },
                }).then((response) => {
                    this.refresh();
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.$parent.busy = false;
                });
            },
            refresh() {
                this.$parent.selection = [];
                this.$parent.fetch();
            },
        },
    }
</script>
