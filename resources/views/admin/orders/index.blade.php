@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Orders'))

{{-- Content --}}
@section ('content')
    <card :title="__('Orders')">
        <template #header>
            <inertia-link href="{{ URL::route('bazar.orders.create') }}" class="btn btn-primary btn-sm">
                {{ __('Create Order') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" :filters="$page.filters" searchable>
            <data-column :label="__('ID')" sort="id">
                <template #default="item">
                    <inertia-link :href="`{{ URL::route('bazar.orders.index') }}/${item.id}`">
                        #@{{ item.id }}
                    </inertia-link>
                </template>
            </data-column>
            <data-column :label="__('Total')">
                <template #default="item">
                    @{{ item.formatted_total }}
                </template>
            </data-column>
            <data-column :label="__('Customer')">
                <template #default="item">
                    @{{ item.address.name }}
                </template>
            </data-column>
            <data-column :label="__('Status')" sort="status">
                <template #default="item">
                    <span class="badge badge-warning" :class="{
                        'badge-success': item.status === 'completed',
                        'badge-danger':  ['failed', 'cancelled'].includes(item.status),
                        'badge-light': item.status === 'on_hold'
                    }">
                        @{{ item.status_name }}
                    </span>
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
