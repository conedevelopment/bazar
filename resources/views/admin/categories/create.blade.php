@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Create Category'))

{{-- Content --}}
@section ('content')
    <data-form action="{{ URL::route('bazar.categories.store') }}" :model="$page.category">
        <template #default="form">
            <card :title="__('General')">
                <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
                <form-input name="slug" :label="__('Slug')" v-model="form.fields.slug"></form-input>
                <form-editor name="description" :label="__('Description')" v-model="form.fields.description"></form-editor>
            </card>
        </template>
        <template #aside="form">
            <card :title="__('Media')" class="mb-5">
                <form-media name="media" v-model="form.fields.media"></form-media>
            </card>
        </template>
    </data-form>
@endsection
