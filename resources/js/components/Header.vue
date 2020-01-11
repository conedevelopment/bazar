<script>
    import Breadcrumbs from './Breadcrumbs';

    export default {
        components: {
            Breadcrumbs
        }
    }
</script>

<template>
    <header class="app-header">
        <div class="row justify-content-between align-items-center">
            <div class="col-12">
                <breadcrumbs></breadcrumbs>
                <h1 class="app-header__title mt-3 mt-md-2">
                    {{ $parent.title || __('Dashboard') }}
                </h1>
            </div>
        </div>
    </header>
</template>
