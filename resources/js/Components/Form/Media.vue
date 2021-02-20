<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        inheritAttrs: false,

        props: {
            value: {
                type: Array,
                default: () => []
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },

        watch: {
            media: {
                handler(n, o) {
                    this.$emit('input', n);
                },
                deep: true
            }
        },

        data() {
            return {
                media: this.value || []
            };
        },

        computed: {
            empty() {
                return this.media.length === 0;
            }
        },

        methods: {
            remove(index) {
                this.media.splice(index, 1);
            }
        }
    }
</script>

<template>
    <div>
        <div class="form-row">
            <div
                v-for="(medium, index) in media"
                class="mb-3"
                :class="{ 'col-4': multiple, 'col-12': ! multiple }"
                :key="index"
            >
                <div class="selected-media-item">
                    <button type="button" class="selected-media-item__remove" @click.prevent="remove(index)">
                        <svg aria-hidden="true" role="img" fill="currentColor" class="icon name-close">
                            <use href="#icon-close" xlink:href="#icon-close"></use>
                        </svg>
                    </button>
                    <img v-if="medium.is_image" class="img-fluid rounded" :src="medium.urls.thumb" alt="">
                    <span v-else class="selected-media-item__document" :title="medium.file_name">
                        <svg aria-hidden="true" role="img" fill="currentColor" class="icon name-file">
                            <use href="#icon-file" xlink:href="#icon-file"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary" @click.prevent="$refs.media.open()">
            {{ __('Select :item', { item: multiple ? 'media' : 'medium' }) }}
        </button>
        <media-manager ref="media" v-model="media" :multiple="multiple"></media-manager>
    </div>
</template>
