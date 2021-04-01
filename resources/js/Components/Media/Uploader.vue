<template>
    <div class="col-sm-4 col-md-3 col-lg-2 mb-3">
        <div class="uploader-item">
            <div v-if="! error" class="uploader-item__progress" :style="{ width: `${progress}%` }"></div>
            <span v-else>{{ error }}</span>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            file: {
                type: File,
                required: true,
            },
        },

        beforeMount() {
            this.generateHash();
            this.createChunks();
        },

        watch: {
            chunks: {
                handler(value, oldValue) {
                    if (value.length > 0) {
                        this.upload();
                    }
                },
                deep: true,
            },
        },

        data() {
            return {
                chunks: [],
                hash: null,
                error: null,
                uploaded: 0,
            };
        },

        computed: {
            progress() {
                return Math.floor((this.uploaded * 100) / this.file.size);
            },
            formData() {
                let formData = new FormData;

                formData.set('is_last', this.chunks.length === 1);
                formData.set('file', this.chunks[0], `${this.hash}__${this.file.name}.chunk`);

                return formData;
            },
            config() {
                return {
                    method: 'POST',
                    data: this.formData,
                    url: this.$parent.endpoint,
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: (event) => { this.uploaded += event.loaded },
                };
            }
        },

        methods: {
            upload() {
                this.$http(this.config).then((response) => {
                    this.onSuccess(response.data);
                }).catch((error) => {
                    this.error = this.__('An error occured!');
                });
            },
            retry() {
                this.chunks = [];
                this.error = null;
                this.uploaded = 0;
                this.generateHash();
                this.createChunks();
            },
            onSuccess(response) {
                this.chunks.shift();

                if (this.chunks.length === 0) {
                    this.$parent.response.data.unshift(response);
                    this.$parent.queue.splice(this.$parent.queue.indexOf(this.file), 1);
                }
            },
            generateHash() {
                this.hash = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
            },
            createChunks() {
                let chunks = [];
                const size = 1024 * 1024;
                const count = Math.ceil(this.file.size / size);

                for (let i = 0; i < count; i++) {
                    chunks.push(this.file.slice(
                        i * size, Math.min(i * size + size, this.file.size), this.file.type
                    ));
                }

                this.chunks = chunks;
            },
        },
    }
</script>
