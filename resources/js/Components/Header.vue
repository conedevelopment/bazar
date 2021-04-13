<template>
    <header class="app-header">
        <div class="row justify-content-between align-items-center">
            <div class="col">
                <breadcrumbs></breadcrumbs>
                <h1 class="app-header__title mt-3 mt-md-2">
                    <icon :name="$parent.icon"></icon>
                    <span>{{ $parent.title }}</span>
                </h1>
            </div>
            <div class="app-header__actions col-auto"></div>
        </div>
    </header>
</template>

<script>
    import Breadcrumbs from './Breadcrumbs';

    export default {
        components: {
            Breadcrumbs,
        },
    }
</script>
