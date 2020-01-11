<script>
    import Item from './SidebarItem';

    export default {
        components: {
            Item
        }
    }
</script>

<template>
    <div class="media-sidebar">
        <div class="media-sidebar__section">
            <h3 class="media-sidebar__title">{{ __(':count selected media', { count: $parent.selection.length }) }}</h3>
            <div class="media-accordions">
                <item v-for="medium in $parent.selection" :key="medium.id" :item="medium"></item>
            </div>
        </div>
    </div>
</template>
