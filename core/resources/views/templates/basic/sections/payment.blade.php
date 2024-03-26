@php
    $paymentContent = getContent('payment.content', true);
    $paymentElement = getContent('payment.element', orderById: true);
@endphp
<section class="brand-section bg--gray ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <div class="section-header">
                    <h3 class="sub-title">{{ __(@$paymentContent->data_values->heading) }}</h3>
                    <h2 class="section-title">{{ __(@$paymentContent->data_values->subheading) }}</h2>
                    <span class="title-border bg_img" data-background="{{ asset($activeTemplateTrue . 'images/icon-title.png') }}"></span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="brand-slider">
                    <div class="swiper-wrapper">
                        @foreach ($paymentElement as $payment)
                            <div class="swiper-slide">
                                <div class="brand-item">
                                    <img src="{{ getImage('assets/images/frontend/payment/' . @$payment->data_values->image, '120x120') }}" alt="{{ __(@$payment->data_values->name) }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
