<div class="sidebar bg--white">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('user.home') }}" class="sidebar__main-logo">
                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo" style="width: 70%; max-height: 500px !important;">
            </a>
        </div>
        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('user.home') }}">
                    <a href="{{ route('user.home') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('app.Dashboard')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('user.plan.index') }}">
                    <a href="{{ route('user.plan.index') }}" class="nav-link ">
                        <i class="menu-icon las la-paper-plane"></i>
                        <span class="menu-title">@lang('app.Plan')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    {{--<a href="javascript:void(0)" class="{{ menuActive('user.ptc*', 3) }} my-2">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('app.PTC')</span>
                    </a>--}}
                    <div class="sidebar-submenu {{ menuActive('user.ptc*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.ptc.index') }} ">
                                <a href="{{ route('user.ptc.index') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Ads')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.ptc.clicks') }}">
                                <a href="{{ route('user.ptc.clicks') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Clicks')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('user.plan.bvLog') }}">
                    <a href="{{ route('user.plan.bvLog') }}" class="nav-link">
                        <i class="menu-icon las la-sitemap"></i>
                        <span class="menu-title">@lang('app.BV Log')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('user.referral') }}">
                    <a href="{{ route('user.referral') }}" class="nav-link">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('app.My Referrals')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('user.myTree') }}">
                    <a href="{{ route('user.myTree') }}" class="nav-link">
                        <i class="menu-icon las la-tree"></i>
                        <span class="menu-title">@lang('app.V100 Tree')</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item {{ menuActive('user.v300') }}">
                    <a href="{{ route('user.v300') }}" class="nav-link">
                        <i class="menu-icon las la-tree"></i>
                        <span class="menu-title">@lang('app.V300 Tree')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('user.binary.summary') }}">
                    <a href="{{ route('user.binary.summary') }}" class="nav-link">
                        <i class=" menu-icon las la-chart-area"></i>
                        <span class="menu-title">@lang('app.Binary Summary')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('user.deposit.history') }}">
                    <a href="{{ route('user.deposit.history') }}" class="nav-link">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title">@lang('app.Deposit')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive(['user.withdraw', 'user.withdraw.preview']) }}">
                    <a href="{{ route('user.withdraw') }}" class="nav-link">
                        <i class="menu-icon las la-university"></i>
                        <span class="menu-title">@lang('app.Withdrawals')</span>
                    </a>
                </li>

                {{--<li class="sidebar-menu-item {{ menuActive('user.balance.transfer') }}">
                    <a href="{{ route('user.balance.transfer') }}" class="nav-link">
                        <i class="menu-icon las la-hand-holding-usd"></i>
                        <span class="menu-title">@lang('app.Balance Transfer')</span>
                    </a>
                </li>--}}

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)"
                        class="{{ menuActive(['user.transactions', 'user.deposit.history', 'user.withdraw.history', 'user.invest', 'user.refCom', 'user.binaryCom'], 3) }} my-2">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('app.Reports') / @lang('app.Logs')</span>
                    </a>
                    <div
                        class="sidebar-submenu {{ menuActive(['user.transactions', 'user.deposit.history', 'user.withdraw.history', 'user.invest', 'user.refCom', 'user.binaryCom'], 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.transactions') }} ">
                                <a href="{{ route('user.transactions') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Transactions Log')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.withdraw.history') }}">
                                <a href="{{ route('user.withdraw.history') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Withdraw Log')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.invest') }}">
                                <a href="{{ route('user.invest') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Invest Log')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.refCom') }}">
                                <a href="{{ route('user.refCom') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Referral Commissions')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.binaryCom') }}">
                                <a href="{{ route('user.binaryCom') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('app.Binary Commission')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                {{--<li class="sidebar-menu-item {{ menuActive('user.twofactor') }}">
                    <a href="{{ route('user.twofactor') }}" class="nav-link">
                        <i class="menu-icon las la-shield-alt"></i>
                        <span class="menu-title">@lang('app.2FA Security')</span>
                    </a>
                </li>--}}
                <li class="sidebar-menu-item {{ menuActive('ticket*') }}">
                    <a href="{{ route('ticket.index') }}" class="nav-link">
                        <i class="menu-icon las la-ticket-alt"></i>
                        <span class="menu-title">@lang('app.Support')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive(['user.profile.setting', 'user.change.password']) }}">
                    <a href="{{ route('user.profile.setting') }}" class="nav-link">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('app.Profile')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('user.login.history') }}">
                    <a href="{{ route('user.login.history') }}" class="nav-link">
                        <i class="menu-icon las la-history"></i>
                        <span class="menu-title">@lang('app.Login History')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('user.logout') }}" class="nav-link">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">@lang('app.Logout')</span>
                    </a>
                </li>
            </ul>
            {{--<div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('app.V'){{ systemDetails()['version'] }} </span>
            </div>--}}
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
