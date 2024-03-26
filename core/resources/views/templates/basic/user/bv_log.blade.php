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
                                    <th>@lang('Sl')</th>
                                    <th>@lang('BV')</th>
                                    <th>@lang('Position')</th>
                                    <th>@lang('Detail')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bvLogs as $log)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td class="budget">
                                            <strong
                                                @if ($log->trx_type == '+') class="text--success" @else class="text--danger" @endif>
                                                {{ $log->trx_type == '+' ? '+' : '-' }}
                                                {{ showAmount($log->amount) }}
                                            </strong>
                                        </td>

                                        <td>
                                            @if ($log->position == Status::BV_LEFT)
                                                <span class="badge badge--success">@lang('Left')</span>
                                            @else
                                                <span class="badge badge--primary">@lang('Right')</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->details }}</td>
                                        <td>

                                            @if ($log->created_at)
                                                {{ showDateTime($log->created_at, 'j M,Y g:i A') }}
                                            @else
                                                @lang('Not Assign')
                                            @endif
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
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
