<script>
    import Item from './Item';
    import Filters from './Filters';
    import Sidebar from './Sidebar';
    import Toolbar from './Toolbar';
    import Uploader from './Uploader';
    import Pagination from './Pagination';
    import Closable from './../../Mixins/Closable';
    import Queryable from './../../Mixins/Queryable';

    export default {
        components: {
            Item,
            Filters,
            Sidebar,
            Toolbar,
            Uploader,
            Pagination
        },

        mixins: [Closable, Queryable],

        props: {
            value: {
                type: Array,
                default: () => []
            },
            endpoint: {
                type: String,
                default: '/bazar/media'
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },

        mounted() {
            this.$once('open', this.fetch);

            window.addEventListener('keyup', event => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });
        },

        watch: {
            isOpen(n, o) {
                const className = this.$el.closest('.form__sidebar') ? 'sidebar' : 'body';

                if (n) {
                    this.$root.$el.classList.add(`has-modal-open--${className}`);
                } else {
                    this.$root.$el.classList.remove(`has-modal-open--${className}`);
                }
            }
        },

        data() {
            return {
                queue: [],
                dragging: false,
                selection: Array.from(this.value || []),
                query: {
                    type: 'all'
                }
            };
        },

        methods: {
            handleFiles(files) {
                this.dragging = false;

                for (let i = 0; i < files.length; i++) {
                    this.queue.unshift(files.item(i));
                }
            }
        }
    }
</script>

<template>
    <div v-show="isOpen" class="modal" @click.self="close">
        <div class="modal-media modal-dialog modal-dialog-scrollable modal-dialog-centered modal-full-screen">
            <div
                class="modal-content"
                :class="{ 'has-active-dropzone': dragging }"
                :data-dropzone-text="__('Drop your files here...')"
                @dragstart.prevent
                @dragend.prevent="dragging = false"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="handleFiles($event.dataTransfer.files)"
            >
                <div class="modal-header">
                    <h3 class="modal-title">{{ __('Media') }}</h3>
                    <button type="button" class="close" @click.prevent="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid p-0">
                        <div class="row mb-3" style="border-bottom: 1px solid #dee2e6;">
                            <div class="col-8">
                                <filters></filters>
                            </div>
                            <div class="col-4">
                                <pagination></pagination>
                            </div>
                        </div>
                        <div v-if="queue.length || items.length" class="form-row">
                            <div :class="{ 'col-9': selection.length, 'col-12': ! selection.length }">
                                <div class="form-row">
                                    <uploader v-for="(file, index) in queue" :key="`uploader-${index}`" :file="file"></uploader>
                                    <item v-for="(item, index) in items" :key="`${item.file_name}-${index}`" :item="item"></item>
                                </div>
                            </div>
                            <div v-show="selection.length" class="col-3">
                                <sidebar></sidebar>
                            </div>
                        </div>
                        <div v-else class="row">
                            <div class="col-12">
                                <div class="alert alert-info">{{ __('No media available.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <toolbar></toolbar>
                </div>
            </div>
        </div>
    </div>
</template>
