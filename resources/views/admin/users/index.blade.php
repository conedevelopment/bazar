@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Users'))

{{-- Content --}}
@section ('content')
    <card :title="__('Users')">
        <template #header>
            <inertia-link href="{{ URL::route('bazar.users.create') }}" class="btn btn-primary btn-sm">
                {{ __('Create User') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" :filters="$page.filters" searchable>
            <data-column :label="__('Avatar')">
                <template #default="item">
                    <img class="table-preview-image" :src="item.avatar" :alt="item.name">
                </template>
            </data-column>
            <data-column :label="__('Name')" sort="name">
                <template #default="item">
                    <inertia-link :href="`{{ URL::route('bazar.users.index') }}/${item.id}`">
                        @{{ item.name }}
                    </inertia-link>
                </template>
            </data-column>
            <data-column :label="__('Email')" sort="email">
                <template #default="item">
                    @{{ item.email }}
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
