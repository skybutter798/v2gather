@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        @foreach ($plans as $plan)
            <div class="col-xl-4 col-md-6 mb-30">
                <div class="card">
                    <div class="card-body pt-5 pb-5 ">
                        <div class=" text-center mb-4">
                            <h2 class="package-name mb-20 text-"><strong>{{ __($plan->name) }}</strong></h2>
                            <span class="price text--dark font-weight-bold d-block">
                                {{ $general->cur_sym }}{{ showAmount($plan->price) }}
                            </span>
                            <hr>
                            <ul class="plan-card-items mt-30">
                                <li class="justify-content-between plan-card-item d-flex">
                                    <div>
                                        <i class="fas fa-check bg--success"></i>
                                        <span>@lang('app.Business Volume (PB)'):
                                            {{ getAmount($plan->bv) }}
                                        </span>
                                    </div>
                                    <span class="plan-icon" data-title="@lang('app.Business Volume (BV) info')" data-info="@lang('app.When someone from your below tree subscribe this plan, You will get this Business Volume  which will be used for matching bonus')">
                                        <i class="fas fa-question-circle"></i>
                                    </span>
                                </li>
                                <li class="justify-content-between plan-card-item d-flex">
                                    <div>
                                        <i class="fas fa-check bg--success"></i>
                                        <span> @lang('Referral Commission'):
                                            {{ $general->cur_sym }}{{ showAmount($plan->ref_com) }}
                                        </span>
                                    </div>
                                    <span class="plan-icon" data-title="@lang('Referral Commission')" data-info="@lang('app.When your referred user subscribe in PLAN, you will get this amount.')">
                                        <i class="fas fa-question-circle"></i>
                                    </span>
                                </li>
                                {{--<li class="justify-content-between plan-card-item d-flex">
                                    <div>
                                        <i class="fas @if (getAmount($plan->tree_com) != 0) fa-check bg--success @else fa-times bg--danger @endif "></i>
                                        <span>
                                            @lang('Tree Commission'): {{ $general->cur_sym }}{{ showAmount($plan->tree_com) }}
                                        </span>

                                    </div>
                                    <span class="plan-icon" data-title="@lang('Commission to tree info')" data-info="@lang('When someone from your below tree subscribe this plan, You will get this amount as tree commission')">

                                        <i class="fas fa-question-circle"></i>
                                    </span>
                                </li>
                                <li class="justify-content-between plan-card-item d-flex">
                                    <div>
                                        <i class="fas fa-check bg--success"></i>
                                        <span>
                                            @lang('Daily Ad View Limit'): {{ $plan->daily_ad_limit }}
                                        </span>
                                    </div>
                                    <span class="plan-icon" data-title="@lang('Daily Ad Limit Info')" data-info="@lang('How many ad you can view in a day')">
                                        <i class="fas fa-question-circle"></i>
                                    </span>
                                </li>--}}
                            </ul>
                        </div>
                        @if (auth()->user()->plan_id != $plan->id)
                            <button class="btn w-100 btn-outline--primary h-45 buyBtn" data-plan="{{ $plan }}">
                                @lang('Subscribe')
                            </button>
                        @else
                            <button class="btn w-100 btn--secondary h-45 already_subscribe" disabled>
                                @lang('Already Subscribe')
                            </button>
                        @endif
                    </div>

                </div><!-- card end -->
            </div>
        @endforeach
        
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card">
                <div class="card-body pt-5 pb-5 ">
                    <div class=" text-center mb-4">
                        <h2 class="package-name mb-20 text-"><strong>V300</strong></h2>
                        <span class="price text--dark font-weight-bold d-block">
                            $300.00
                        </span>
                        <hr>
                        <ul class="plan-card-items mt-30">
                            <li class="justify-content-between plan-card-item d-flex">
                                <div>
                                    <i class="fas fa-check bg--success"></i>
                                    <span>@lang('app.Business Volume (PB)'): 300</span>
                                </div>
                                <span class="plan-icon" data-title="@lang('app.Business Volume (BV) info')" data-info="@lang('app.When someone from your below tree subscribe this plan, You will get this Business Volume  which will be used for matching bonus')">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                            </li>
                            <li class="justify-content-between plan-card-item d-flex">
                                <div>
                                    <i class="fas fa-check bg--success"></i>
                                    <span>@lang('app.Referral Comission'): $150.00</span>
                                </div>
                                <span class="plan-icon" data-title="@lang('Referral Commission')" data-info="@lang('app.When your referred user subscribe in PLAN, you will get this amount.')">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                            </li>
                        </ul>
                    </div>
                    @if (auth()->user()->plan_3 != 1)
                    <button class="btn w-100 btn-outline--primary h-45 buyBtnv300" data-bs-target="#BuyModalv300">@lang('Subscribe')</button>
                    @else
                        <button class="btn w-100 btn--secondary h-45 already_subscribe" disabled>
                            @lang('Already Subscribe')
                        </button>
                    @endif
                </div>
            </div><!-- card end -->
        </div>
        @if ($plans->hasPages())
            <div class="justify-content-center">
                {{ paginateLinks($plans) }}
            </div>
        @endif
    </div>

    <div class="modal custom--modal fade" id="BuyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="post" action="{{ route('user.plan.subscribe') }}">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <strong class="modal-title"> @lang('Confirmation to purches  ')<span class="planName"></span></strong>

                        <button type="button" class="close btn btn-sm btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <ul class="list-group list-group-flush p-0">
                                <li class="list-group-item d-flex justify-content-between ps-0">
                                    <span class="text-dark">@lang('app.Plan Price')</span>
                                    <span class="price fw-bold"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between ps-0">
                                    <span class="text-dark">@lang('app.Referral Comission')</span>
                                    <span class="refLevel fw-bold"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between ps-0">
                                    <span class="text-dark">@lang('app.Business Volume (BV)')</span>
                                    <span class="paring fw-bold"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group">
                            @if (auth()->user()->plan_id)
                                <code class="d-block">@lang('If you subscribe to this one. Your old limitation will reset according to this package.')</code>
                            @endif
                            <label>@lang('app.Current Balance')</label>
                            <p><strong>V2P: {{ number_format($user->V2P, 2) }} | RP: {{ number_format($user->RP, 2) }} | WP: {{ number_format($user->balance, 2) }}</strong></p>
                            <br>
                            <div class="form-group">
                                <label>@lang('app.Use RP (Max 10% of Plan Price)')</label>
                                <input type="number" class="form-control" name="rp" id="rpInput" placeholder="0" max="{{ $plan->price * 0.1 }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('app.Use V2P (Up to 100% of Plan Price)')</label>
                                <input type="number" class="form-control" name="v2p" id="v2pInput" placeholder="0" required readonly>
                            </div>
                            <input type="hidden" id="planPrice" value="{{ $plan->price }}">

                            <code class="gateway-info rate-info d-none">@lang('Rate'): 1 {{ $general->cur_text }}
                                = <span class="rate"></span> <span class="method_currency"></span></code>
                        </div>
                        <div class="form-group">
                            <label>@lang('app.Invest Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form--control" name="amount" required>
                                <span class="input-group-text text-white bg--primary border-0">{{ $general->cur_text }}</span>
                            </div>
                            <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span>
                                {{ $general->cur_text }}. @lang('Total amount'): <span class="total"></span>
                                {{ $general->cur_text }}</code>
                        </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal" style="color:white">@lang('app.Cancel')</button>
                        <button type="submit" class="btn btn--dark" style="color:white">@lang('app.Submit')</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal custom--modal fade" id="BuyModalv300" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="post" action="{{ route('user.plan.subscribev300') }}">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <strong class="modal-title"> @lang('Confirmation to purches  ')<span class="planName"></span></strong>

                        <button type="button" class="close btn btn-sm btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <ul class="list-group list-group-flush p-0">
                                <li class="list-group-item d-flex justify-content-between ps-0">
                                    <span class="text-dark">@lang('app.Plan Price')</span>
                                    <span class="fw-bold">300.00 USD</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between ps-0">
                                    <span class="text-dark">@lang('app.Referral Comission')</span>
                                    <span class="fw-bold">150.00 USD</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between ps-0">
                                    <span class="text-dark">@lang('app.Business Volume (BV)')</span>
                                    <span class="fw-bold">300 PB</span>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label>@lang('app.Current Balance')</label>
                            <p><strong>V2P: {{ number_format($user->V2P, 2) }} | RP: {{ number_format($user->RP, 2) }} | WP: {{ number_format($user->balance, 2) }}</strong></p>
                            <br>
                            <div class="form-group">
                                <label>@lang('app.Use RP (Max 10% of Plan Price)')</label>
                                <input type="number" class="form-control" name="rp" id="rpInputv300" placeholder="0" max="{{ 300 * 0.1 }}" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('app.Use V2P (Up to 100% of Plan Price)')</label>
                                <input type="number" class="form-control" name="v2p" id="v2pInputv300" placeholder="0" required readonly>
                            </div>
                            <input type="hidden" id="planPrice" value="300">

                            <code class="gateway-info rate-info d-none">@lang('Rate'): 1 {{ $general->cur_text }}
                                = <span class="rate"></span> <span class="method_currency"></span></code>
                        </div>
                        <div class="form-group">
                            <label>@lang('app.Invest Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form--control" name="amount" value="300" required readonly>
                                <span class="input-group-text text-white bg--primary border-0">{{ $general->cur_text }}</span>
                            </div>
                            <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span>
                                {{ $general->cur_text }}. @lang('Total amount'): <span class="total"></span>
                                {{ $general->cur_text }}</code>
                        </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal" style="color:white">@lang('app.Cancel')</button>
                        <button type="submit" class="btn btn--dark" style="color:white">@lang('app.Submit')</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

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
                    <p></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('user.home') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            $('nav').removeClass('justify-content-end').addClass('justify-content-center');
            $('.plan-icon').click(function(e) {
                let modal = $('.infoModal');
                let title = $(this).data('title');
                let info = $(this).data('info');
                modal.modal('show');
                modal.find('.modal-title').html(title)
                modal.find('.modal-body p').html(info);
            });
            
            $('.buyBtnv300').click(function() {
                $('#rpInputv300').val(0);
                $('#v2pInputv300').val(300);
            });
            
            $('.buyBtn').click(function() {

                let symbol = '{{ $general->cur_sym }}';
                let currency = '{{ $general->cur_text }}';
                $('.gateway-info').addClass('d-none');
                let modal = $('#BuyModal');
                let plan = $(this).data('plan')
                
                modal.find('.planName').text(plan.name)
                modal.find('[name=id]').val(plan.id)
                let planPrice = parseFloat(plan.price).toFixed(2);
                
                $('#rpInput').val(0);
                $('#v2pInput').val(planPrice);
                
                $('#planPrice').val(planPrice);
                $('#v2pInput').attr('max', planPrice);
                
                modal.find('[name=amount]').val(planPrice);
                modal.find('[name=amount]').attr('readonly', true);

                modal.find('.dailyLimit').html(`${plan.daily_ad_limit}`)
                modal.find('.refLevel').html(`${parseFloat(plan.ref_com).toFixed(2)} {{ $general->cur_text }}`)
                modal.find('.price').html(`${parseFloat(plan.price).toFixed(2)} {{ $general->cur_text }}`)
                modal.find('.paring').html(`${parseFloat(plan.bv).toFixed(2)} PB`)
                modal.find('.validity').html(`${parseFloat(plan.tree_com).toFixed(2)} {{ $general->cur_text }}`)

                $('[name=amount]').on('input', function() {
                    $('[name=wallet_type]').trigger('change');
                })

                $('[name=wallet_type]').change(function() {
                    var amount = $('[name=amount]').val();
                    if ($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' && amount) {
                        var resource = $('select[name=wallet_type] option:selected').data('gateway');
                        var fixed_charge = parseFloat(resource.fixed_charge);
                        var percent_charge = parseFloat(resource.percent_charge);
                        var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                        $('.charge').text(charge);
                        $('.rate').text(parseFloat(resource.rate));
                        $('.gateway-info').removeClass('d-none');
                        if (resource.currency == '{{ $general->cur_text }}') {
                            $('.rate-info').addClass('d-none');
                        } else {
                            $('.rate-info').removeClass('d-none');
                        }
                        $('.method_currency').text(resource.currency);
                        $('.total').text(parseFloat(charge) + parseFloat(amount));
                    } else {
                        $('.gateway-info').addClass('d-none');
                    }
                });
                modal.find('input[name=id]').val(plan.id);
                modal.modal('show');
            });
        })(jQuery);
    </script>
    <script>
    document.getElementById('rpInput').addEventListener('input', function(e) {
        let rpValue = parseFloat(e.target.value) || 0;
        let planPrice = parseFloat(document.getElementById('planPrice').value);
        let maxRp = planPrice * 0.1;
        let remainingForV2P = planPrice - rpValue;
        
        // Ensure RP does not exceed 10% of the plan price
        if (rpValue > maxRp) {
            rpValue = maxRp;
            e.target.value = rpValue;
        }
    
        // Adjust V2P input
        document.getElementById('v2pInput').value = remainingForV2P > 0 ? remainingForV2P : 0;
    });
    
    document.getElementById('rpInputv300').addEventListener('input', function(e) {
        let rpValue = parseFloat(e.target.value) || 0;
        let planPrice = parseFloat(300);
        let maxRp = planPrice * 0.1;
        let remainingForV2P = planPrice - rpValue;
        
        // Ensure RP does not exceed 10% of the plan price
        if (rpValue > maxRp) {
            rpValue = maxRp;
            e.target.value = rpValue;
        }
    
        // Adjust V2P input
        document.getElementById('v2pInputv300').value = remainingForV2P > 0 ? remainingForV2P : 0;
    });
    </script>
    
    <script>
    $(document).ready(function() {
        // Listen for clicks on buttons that have a 'data-bs-target'
        $('[data-bs-target]').click(function() {
            var target = $(this).attr('data-bs-target');
            // Use Bootstrap's modal method to show the modal
            $(target).modal('show');
        });
    });
    </script>


@endpush
