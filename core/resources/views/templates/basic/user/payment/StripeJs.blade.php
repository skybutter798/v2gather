@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Stripe Storefront')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $data->url }}" method="{{ $data->method }}">
                        <ul class="list-group text-center list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You have to pay '):
                                <strong>{{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You will get '):
                                <strong>{{ showAmount($deposit->amount) }} {{ __($general->cur_text) }}</strong>
                            </li>
                        </ul>
                        <script src="{{ $data->src }}" class="stripe-button"
                            @foreach ($data->val as $key => $value)
                            data-{{ $key }}="{{ $value }}" @endforeach>
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('user.deposit.index') }}" />
@endpush
@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";

            $('button[type="submit"]').text("Pay Now").addClass("btn btn--primary w-100 h-45 mt-3");

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .stripe-button-el {
            background-image: none !important;
        }
    </style>
@endpush
