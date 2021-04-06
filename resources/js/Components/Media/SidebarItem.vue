<template>
    <div class="media-accordion">
        <div class="media-accordion__heading" @click="toggle">
            <h3 class="media-accordion__title d-flex align-items-center">
                <div class="media-accordion__image-wrapper" :class="{ 'is-loading': loading }" v-if="item.is_image">
                    <img :src="url" class="media-accordion__image" alt="" @error="reload" @load="loading = false">
                </div>
                <icon v-else name="file" class="media-accordion__icon"></icon>
                <span style="text-overflow: ellipsis; max-width: 190px; display: inline-block; overflow: hidden; white-space: nowrap;">
                    {{ item.file_name }}
                </span>
            </h3>
            <button type="button" class="icon-btn icon-btn-danger" :aria-label="__('Remove')" @click="remove">
                <icon name="close"></icon>
            </button>
        </div>
        <div class="media-accordion__content" :class="{ 'is-open': isOpen }">
            <ul class="media-sidebar__list mt-3 mb-3">
                <li>{{ createdAt }}</li>
                <li>{{ size }}</li>
                <li v-if="item.is_image" v-html="dimensions"></li>
            </ul>
            <button type="button" class="btn btn-sm btn-outline-danger" :disabled="busy" @click="destroy">
                {{ __('Delete') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                tries: 0,
                isOpen: false,
                loading: false,
                busy: false,
                url: this.item.urls.thumb,
            };
        },

        computed: {
            action() {
                return `/admin/media/${this.item.id}`;
            },
            size() {
                const sizes = ['KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(this.item.size) / Math.log(1024));

                return (this.item.size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
            },
            dimensions() {
                return `${this.item.width}&times;${this.item.height} px`;
            },
            createdAt() {
                return this.item.created_at.substr(0, 16).replace('T', ' ');
            },
        },

        methods: {
            toggle() {
                this.isOpen = ! this.isOpen;
            },
            remove() {
                this.$parent.$parent.selection.splice(
                    this.$parent.$parent.selection.findIndex((item) => item.id === this.item.id), 1
                );
            },
            destroy() {
                this.busy = true;
                this.$http.delete(this.action).then((response) => {
                    this.$parent.$parent.response.data.splice(
                        this.$parent.$parent.response.data.findIndex((item) => item.id === this.item.id), 1
                    );
                    this.remove();
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.busy = false;
                });
            },
            reload() {
                if (this.tries >= 5) {
                    this.loading = false;
                    return;
                }

                this.loading = true;

                const interval = setInterval(() => {
                    const url = new URL(this.url);
                    url.searchParams.set('key', (new Date).getTime());

                    this.url = url.toString();
                    this.tries++;

                    clearInterval(interval);
                }, 5000);
            },
        },
    }
</script>
