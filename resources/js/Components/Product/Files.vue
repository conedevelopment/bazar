<template>
    <div>
        <div class="form-group">
            <button type="button" class="btn btn-primary mr-2" @click="addCustom">
                {{ __('Add Custom') }}
            </button>
            <button type="button" class="btn btn-outline-primary" @click="$refs.media.open">
                {{ __('Add Media') }}
            </button>
        </div>
        <div v-show="! empty" class="form-group">
            <table class="table table-hover has-filled-header mb-0">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('URL') }}</th>
                        <th scope="col">{{ __('Expiration Days') }}</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(file, index) in modelValue" :key="index">
                        <td>
                            <data-form-input
                                type="text"
                                class="mb-0 form-group-sm"
                                size="10"
                                v-model="file.name"
                                :name="`inventory.files.${index}.name`"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                type="text"
                                class="mb-0 form-group-sm"
                                size="10"
                                :name="`inventory.files.${index}.url`"
                                v-model="file.url"
                            ></data-form-input>
                        </td>
                        <td>
                            <data-form-input
                                type="number"
                                class="mb-0 form-group-sm"
                                min="0"
                                size="1"
                                placeholder="0"
                                :name="`inventory.files.${index}.expiration`"
                                v-model="file.expiration"
                            ></data-form-input>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="icon-btn icon-btn-danger"
                                :aria-label="__('Remove')"
                                @click="remove(index)"
                            >
                                <icon name="close"></icon>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <media-manager ref="media" @update:modelValue="addMedia"></media-manager>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['update:modelValue'],

        computed: {
            empty() {
                return this.modelValue.length === 0;
            },
        },

        methods: {
            addMedia(files) {
                let value = Array.from(this.modelValue);
                value.push({ name: files[0].name, url: files[0].urls.full });
                this.$emit('update:modelValue', value);
            },
            addCustom() {
                let value = Array.from(this.modelValue);
                value.push({ name: '', url: '' });
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
