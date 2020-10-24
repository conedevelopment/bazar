<data-form action="{{ $action }}" {{ $attributes }} :model="{{ $model->toJson() }}">
    <template #default="form">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 form__body">
                {{ $slot }}
            </div>
            <div class="col-12 col-lg-5 col-xl-4 mt-5 mt-lg-0 form__sidebar">
                <div class="sticky-helper">
                    @isset ($aside)
                        {{ $aside }}
                    @endif
                    <card :title="__('Actions')">
                        <div class="form-group d-flex justify-content-between mb-0">
                            @if ($model->exists)
                                <inertia-link href="{{ $action }}" method="DELETE" class="btn btn-outline-danger">
                                    {{ $model->deleted_at ? __('Delete Permanently') : __('Trash') }}
                                </inertia-link>
                            @endif
                            @if ($model->deleted_at)
                                <inertia-link href="{{ $action }}/restore`" method="PATCH" class="btn btn-warning">
                                    {{ __('Restore') }}
                                </inertia-link>
                            @else
                                <button type="submit" class="btn btn-primary">
                                    {{ $model->exists ? __('Update') : __('Save') }}
                                </button>
                            @endif
                        </div>
                    </card>
                </div>
            </div>
        </div>
    </template>
</data-form>
