<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0">
                        <a class="site-logo site-title" href="{{ route('home') }}">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo">
                        </a>
                        @if ($general->multi_language)
                            <div class="language-select-area d-block d-lg-none ms-auto">
                                <select class="language-select langSel">
                                    @foreach ($language as $item)
                                        <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarContent">
                            <ul class="navbar-nav main-menu ms-auto me-4">
                                <li>
                                    <a href="{{ route('home') }}" class="{{ menuActive('home') }}">@lang('Home')</a>
                                </li>
                                @foreach ($pages as $k => $data)
                                    <li><a href="{{ route('pages', [$data->slug]) }}" class="{{ menuActive('pages', null, $data->slug) }} ">{{ trans($data->name) }}</a></li>
                                @endforeach
                                <li><a href="{{ route('blog') }}" class="{{ menuActive('blog') }}">@lang('Blog')</a></li>
                                <li><a href="{{ route('contact') }}" class="{{ menuActive('contact') }}">@lang('Contact')</a></li>
                            </ul>
                            @if ($general->multi_language)
                                <div class="language-select-area d-none d-lg-block">
                                    <select class="language-select langSel">
                                        @foreach ($language as $item)
                                            <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="header-action tab">
                                @guest
                                    <a href="{{ route('user.register') }}" class="btn--base">@lang('Register')</a>
                                    <a href="{{ route('user.login') }}" class="btn--base active">@lang('Login')</a>
                                @else
                                    <a href="{{ route('user.home') }}" class="btn--base active">@lang('Dashboard')</a>

                                    <a href="{{ route('user.logout') }}" class="btn--base ">@lang('Logout')</a>
                                @endguest
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
