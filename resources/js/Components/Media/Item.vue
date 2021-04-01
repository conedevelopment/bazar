<template>
    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
        <div
            class="media-item"
            style="cursor: pointer;"
            :class="{ 'is-image': item.is_image, 'is-document': ! item.is_image, 'is-selected': selected, 'is-loading': loading }"
            @click.prevent="toggle"
        >
            <img v-if="item.is_image" :src="url" :alt="item.name" @error="reload" @load="loading = false">
            <span v-else class="media-item__caption">
                <icon name="file"></icon>
                <span style="text-overflow: ellipsis;">{{ item.file_name }}</span>
            </span>
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
                loading: false,
                url: this.item.urls.thumb,
            };
        },

        computed: {
            selected() {
                return this.$parent.selection.some((item) => item.id === this.item.id);
            },
        },

        methods: {
            select() {
                if (this.$parent.multiple) {
                    this.$parent.selection.push(this.item)
                } else {
                    this.$parent.selection = [this.item];
                }
            },
            deselect() {
                const index = this.$parent.selection.findIndex((item) => item.id === this.item.id);

                this.$parent.selection.splice(index, 1);
            },
            toggle() {
                if (! this.$parent.processing) {
                    this.selected ? this.deselect() : this.select();
                }
            },
            reload() {
                if (this.tries >= 5) {
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
