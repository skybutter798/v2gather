@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php
        $content = getContent('register.content', true);
        $policyPages = getContent('policy_pages.element', orderById: true);
    @endphp
    <section class="account-section ptb-80 bg_img" data-background="{{ getImage('assets/images/frontend/register/' . @$content->data_values->background_image, '1920x390') }}">
        <div class="container">
            <div class="row account-row align-items-center justify-content-center">
                <div class="col-lg-7">
                    <div class="account-form-area">
                        <div class="account-logo-area text-center">
                            <div class="account-logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo">
                                </a>
                            </div>
                        </div>
                        <div class="account-header text-center">
                            <h2 class="title">{{ __(@$content->data_values->heading) }}</h2>
                            <h3 class="sub-title">
                                @lang('Already have an account')?
                                <a href="{{ route('user.login') }}" class="text--danger">
                                    @lang('Login Now')
                                </a>
                            </h3>
                        </div>
                        <form class="account-form verify-gcaptcha" method="post" action="{{ route('user.register') }}">
                            @csrf
                            <div class="row ">
                                @if ($refUser == null)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Referral Username')</label>
                                            <input class="referral ref_id form--control form-control" name="referral" type="text" value="{{ old('referral') }}" autocomplete="off" required>
                                            <div id="ref"></div>
                                            <span id="referral"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Position')</label>
                                            <select class="position form--control " id="position" name="position" required disabled>
                                                <option value="">@lang('Select position')</option>
                                                @foreach (mlmPositions() as $k => $v)
                                                    <option value="{{ $k }}">{{ __($v) }}</option>
                                                @endforeach
                                            </select>
                                            <span id="position-test" class="text--danger mt-2 d-none">
                                                @lang('Please enter referral username first')
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Referral Username')</label>
                                            <input class="referral form--control form-control" name="referral" type="text" value="{{ $refUser->username }}" required readonly>
                                        </div>
                                        <input name="referrer_id" type="hidden" value="{{ $refUser->id }}">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Position')</label>
                                            <select class="position form--control form-control" id="position" required disabled>
                                                <option value="">@lang('Select position')*</option>
                                                @foreach (mlmPositions() as $k => $v)
                                                    <option value="{{ $k }}" @if ($pos == $k) selected @endif>{{ __($v) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input name="position" type="hidden" value="{{ $pos }}">
                                            <strong class='text--success'>@lang('Your are joining under') {{ $joining }}
                                                @lang('at')
                                                {{ $position }} </strong>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Username')</label>
                                        <input class="checkUser form--control form-control" name="username" type="text" value="{{ old('username') }}" required>
                                        <small class="text--danger usernameExist"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Email')</label>
                                        <input class="checkUser form--control form-control" name="email" type="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Country')</label>
                                        <select name="country" class="form--control" required>
                                            @foreach ($countries as $key => $country)
                                                <option data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $country->country }}">
                                                    {{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Mobile')</label>
                                        <div class="input-group">
                                            <span class="input-group-text mobile-code border-0 bg--base" style="color:black">

                                            </span>
                                            <input name="mobile_code" type="hidden">
                                            <input name="country_code" type="hidden">
                                            <input class="form-control form--control" name="mobile" type="number" value="{{ old('mobile') }}"  required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Password')</label>
                                        <input name="password" type="password" class="form--control" required>
                                        @if ($general->secure_password)
                                            <div class="input-popup">
                                                <p class="error lower">@lang('1 small letter minimum')</p>
                                                <p class="error capital">@lang('1 capital letter minimum')</p>
                                                <p class="error number">@lang('1 number minimum')</p>
                                                <p class="error special">@lang('1 special character minimum')</p>
                                                <p class="error minimum">@lang('6 character password')</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Confirm password')</label>
                                        <input name="password_confirmation" type="password" class="form--control" required>
                                    </div>
                                </div>
                                <x-captcha />
                                @if ($general->agree)
                                    <div class="col-lg-12 form-group">
                                        <div class="checkbox-wrapper d-flex flex-wrap align-items-center">
                                            <div class="checkbox-item">
                                                <input type="checkbox" id="c1" name="agree" required>
                                                <label for="c1">@lang('I agree with')
                                                    @foreach ($policyPages as $policy)
                                                        <a target="_blank" href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" class="text--danger" style="color: black !important;">{{ __($policy->data_values->title) }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn--base w-100 h-45">@lang('Create an Account')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h3>
                        <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                    </div>
                    <div class="modal-body">
                        <h4 class="text-center">@lang('You already have an account please Login ')</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                        <a href="{{ route('user.login') }}" class="btn btn--base btn-sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if ($general->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
@push('script')
    <script>
        "use strict";

        (function($) {
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            @if ($general->secure_password)
                $('input[name=password]').on('input', function() {
                    secure_password($(this));
                });
            @endif

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });

            var not_select_msg = $('#position-test').html();
            var positionDetails = null;

            $(document).on('focusout', '.ref_id', function() {
                var ref_id = $(this).val();
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: "POST",
                    url: "{{ route('check.referral') }}",
                    data: {
                        'ref_id': ref_id,
                        '_token': token
                    },
                    success: function(data) {
                        if (data.success) {
                            $('select[name=position]').removeAttr('disabled');
                            $('#position-test').text('');
                            $("#ref").html('<span class="mt-2 text--success fw-bold">Referrer username matched</span>');
                            $('#position-test').removeClass('d-none');

                        } else {
                            $('select[name=position]').attr('disabled', true);
                            $('#position-test').html(not_select_msg);
                            $("#ref").html('<span class="mt-2 text--danger fw-bold">Referrer username not found</span>');
                        }
                        positionDetails = data;
                        updateHand();
                    }
                });
            });

            $(document).on('change', '#position', function() {
                updateHand();
            });

            function updateHand() {
                var pos = $('#position').val(),
                    className = null,
                    text = null;
                if (pos && positionDetails.success == true) {
                    className = 'text--success';
                    text =
                        `<span class="help-block"><strong class="text--success">Your are joining under ${positionDetails.position[pos]} at position ${pos == 1 ? 'left' : 'right'} <strong></span>`;
                } else {
                    className = 'text--danger';
                    if (positionDetails.success == true) text = 'Select your position';
                    else if ($('.ref_id').val()) text = `Please enter a valid referral username`;
                    else text = `Enter referral username first`;
                }
                $("#position-test").html(`<span class="help-block"><strong class="${className}">${text}</strong></span>`)
            }
            @if (old('position'))
                $(`select[name=position]`).val('{{ old('position') }}');
                $(`select[name=referral]`).change();
            @endif
        })(jQuery);
    </script>
@endpush
