@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ __('Users') }}</h2>
            <bazar-link href="{{ URL::route('bazar.users.create') }}" class="btn btn-primary btn-sm">
                {{ __('Create User') }}
            </bazar-link>
        </div>
        <div class="card__inner">
            <data-table :response="{{ json_encode($results) }}" :filters="{{ json_encode($filters) }}" searchable>
                <data-column :label="__('Avatar')">
                    <template #default="item">
                        <img class="table-preview-image" :src="item.avatar" :alt="item.name">
                    </template>
                </data-column>
                <data-column :label="__('Name')" sort="name">
                    <template #default="item">
                        <bazar-link :href="`{{ URL::route('bazar.users.index') }}/${item.id}`">
                            @{{ item.name }}
                        </bazar-link>
                    </template>
                </data-column>
                <data-column :label="__('Email')" sort="email">
                    <template #default="item">
                        @{{ item.email }}
                    </template>
                </data-column>
                <data-column :label="__('Created at')" sort="created_at">
                    <template #default="item">
                        @{{ item.created_at }}
                    </template>
                </data-column>
            </data-table>
        </div>
    </section>
@endsection
