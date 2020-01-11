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
            // this.editor.enable(! this.disabled);
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
                        toolbar: [
                            [{ header: [1, 2, 3, 4, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    },
                    theme: 'snow',
                    formats: ['header', 'bold', 'underline', 'italic', 'list'],
                    placeholder: this.$attrs.placeholder || ''
                };
            }
        },

        methods: {
            update() {
                this.$emit('input', this.editor.root.innerHTML === '<p><br></p>' ? '' : this.editor.root.innerHTML);
            }
        }
    }
</script>

<template>
    <div class="form-group">
        <label v-if="label" :for="name">{{ label }}</label>
        <div
            ref="input"
            class="editor form-control"
            :class="{ 'is-invalid': invalid }"
        ></div>
        <span v-if="help || invalid" class="form-text" :class="{ 'text-danger': invalid }">
            {{ error || help }}
        </span>
    </div>
</template>
