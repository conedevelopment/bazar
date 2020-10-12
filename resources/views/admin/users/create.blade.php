@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Create User'))

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.users.store') }}" :model="$page.user">
        <template #default="form">
            <card :title="__('General')">
                <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
                <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
            </card>
        </template>
    </data-form>
@endsection
