<template>
    <nav class="app-header__breadcrumb" :aria-label="__('Breadcrumbs')">
        <ol class="breadcrumb">
            <li v-for="(path, index) in paths" :key="index" class="breadcrumb-item" :class="{ 'is-active': isLast(path) }">
                <span v-if="isLast(path)">{{ breadcrumbs[path] }}</span>
                <inertia-link v-else :href="path">{{ breadcrumbs[path] }}</inertia-link>
            </li>
        </ol>
    </nav>
</template>

<script>
    export default {
        computed: {
            breadcrumbs() {
                return this.$page.props.breadcrumbs;
            },
            paths() {
                return Object.keys(this.breadcrumbs).sort((a, b) => a.length - b.length);
            },
        },

        methods: {
            isLast(path) {
                return this.paths.indexOf(path) === this.paths.length - 1;
            },
        },
    }
</script>
