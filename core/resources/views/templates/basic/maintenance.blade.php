@extends($activeTemplate . 'layouts.frontend')
@section('panel')
    @php
        $maintenanceContent = getContent('maintenance_mode.content', true);
    @endphp
    <section class="maintenance-page d-flex align-items-center justify-content-center" style="background-color: black;">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-7 text-center">
                    <div class="row justify-content-center">
                        <div class="col-xl-10">
                            <h3 class="text--white">{{ __(@$maintenanceContent->data_values->heading) }}</h3>
                        </div>
                        <div class="col-sm-6 col-8 col-lg-12">
                            <img src="https://v2gather.org/assets/images/logoIcon/logo.png" width="300px"
                                alt="@lang('image')" class="img-fluid mx-auto mb-3">
                        </div>
                    </div>
                    <p class="mx-auto text-center text-white">@php echo $maintenanceContent->data_values->description @endphp</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
<style>
.maintenance-page{
    min-height: 100vh;
}
</style>
@endpush
