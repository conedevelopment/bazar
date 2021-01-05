@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.orders.update', $order) }}" :model="{{ $order }}">
        <template #default="form">
            <card :title="__('General')" class="mb-5">
                <order-info :order="form.model"></order-info>
            </card>
            <card :title="__('Products')">
                <div v-if="! form.fields.products.length" class="alert alert-info  mb-0">
                    {{ __('There are no products yet.') }}
                </div>
                <products-table v-else :order="form.model"></products-table>
            </card>
        </template>
        <template #aside="form">
            <card :title="__('Settings')" class="mb-5">
                <form-select name="status" :label="__('Status')" :options="{{ json_encode($statuses) }}" v-model="form.fields.status"></form-select>
            </card>
        </template>
    </data-form>
    <div class="row">
        <div class="col-12 col-lg-7 col-xl-8 mt-5">
            <transactions :order="{{ $order }}"></transactions>
        </div>
    </div>
@endsection
