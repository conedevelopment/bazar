@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Variations'))

{{-- Content --}}
@section ('content')
    <card :title="__('Variations')">
        <template #header>
            <inertia-link
                href="{{ URL::route('bazar.products.variations.create', $product) }}"
                class="btn btn-primary btn-sm"
            >
                {{ __('Create Variation') }}
            </inertia-link>
        </template>
        <data-table :response="$page.results" :filters="$page.filters" searchable>
            <data-column :label="__('Photo')">
                <template #default="item">
                    <img
                        class="table-preview-image"
                        :src="item.media[0] ? item.media[0].urls.thumb : '{{ asset('vendor/bazar/img/placeholder.svg') }}'"
                        alt=""
                    >
                </template>
            </data-column>
            <data-column :label="__('Alias')" sort="alias">
                <template #default="item">
                    <inertia-link :href="`{{ URL::route('bazar.products.variations.index', $product) }}/${item.id}`">
                        @{{ item.alias }}
                    </inertia-link>
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
    </card>
@endsection
