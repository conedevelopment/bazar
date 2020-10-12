@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Categories'))

{{-- Content --}}
@section ('content')
    <card :title="__('Categories')">
        <template #header>
            <inertia-link href="{{ URL::route('bazar.categories.create') }}" class="btn btn-primary btn-sm">
                {{ __('Create Category') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" :filters="$page.filters" searchable>
            <data-column :label="__('Photo')">
                <template #default="item">
                    <img
                        class="table-preview-image"
                        :src="item.media[0] ? item.media[0].urls.thumb : '{{ URL::asset('vendor/bazar/img/placeholder.svg') }}'"
                        :alt="item.name"
                    >
                </template>
            </data-column>
            <data-column :label="__('Name')" sort="name">
                <template #default="item">
                    <inertia-link :href="`{{ URL::route('bazar.categories.index') }}/${item.id}`">
                        @{{ item.name }}
                    </inertia-link>
                </template>
            </data-column>
            <data-column :label="__('Created at')" sort="created_at">
                <template #default="item">
                    @{{ item.created_at }}
                </template>
            </data-column>
        </data-table>
    </card>
@endsection
