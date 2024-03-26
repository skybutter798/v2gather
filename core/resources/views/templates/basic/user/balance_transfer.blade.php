@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="alert alert-success p-3">
                                <h5 class="text-center">
                                    @lang(' you available balance'): <span class="text--danger"> {{ showAmount(auth()->user()->balance) }} {{ __($general->cur_text) }}</span>
                                </h5>
                            </div>
                            <div class="alert alert-warning balance_transfer">
                                <strong>@lang('Balance Transfer Charge') {{ getAmount($general->balance_transfer_fixed_charge) }}
                                    {{ __($general->cur_text) }} @lang('Fixed and')
                                    {{ getAmount($general->balance_transfer_percent_charge) }}
                                    % @lang('of your total amount to transfer balance.')</strong>
                            </div>
                            <div class="alert alert-success balance_transfer d-none">
                                <p id="after-balance"></p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <form method="POST" action="">
                                @csrf
                                <div class="form-group">
                                    <label> @lang('Username / Email To Send Amount') </label>
                                    <input type="text" class="form-control form--control" name="username" required autocomplete="off">
                                    <span id="position-test"></span>
                                </div>
                                <div class="form-group">
                                    <label> @lang('Transfer Amount') </label>
                                    <input onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="form-control form--control" autocomplete="off" name="amount" required>
                                    <span id="balance-message"></span>
                                </div>
                                <div class="form-group ">
                                    <button type="submit" class=" btn h-45 w-100 btn--primary mr-2">@lang('Transfer Balance')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            $('input[name=username]').on('blur', function() {
                let username = $(this).val();
                var token = "{{ csrf_token() }}";
                if (username) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('user.search') }}",
                        data: {
                            'username': username,
                            '_token': token
                        },
                        success: function(data) {
                            if (data.success) {
                                $('#position-test').html(
                                    '<div class="text--success mt-2">@lang('User found')</div>')
                            } else {
                                $('#position-test').html(
                                    '<div class="text--danger mt-2">@lang('User not found')</div>');
                            }
                        }
                    });
                } else {
                    $('#position-test').html('');
                }
            })
            $('input[name="amount"]').on('blur', function() {
                let amount = parseFloat($(this).val());
                let balance = parseFloat("{{ auth()->user()->balance }}");
                let fixed_charge = parseFloat("{{ $general->balance_transfer_fixed_charge + 0 }}");
                let percent_charge = parseFloat("{{ $general->balance_transfer_percent_charge + 0 }}");
                let percent = (amount * percent_charge) / 100;
                let with_charge = amount + fixed_charge + percent;
                if (with_charge > balance) {
                    $('.alert-success').removeClass('d-none');
                    $('#after-balance').html('<p  class="text--danger">' + with_charge +
                        ' {{ $general->cur_text }} ' + ` @lang('will be subtracted from your balance') ` +
                        '</p>');
                    $('#balance-message').html('<small class="text--danger">Insufficient Balance!</small>');
                } else if (with_charge <= balance) {
                    $('.alert-success').removeClass('d-none');
                    $('#after-balance').html('<p class="text--danger">' + with_charge +
                        ' {{ $general->cur_text }} ' +
                        ` @lang('will be subtracted from your balance')` + '</p>');
                    $('#balance-message').html('');
                }
            })
        })(jQuery)
    </script>
@endpush
