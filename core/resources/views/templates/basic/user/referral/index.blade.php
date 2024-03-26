@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 mb-5">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">@lang('Join left')</label>
                        <div class="input-group">
                            <input type="text" name="key" value="{{ route('user.register') }}?ref={{ auth()->user()->username }}&position=left" class="form-control form--control leftRefURL" readonly>
                            <button type="button" class="input-group-text copytext btn btn--primary" data-url="leftRefURL">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">@lang('Join right')</label>
                        <div class="input-group">
                            <input type="text" name="key" value="{{ route('user.register') }}?ref={{ auth()->user()->username }}&position=right" class="form-control form--control rigtRefURL" readonly>
                            <button type="button" class="input-group-text copytext btn btn--primary" data-url="rigtRefURL">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card  b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Join Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($referrals as $referral)
                                    <tr>
                                        <td> {{ $loop->index + 1 }}</td>
                                        <td>{{ $referral->username }}</td>
                                        <td>{{ $referral->fullname }} </td>
                                        <td> {{ $referral->email }}</td>
                                        <td>{{ diffForHumans($referral->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($referrals->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($referrals) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>


    </div>
@endsection


@push('style')
    <style>
        .copied::after {
            background-color: #4634FF !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.copytext').click(function() {
                let url = $(this).data('url')
                var copyText = document.getElementsByClassName(url);
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
            });
        })(jQuery);
    </script>
@endpush
