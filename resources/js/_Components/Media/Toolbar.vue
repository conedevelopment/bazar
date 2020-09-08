<script>
    export default {
        data() {
            return {
                busy: false
            };
        },

        computed: {
            ids() {
                return this.$parent.selection.map(item => item.id);
            }
        },

        methods: {
            select() {
                this.$parent.$emit('input', this.$parent.selection);
                this.$parent.close();
            },
            destroy() {
                this.busy = true;
                this.$http.delete('/bazar/media/batch-destroy', {
                    data: { ids: this.ids }
                }).then(response => {
                    this.refresh();
                }).catch(error => {
                    //
                }).finally(() => {
                    this.busy = false;
                });
            },
            refresh() {
                this.$parent.selection = [];
                this.$parent.fetch();
            }
        }
    }
</script>

<template>
    <div class="d-flex justify-content-between w-100">
        <div>
            <button type="button" class="btn btn-danger" :disabled="! ids.length || busy" @click.prevent="destroy">
                {{ __('Delete') }}
            </button>
            <span v-show="ids.length" class="modal-help-text ml-3">
                {{ __(':files file(s) are selected', { files: ids.length }) }}
            </span>
        </div>
        <div>
            <button type="button" :disabled="busy" class="btn btn-primary" @click.prevent="select">
                {{ __('Select') }}
            </button>
            <button type="button" class="btn btn-outline-primary ml-2" :disabled="busy" @click.prevent="$parent.close">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</template>
