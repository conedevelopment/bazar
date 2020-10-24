@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', $category->name)

{{-- Content --}}
@section ('content')
    <x-bazar::form action="{{ URL::route('bazar.categories.update', $category) }}" :model="$category">
        <card :title="__('General')">
            <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
            <form-input name="slug" :label="__('Slug')" v-model="form.fields.slug"></form-input>
            <form-editor name="description" :label="__('Description')" v-model="form.fields.description"></form-editor>
        </card>

        <x-slot name="aside">
            <card :title="__('Media')" class="mb-5">
                <form-media name="media" v-model="form.fields.media"></form-media>
            </card>
        </x-slot>
    </x-bazar::form>
@endsection
