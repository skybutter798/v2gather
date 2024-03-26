@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required
                                        value="{{ $general->site_name }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" type="text" name="cur_text" required
                                        value="{{ $general->cur_text }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" type="text" name="cur_sym" required
                                        value="{{ $general->cur_sym }}">
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Timezone')</label>
                                <select class="select2-basic" name="timezone">
                                    @foreach ($timezones as $timezone)
                                        <option value="'{{ @$timezone }}'">{{ __($timezone) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ $general->base_color }}" />
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color"
                                        value="{{ $general->base_color }}" />
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Site Secondary Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ $general->secondary_color }}" />
                                    </span>
                                    <input type="text" class="form-control colorCode" name="secondary_color"
                                        value="{{ $general->secondary_color }}" />
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Fixed Charge')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control"
                                            name="balance_transfer_fixed_charge"
                                            value="{{ getAmount($general->balance_transfer_fixed_charge) }}" />
                                        <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Percent Charge')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control"
                                            name="balance_transfer_percent_charge"
                                            value="{{ getAmount($general->balance_transfer_percent_charge) }}" />
                                        <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Limit')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control"
                                            name="balance_transfer_limit"
                                            value="{{ getAmount($general->balance_transfer_limit) }}" />
                                        <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Dispatch Commission Module ')</label>
                                    <div class="input-group">
                                        <select class="form-control" name="dispatch_commission_module">
                                            <option>@lang('Select One...')</option>
                                            <option value="1" @selected($general->dispatch_commission_module == Status::DISPATCH_COMMISSION_LOWER_PLAN)>@lang('Based on lower plan')</option>
                                            <option value="2" @selected($general->dispatch_commission_module == Status::DISPATCH_COMMISSION_CHILD_PLAN)>@lang('Based on child user\'s plan')</option>
                                            <option value="3" @selected($general->dispatch_commission_module == Status::DISPATCH_COMMISSION_SELF_PLAN)>@lang('Based on self plan')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/viseradmin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/viseradmin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });

            $('select[name=timezone]').val("'{{ config('app.timezone') }}'").select2();
            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });
        })(jQuery);
    </script>
@endpush
