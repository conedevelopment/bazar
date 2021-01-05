@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
<data-form action="{{ URL::route('bazar.products.variants.update', [$product, $variant]) }}" :model="{{ $variant }}">
        <template #default="form">
            <card :title="__('Options')" class="mb-5">
                @if (! empty($product->options))
                    <div class="row">
                        @foreach ($product->options as $name => $options)
                            <div class="col">
                                <form-select
                                    name="option.{{ $name }}"
                                    label="{{ __($name) }}"
                                    :options="{{ json_encode(['*' => __('Any')] + $options) }}"
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
                @foreach ($currencies as $currency => $symbol)
                    <div class="row">
                        <div class="col">
                            <form-input
                                v-model="form.fields.prices.{{ $currency }}.default"
                                type="number"
                                min="0"
                                step="0.1"
                                name="form.fields.prices.{{ $currency }}.default"
                                label="{{ __('Price (:CURRENCY)', compact('currency')) }}"
                            ></form-input>
                        </div>
                        <div class="col">
                            <form-input
                                v-model="form.fields.prices.{{ $currency }}.sale"
                                type="number"
                                min="0"
                                step="0.1"
                                name="form.fields.prices.{{ $currency }}.sale"
                                label="{{ __('Sale Price (:CURRENCY)', compact('currency')) }}"
                            ></form-input>
                        </div>
                    </div>
                @endforeach
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
                    label="{{ __('Weight (:unit)', ['unit' => Config::get('bazar.weight_unit')]) }}"
                    v-model="form.fields.inventory.weight"
                    v-show="! form.fields.inventory.virtual"
                ></form-input>
                <div class="row align-items-end" v-show="! form.fields.inventory.virtual">
                    <div class="col">
                        <form-input
                            name="inventory.length"
                            min="0"
                            type="number"
                            label="{{ __('Length (:unit)', ['unit' => Config::get('bazar.dimension_unit')]) }}"
                            v-model="form.fields.inventory.length"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="inventory.width"
                            min="0"
                            type="number"
                            label="{{ __('Width (:unit)', ['unit' => Config::get('bazar.dimension_unit')]) }}"
                            v-model="form.fields.inventory.width"
                        ></form-input>
                    </div>
                    <div class="col">
                        <form-input
                            name="inventory.height"
                            min="0"
                            type="number"
                            label="{{ __('Height (:unit)', ['unit' => Config::get('bazar.dimension_unit')]) }}"
                            v-model="form.fields.inventory.height"
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
