@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Create User'))

{{-- Content --}}
@section ('content')
    <x-bazar::form action="{{ URL::route('bazar.users.store') }}" :model="$user">
        <card :title="__('General')">
            <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
            <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
        </card>
    </x-bazar::form>
@endsection
