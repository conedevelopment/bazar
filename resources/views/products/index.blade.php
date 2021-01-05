@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ __('Products') }}</h2>
            <bazar-link href="{{ URL::route('bazar.products.create') }}" class="btn btn-primary btn-sm">
                {{ __('Create Product') }}
            </bazar-link>
        </div>
        <div class="card__inner">
            <data-table :response="{{ json_encode($results) }}" :filters="{{ json_encode($filters) }}" searchable>
                <data-column :label="__('Photo')">
                    <template #default="item">
                        <img
                            class="table-preview-image"
                            :src="item.media[0] ? item.media[0].urls.thumb : '/vendor/bazar/img/placeholder.svg'"
                            :alt="item.name"
                        >
                    </template>
                </data-column>
                <data-column :label="__('Name')" sort="name">
                    <template #default="item">
                        <bazar-link :href="`{{ URL::route('bazar.products.index') }}/${item.id}`">
                            @{{ item.name }}
                        </bazar-link>
                    </template>
                </data-column>
                <data-column :label="__('SKU')" sort="inventory->sku">
                    <template #default="item">
                        @{{ item.inventory.sku }}
                    </template>
                </data-column>
                <data-column :label="__('Price')">
                    <template #default="item">
                        @{{ item.formatted_price }}
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
