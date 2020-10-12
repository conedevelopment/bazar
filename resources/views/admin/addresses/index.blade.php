@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Addresses'))

{{-- Content --}}
@section ('content')
    <card :title="__('Addresses')">
        <template #header>
            <inertia-link href="{{ URL::route('bazar.users.addresses.create', $user) }}" class="btn btn-primary btn-sm">
                {{ __('Create Address') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" searchable>
            <data-column :label="__('Alias')" sort="alias">
                <template #default="item">
                    <inertia-link :href="`{{ URL::route('bazar.users.addresses.index', $user) }}/${item.id}`">
                        @{{ item.alias }}
                    </inertia-link>
                </template>
            </data-column>
            <data-column :label="__('Name')">
                <template #default="item">
                    @{{ item.name }}
                </template>
            </data-column>
            <data-column :label="__('Address')">
                <template #default="item">
                    @{{ item.country_name }}<br>
                    @{{ item.postcode }}, @{{ item.city }}<br>
                    @{{ item.address }}
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
