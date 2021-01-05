@extends ('bazar::layout.layout')

{{-- Content --}}
@section ('content')
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ __('Addresses') }}</h2>
            <bazar-link href="{{ URL::route('bazar.users.addresses.create', $user) }}" class="btn btn-primary btn-sm">
                {{ __('Create Address') }}
            </bazar-link>
        </div>
        <div class="card__inner">
            <data-table :response="{{ json_encode($results) }}" searchable>
                <data-column :label="__('Alias')" sort="alias">
                    <template #default="item">
                        <bazar-link :href="`{{ URL::route('bazar.users.addresses.index', $user) }}/${item.id}`">
                            @{{ item.alias }}
                        </bazar-link>
                    </template>
                </data-column>
                <data-column :label="__('Name')">
                    <template #default="item">
                        @{{ item.name }}
                    </template>
                </data-column>
                <data-column :label="__('Address')">
                    <template #default="item">
                        @{{ item.country_name }}<br>
                        @{{ item.postcode }}, @{{ item.city }}<br>
                        @{{ item.address }}
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
