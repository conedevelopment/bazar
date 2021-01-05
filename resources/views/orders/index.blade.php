@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ __('Orders') }}</h2>
            <bazar-link href="{{ URL::route('bazar.orders.create') }}" class="btn btn-primary btn-sm">
                {{ __('Create Order') }}
            </bazar-link>
        </div>
        <div class="card__inner">
            <data-table :response="{{ json_encode($results) }}" :filters="{{ json_encode($filters) }}" searchable>
                <data-column :label="__('ID')" sort="id">
                    <template #default="item">
                        <bazar-link :href="`{{ URL::route('bazar.orders.index') }}/${item.id}`">
                            #@{{ item.id }}
                        </bazar-link>
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
                        <span class="badge" :class="item.status">
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
        </div>
    </section>
@endsection
