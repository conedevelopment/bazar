@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.users.update', $user) }}" :model="{{ $user }}">
        <template #default="form">
            <card :title="__('General')">
                <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
                <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
            </card>
        </template>
        <template #aside>
            <card :title="__('Addresses')" class="mb-5">
                <bazar-link href="{{ URL::route('bazar.users.addresses.index', $user) }}" class="btn btn-primary">
                    {{ __('Manage Addresses') }}
                </bazar-link>
            </card>
        </template>
    </data-form>
@endsection
