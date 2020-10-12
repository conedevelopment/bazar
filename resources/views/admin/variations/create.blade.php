@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Create Variation'))

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.products.variations.store', $product) }}" :model="$page.variation">
        <template #default="form">
            <card :title="__('Options')" class="mb-5">
                @if (! empty($variation->product->options))
                    <div class="row">
                        @foreach ($variation->product->options as $name => $options)
                            <div class="col">
                                <form-select
                                    name="option.{{ $name }}"
                                    label="{{ __($name) }}"
                                    :options="{{ json_encode(['*' => __('Any')] + array_combine($options, $options)) }}"
                                    v-model="form.fields.option.{{ $name }}"
                                ></form-select>
                            </div>
                        @endforeach
                    </div>
                    <div v-if="form.errors.has('option')" class="row">
                        <div class="col">
                            <span class="form-text text-danger">
                                @{{ form.errors.get('option') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        {{ __('No variable options are available for the product.') }}
                    </div>
                @endif
            </card>
            <card :title="__('Pricing')" class="mb-5">
                <div v-for="(symbol, currency) in $page.currencies" :key="currency" class="row">
                    <div class="col">
                        <form-input
                            v-model="form.fields.prices[currency].normal"
                            type="number"
                            min="0"
                            step="0.1"
                            :name="`form.fields.prices.${currency}.normal`"
                            :label="__('Price (:CURRENCY)', { currency })"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            v-model="form.fields.prices[currency].sale"
                            type="number"
                            min="0"
                            step="0.1"
                            :name="`form.fields.prices.${currency}.sale`"
                            :label="__('Sale Price (:CURRENCY)', { currency })"
                        ></form-input>
                    </div>
                </div>
            </card>
            <card :title="__('Inventory')">
                <template #header>
                    <form-checkbox name="inventory.virtual" :label="__('Virtual')" v-model="form.fields.inventory.virtual"></form-checkbox>
                </template>
                <form-input name="inventory.sku" :label="__('SKU')" v-model="form.fields.inventory.sku"></form-input>
                <form-input
                    name="inventory.quantity"
                    min="0"
                    type="number"
                    :label="__('Quantity')"
                    :help="__('Leave it empty for disabling quantity tracking.')"
                    v-model="form.fields.inventory.quantity"
                    v-show="! form.fields.inventory.virtual"
                ></form-input>
                <form-input
                    name="inventory.weight"
                    min="0"
                    type="number"
                    :label="__('Weight (:unit)', { unit: 'g' })"
                    v-model="form.fields.inventory.weight"
                    v-show="! form.fields.inventory.virtual"
                ></form-input>
                <div class="row align-items-end" v-show="! form.fields.inventory.virtual">
                    <div class="col">
                        <form-input
                            name="inventory.dimensions.length"
                            min="0"
                            type="number"
                            :label="__('Length (:unit)', { unit: 'mm' })"
                            v-model="form.fields.inventory.dimensions.length"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="inventory.dimensions.width"
                            min="0"
                            type="number"
                            :label="__('Width (:unit)', { unit: 'mm' })"
                            v-model="form.fields.inventory.dimensions.width"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="inventory.dimensions.height"
                            min="0"
                            type="number"
                            :label="__('Height (:unit)', { unit: 'mm' })"
                            v-model="form.fields.inventory.dimensions.height"
                        ></form-input>
                    </div>
                </div>
                <div class="form-group">
                    <form-checkbox name="inventory.virtual" :label="__('Downloadable')" v-model="form.fields.inventory.downloadable"></form-checkbox>
                </div>
                <form-downloads name="inventory.files" v-model="form.fields.inventory.files" v-show="form.fields.inventory.downloadable"></form-downloads>
            </card>
        </template>
        <template #aside="form">
            <card :title="__('General')" class="mb-5">
                <form-input name="alias" :label="__('Alias')" v-model="form.fields.alias"></form-input>
            </card>
            <card :title="__('Media')" class="mb-5">
                <form-media name="media" multiple v-model="form.fields.media"></form-media>
            </card>
        </template>
    </data-form>
@endsection
