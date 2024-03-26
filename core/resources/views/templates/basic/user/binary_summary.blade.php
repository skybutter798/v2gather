@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 mb-5">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('app.Paid left')</th>
                                    <th>@lang('app.Paid right')</th>
                                    <th>@lang('app.Free left')</th>
                                    <th>@lang('app.Free right')</th>
                                    <th>@lang('app.Bv left')</th>
                                    <th>@lang('app.Bv right')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $binaries->paid_left }}</td>
                                    <td>{{ $binaries->paid_right }}</td>
                                    <td>{{ $binaries->free_left }}</td>
                                    <td>{{ $binaries->free_right }}</td>
                                    <td>{{ showAmount($binaries->bv_left) }}</td>
                                    <td>{{ showAmount($binaries->bv_right) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
