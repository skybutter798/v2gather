@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contact = getContent('contact_us.content', true);
    @endphp
    <section class="contact-section ptb-80 bg--gray bg_img" data-background="{{ getImage('assets/images/frontend/contact_us/' . @$contact->data_values->background_image, '1920x820') }}">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-lg-4 mb-30">
                    <div class="contact-info-item-area mb-40-none">
                        <div class="contact-info-item d-flex flex-wrap mb-40">
                            <div class="contact-info-icon">
                                <i class="fas fa fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h3 class="title">@lang('Office Address')</h3>
                                <p>{{ @$contact->data_values->contact_details }}</p>
                            </div>
                        </div>
                        <div class="contact-info-item d-flex flex-wrap mb-40">
                            <div class="contact-info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h3 class="title">@lang('Email Address')</h3>
                                <p>
                                    <a href="mailto:{{ @$contact->data_values->email_address }}">{{ @$contact->data_values->email_address }}</a>
                                </p>
                            </div>
                        </div>
                        <div class="contact-info-item d-flex flex-wrap mb-40">
                            <div class="contact-info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h3 class="title">@lang('Phone Number')</h3>
                                <p><a href="Tel:{{ @$contact->data_values->contact_number }}">{{ @$contact->data_values->contact_number }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-30">
                    <div class="contact-form-area">
                        <form method="post" action="" class="contact-form verify-gcaptcha">
                            @csrf
                            @php
                                $user=auth()->user();
                            @endphp
                            <div class="row">
                                <div class="col-lg-6 form-group">
                                    <input type="text" name="name" class="form-control form--control" value="{{ old('name',@$user->fullname) }}" @readonly($user) placeholder="@lang('Your name')" required>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <input type="email" name="email" class="form-control form--control" value="{{ old('email',@$user->email) }}" @readonly($user)  required placeholder="@lang('Enter email address')" >
                                </div>
                                <div class="col-lg-12 form-group">
                                    <input type="text" name="subject" class="form-control form--control " placeholder="@lang('Write your subject')" value="{{ old('subject') }}" required>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <textarea class="form-control form--control" placeholder="@lang('Write your message')" name="message" required>{{ old('message') }}</textarea>
                                </div>

                                <div class="col-lg-12">
                                    <x-captcha />
                                </div>

                                <div class="col-lg-12 form-group">
                                    <button type="submit" class="btn btn--base h-45 w-100">@lang('Send Message')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="maps">
        <iframe src="{{ @$contact->data_values->map_url }}" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let labelText = $("label[for='captcha']").text();
            $('[name=captcha]').attr('placeholder', labelText)

        });
    </script>
@endpush

@push('style')
    <style>
        iframe {
            width: 100% !important;
            height: 100%;
        }

        label[for="captcha"] {
            display: none
        }
    </style>
@endpush
