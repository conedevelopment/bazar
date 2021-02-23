<script>
    export default {
        data() {
            return {
                busy: false
            };
        },

        computed: {
            id() {
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
                    data: { id: this.id }
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
            <button type="button" class="btn btn-danger" :disabled="! id.length || busy" @click.prevent="destroy">
                {{ __('Delete') }}
            </button>
            <span v-show="id.length" class="modal-help-text ml-3">
                {{ __(':files file(s) are selected', { files: id.length }) }}
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
