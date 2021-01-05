@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.profile.update') }}" :model="{{ $admin }}" custom>
        <template #default="form">
            <div class="row">
                <div class="col-12 col-lg-7 col-xl-8 form__body">
                    <card :title="__('Profile')">
                        <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
                        <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
                    </card>
                </div>
                <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                    <div class="sticky-helper">
                        <card :title="__('Actions')">
                            <div class="form-group d-flex justify-content-between mb-0">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </card>
                    </div>
                </div>
            </div>
        </template>
    </data-form>
@endsection
