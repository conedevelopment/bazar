@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ __('Variants') }}</h2>
            <bazar-link href="{{ URL::route('bazar.products.variants.create', $product) }}" class="btn btn-primary btn-sm">
                {{ __('Create Variant') }}
            </bazar-link>
        </div>
        <div class="card__inner">
            <data-table :response="{{ json_encode($results) }}" :filters="{{ json_encode($filters) }}" searchable>
                <data-column :label="__('Photo')">
                    <template #default="item">
                        <img
                            class="table-preview-image"
                            :src="item.media[0] ? item.media[0].urls.thumb : '/vendor/bazar/img/placeholder.svg'"
                            alt=""
                        >
                    </template>
                </data-column>
                <data-column :label="__('Alias')" sort="alias">
                    <template #default="item">
                        <bazar-link :href="`{{ URL::route('bazar.products.variants.index', $product) }}/${item.id}`">
                            @{{ item.alias }}
                        </bazar-link>
                    </template>
                </data-column>
                <data-column :label="__('Price')">
                    <template #default="item">
                        @{{ item.price ? item.formatted_price : item.product.formatted_price }}
                    </template>
                </data-column>
                <data-column :label="__('Option')">
                    <template #default="item">
                        <span v-for="(value, key) in item.option" :key="key" class="badge badge-primary mr-1">
                            @{{ __(key) }}: @{{ __(value) }}
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
