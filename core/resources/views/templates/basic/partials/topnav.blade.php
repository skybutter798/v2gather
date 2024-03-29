<!-- navbar-wrapper start -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Qt9Hug5NfnQDGMoaQYXN1+PiQvda7poO7/6kT8Fk6rC3g3h3qGjw2nA3cQx3uV3F" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js" integrity="sha384-kQtW33rZJAHj6NBkG9nE2BNT1tpP9OgkV2zL+5RLT6IdEU4jHAJdYmbL/XWC/v+R" crossorigin="anonymous"></script>

<!-- Optional JavaScript; choose one of the two! -->
<nav class="navbar-wrapper bg--white">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                placeholder="@lang('Search here...')">
            <i class="las la-search"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    
    <div class="" style="padding-left:10px">
    <a href="{{ route('language.switch', ['lang' => 'en']) }}" style="display: inline-block; padding: 8px 12px; background-color: #00a1ff; color: white; text-align: center; text-decoration: none; border-radius: 5px;">English</a>
    <a href="{{ route('language.switch', ['lang' => 'cn']) }}" style="display: inline-block; padding: 8px 12px; background-color: #00a1ff; color: white; text-align: center; text-decoration: none; border-radius: 5px;">中文</a>
    </div>
    
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}"
                                alt="@lang('image')">
                        </span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('user.profile.setting') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>
                    <a href="{{ route('user.change.password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a href="{{ route('user.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
