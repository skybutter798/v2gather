@php
    $aboutCaption = getContent('about.content',true);
@endphp
<section class="about-section ptb-80">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-lg-5 col-md-6 mb-30">
                <div class="about-thumb">
                    <img src="{{ getImage('assets/images/frontend/about/'.@$aboutCaption->data_values->background_image, '920x600') }}" alt="about">
                </div>
            </div>
            <div class="col-lg-7 col-md-6 mb-30">
                <div class="about-area">
                    <div class="section-header">
                        <h3 class="sub-title">{{ __(@$aboutCaption->data_values->sub_heading) }}</h3>
                        <h2 class="section-title">{{ __(@$aboutCaption->data_values->heading) }}</h2>
                        <span class="title-border bg_img" data-background="{{asset($activeTemplateTrue . 'images/icon-title.png')}}"></span>
                    </div>
                    <p>
                        {{ __(@$aboutCaption->data_values->description) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>