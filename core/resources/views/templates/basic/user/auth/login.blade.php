@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php
        $LoginContent = getContent('login.content', true);
    @endphp
    <section class="account-section bg_img" data-background="{{ getImage('assets/images/frontend/login/' . @$LoginContent->data_values->background_image, '1920x700') }}">
        <div class="container">
            <div class="row account-row align-items-center justify-content-center">
                <div class="col-lg-5">
                    <div class="account-form-area">
                        <div class="account-logo-area text-center">
                            <div class="account-logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo">
                                </a>
                            </div>
                        </div>
                        <div class="account-header text-center">
                            <h2 class="title">{{ __(@$content->data_values->title) }}</h2>
                            <h3 class="sub-title">
                                @lang('Don\'t have an account?')
                                <a href="{{ route('user.register') }}" class="ms-2 text--danger">@lang('Create An Account')</a>
                            </h3>
                        </div>

                        <form class="account-form verify-gcaptcha" method="post" action="{{ route('user.login') }}">
                            @csrf
                            <div class="row ml-b-20">
                                <div class="form-group ">
                                    <label>@lang('Email or Username')</label>
                                    <input type="text" class="form-control form--control" name="username" value="{{ old('username') }}" required>
                                </div>

                                <div class="form-group ">
                                    <label>@lang('Password')</label>
                                    <input type="password" class="form-control form--control" name="password" required>
                                </div>
                                <x-captcha />
                                <div class="mb-2">
                                    <a class="text--danger" href="{{ route('user.password.request') }}">@lang('Forgot Password?')</a>
                                </div>
                                <div class=" form-group text-center">
                                    <button type="submit" class="btn btn--base h-45 w-100">@lang('Login')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
