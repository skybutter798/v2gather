@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Total Click')</th>
                                    <th>@lang('Total Earn')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($ptc as $data)
                                    <tr>
                                        <td>{{ $data->vdt }}</td>
                                        <td>{{ $data->clicks }}
                                        <td>{{ showAmount($data->amount) }} {{ $general->cur_text }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
