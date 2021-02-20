<script>
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        inheritAttrs: false,

        props: {
            value: {
                type: Array,
                default: () => []
            }
        },

        watch: {
            files: {
                handler(n, o) {
                    this.$emit('input', n);
                },
                deep: true
            }
        },

        data() {
            return {
                files: this.value || []
            };
        },

        computed: {
            empty() {
                return this.files.length === 0;
            }
        },

        methods: {
            addFromMedia(files) {
                this.files.push({ name: files[0].name, url: files[0].urls.full });
            },
            addCustom() {
                this.files.push({ name: '', url: '' });
            },
            remove(index) {
                this.files.splice(index, 1);
            }
        }
    }
</script>

<template>
    <div>
        <div class="form-group">
            <button type="button" class="btn btn-primary mr-2" @click.prevent="addCustom">
                {{ __('Add Custom') }}
            </button>
            <button type="button" class="btn btn-outline-primary" @click.prevent="$refs.files.open()">
                {{ __('From Media') }}
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
                    <tr v-for="(file, index) in files" :key="index">
                        <td>
                            <form-input
                                class="mb-0 form-group-sm"
                                size="10"
                                v-model="file.name"
                                :name="`${name}.${index}.name`"
                            ></form-input>
                        </td>
                        <td>
                            <form-input
                                class="mb-0 form-group-sm"
                                size="10"
                                v-model="file.url"
                                :name="`${name}.${index}.url`"
                            ></form-input>
                        </td>
                        <td>
                            <form-input
                                class="mb-0 form-group-sm"
                                type="number"
                                min="0"
                                size="1"
                                placeholder="0"
                                v-model="file.expiration"
                                :name="`${name}.${index}.expiration`"
                            ></form-input>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="icon-btn icon-btn-danger"
                                :aria-label="__('Remove')"
                                @click.prevent="remove(index)"
                            >
                                <svg aria-hidden="true" role="img" fill="currentColor" class="icon name-close">
                                    <use href="#icon-close" xlink:href="#icon-close"></use>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <media-manager ref="files" @input="addFromMedia"></media-manager>
    </div>
</template>
