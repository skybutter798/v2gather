@php
    $serviceContent = getContent('service.content', true);
    $serviceElement = getContent('service.element', orderById: true);
@endphp
<section class="service-section bg--gray ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <div class="section-header">
                    <h3 class="sub-title">{{ __(@$serviceContent->data_values->sub_heading) }}</h3>
                    <h2 class="section-title">{{ __(@$serviceContent->data_values->heading) }}</h2>
                    <span class="title-border bg_img" data-background="{{ asset($activeTemplateTrue . 'images/icon-title.png') }}"></span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($serviceElement as $service)
                <div class="col-lg-4 col-md-6 col-sm-8 mb-30">
                    <div class="service-item">
                        <div class="service-body d-flex flex-wrap align-items-center">
                            <div class="service-icon">
                                @php
                                    echo @$service->data_values->icon;
                                @endphp

                            </div>
                            <div class="service-content">
                                <h3 class="title">{{ __(@$service->data_values->title) }}</h3>
                                <p>{{ __(@$service->data_values->description) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
