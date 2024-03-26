@php
    $content = getContent('breadcrumb.content', true);
@endphp

<section class="inner-banner-section banner-section bg_img"
    data-background="{{ getImage('assets/images/frontend/breadcrumb/' . @$content->data_values->background_image, '1920x1024') }}">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 text-center">
                <div class="banner-content">
                    <h1 class="title text-white">{{ __($pageTitle) }}</h1>
                    <div class="breadcrumb-area">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>

                                <li class="breadcrumb-item active" aria-current="page">{{ __($pageTitle) }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
