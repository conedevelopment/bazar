<template>
    <div>
        <div class="row">
            <div
                v-for="medium in modelValue"
                class="mb-3"
                :class="{ 'col-4': multiple, 'col-12': ! multiple }"
                :key="medium.id"
            >
                <div class="selected-media-item">
                    <button type="button" class="selected-media-item__remove" @click="remove(index)">
                        <icon name="close"></icon>
                    </button>
                    <img v-if="medium.is_image" class="img-fluid rounded" :src="medium.urls.thumb" :alt="medium.file_name">
                    <span v-else class="selected-media-item__document" :title="medium.file_name">
                        <icon name="file"></icon>
                    </span>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary" @click="$refs.media.open">
            {{ __('Select :item', { item: multiple ? 'media' : 'medium' }) }}
        </button>
        <media-manager ref="media" :modelValue="modelValue" :multiple="multiple" @update:modelValue="update"></media-manager>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
            multiple: {
                type: Boolean,
                default: false,
            },
        },

        emits: ['update:modelValue'],

        inheritAttrs: false,

        computed: {
            empty() {
                return this.modelValue.length === 0;
            },
        },

        methods: {
            update(value) {
                this.$emit('update:modelValue', value);
            },
            remove(index) {
                let value = Array.from(this.modelValue);
                value.splice(index, 1);
                this.$emit('update:modelValue', value);
            },
        },
    }
</script>
