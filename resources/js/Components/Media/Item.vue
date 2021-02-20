<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true
            }
        },

        computed: {
            selected() {
                return this.$parent.selection.some(item => item.id === this.item.id);
            }
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
                const index = this.$parent.selection.findIndex(item => item.id === this.item.id);

                this.$parent.selection.splice(index, 1);
            },
            toggle() {
                if (! this.$parent.busy) {
                    this.selected ? this.deselect() : this.select();
                }
            }
        }
    }
</script>

<template>
    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
        <div
            class="media-item"
            :class="{ 'is-image': item.is_image, 'is-document': ! item.is_image, 'is-selected': selected }"
            @click.prevent="toggle"
        >
            <img v-if="item.is_image" :src="item.urls.thumb" :alt="item.name">
            <span v-else class="media-item__caption">
                <svg aria-hidden="true" role="img" fill="currentColor" class="icon name-file">
                    <use href="#icon-file" xlink:href="#icon-file"></use>
                </svg>
                <span style="text-overflow: ellipsis;">{{ item.file_name }}</span>
            </span>
        </div>
    </div>
</template>
