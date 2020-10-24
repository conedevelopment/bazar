@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', $user->name)

{{-- Content --}}
@section ('content')
    <x-bazar::form action="{{ URL::route('bazar.users.update', $user) }}" :model="$user">
        <card :title="__('General')">
            <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
            <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
        </card>

        <x-slot name="aside">
            <card :title="__('Addresses')" class="mb-5">
                <inertia-link href="{{ URL::route('bazar.users.addresses.index', $user) }}" class="btn btn-primary">
                    {{ __('Manage Addresses') }}
                </inertia-link>
            </card>
        </x-slot>
    </x-bazar::form>
@endsection
