@extends ('bazar::layout.layout')

{{-- Title --}}
@section ('title', __('Dashboard'))

{{-- Content --}}
@section ('content')
    <div class="row">
        <div class="col-12">
            <widget-metrics class="row"></widget-metrics>
        </div>
        <div class="col-6">
            <widget-activities></widget-activities>
        </div>
        <div class="col-6">
            <widget-sales></widget-sales>
        </div>
    </div>
@endsection
