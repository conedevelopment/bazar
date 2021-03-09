<script>
    import Quill from 'quill';
    import Field from './../../Mixins/Field';

    export default {
        mixins: [Field],

        props: {
            value: {
                type: String,
                default: ''
            }
        },

        mounted() {
            this.editor = new Quill(this.$refs.input, this.config);
            this.editor.root.innerHTML = this.value;
            this.editor.enable(! this.attrs.disabled);
            this.editor.on('text-change', () => this.update());
        },

        data() {
            return {
                editor: null
            };
        },

        computed: {
            config() {
                return {
                    modules: {
                        toolbar: {
                            container: [
                                [{ header: [1, 2, 3, 4, false] }],
                                ['bold', 'italic', 'underline'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'align': [] }],
                                ['link', 'image'],
                                ['clean']
                            ],
                            handlers: {
                                image: this.mediaHandler
                            }
                        }
                    },
                    theme: 'snow',
                    formats: ['header', 'align', 'bold', 'underline', 'italic', 'list', 'image', 'link'],
                    placeholder: this.$attrs.placeholder || ''
                };
            }
        },

        methods: {
            update() {
                this.$emit('input', this.editor.root.innerHTML === '<p><br></p>' ? '' : this.editor.root.innerHTML);
            },
            mediaHandler() {
                this.$refs.media.open();
            },
            insertMedia(values) {
                const range = this.editor.getSelection() || 0;

                values.forEach(value => {
                    if (value.is_image) {
                        this.editor.insertEmbed(
                            range ? range.index : 0, 'image', value.urls.original, Quill.sources.USER
                        );
                    } else {
                        this.editor.insertText(
                            range ? range.index : 0, value.name, 'link', value.urls.original, Quill.sources.USER
                        );
                    }
                });

                this.$refs.media.selection = [];
            }
        }
    }
</script>

<template>
    <div class="form-group">
        <label v-if="label" :for="name">{{ label }}</label>
        <div ref="input" class="editor form-control" :class="{ 'is-invalid': invalid }"></div>
        <span v-if="help || invalid" class="form-text" :class="{ 'text-danger': invalid }">
            {{ error || help }}
        </span>
        <media-manager ref="media" multiple @input="insertMedia"></media-manager>
    </div>
</template>
