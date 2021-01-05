<nav class="app-header__breadcrumb" aria-label="{{ __('Breadcrumbs') }}">
    <ol class="breadcrumb">
        <li v-for="(path, index) in paths" :key="index" class="breadcrumb-item" :class="{ 'is-active': isLast(path) }">
            <span v-if="isLast(path)">{{ breadcrumbs[path] }}</span>
            <bazar-link v-else :href="path">{{ breadcrumbs[path] }}</bazar-link>
        </li>
    </ol>
</nav>
