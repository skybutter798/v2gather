@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="plan-section bg--gray ptb-80" id="plan">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                @foreach ($plans as $plan)
                    <div class="col-xl-4  col-md-6 mb-30">
                        <div class="plan-item">
                            <div class="plan-header section--bg text-center">
                                <h3 class="title text-white">{{ __(@$plan->name) }}</h3>
                                <div class="plan-price">

                                    <h3 class="price"><span>{{ $general->cur_sym }}</span>{{ showAmount($plan->price) }}
                                    </h3>
                                </div>
                            </div>
                            <div class="plan-body text-center">
                                <ul class="plan-list">
                                    <li class="d-flex  justify-content-between">
                                        <div>
                                            @lang('Business Volume') (@lang('BV')) :
                                            <span class="amount">{{ $plan->bv }}</span>
                                        </div>
                                        <span class="icon plan_modal" data-title="@lang('Business Volume (BV) info')" data-info="@lang('When someone from your below tree subscribe this plan, You will get this Business Volume  which will be used for matching bonus')">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            @lang('Referral Commission') : <span class="amount">{{ $general->cur_sym }}{{ showAmount($plan->ref_com) }}</span>
                                        </div>
                                        <span class="icon plan_modal" data-title="@lang('Referral Commission info')" data-info="@lang('When your referred user subscribe in') <b> @lang('ANY PLAN')</b>, @lang('you will get this amount'). ">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            @lang('Commission To Tree') : <span class="amount">{{ $general->cur_sym }}{{ showAmount($plan->tree_com) }}</span>
                                        </div>
                                        <span class="icon plan_modal" data-title="@lang('Commission to tree info')" data-info="@lang('When someone from your below tree subscribe this plan, You will get this amount as tree commission')">

                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            @lang('Daily Ad Limit') : <span class="amount">{{ $plan->daily_ad_limit }}</span>
                                        </div>
                                        <span class="icon plan_modal" data-title="@lang('Daily Ad Limit Info')" data-info="@lang('How many ad you can view in a day')">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                    </li>
                                </ul>
                                <div class="plan-btn text-center mt-30">
                                    <a href="{{ route('user.plan.index') }}" class="btn--base w-100">@lang('Subscribe Now')</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="modal fade infoModal">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text--danger"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        span.icon {
            cursor: pointer;
        }
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            $('.plan_modal').on('click', function() {
                let modal = $('.infoModal');
                let title = $(this).data('title');
                let info = $(this).data('info');
                modal.modal('show');
                modal.find('.modal-title').html(title)
                modal.find('.modal-body p').html(info);
            })
        })(jQuery)
    </script>
@endpush
