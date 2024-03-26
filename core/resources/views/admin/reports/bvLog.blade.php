@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('BV')</th>
                                    <th>@lang('Position')</th>
                                    <th>@lang('Detail')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $data)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $data->user->fullname }}</span>
                                            <br>
                                            <span class="text--small"> <a
                                                    href="{{ route('admin.users.detail', @$data->user_id) }}"><span>@</span>{{ $data->user->username }}</a>
                                            </span>
                                        </td>
                                        <td class="budget">
                                            <strong
                                                @if ($data->trx_type == '+') class="text--success"
                                                @else class="text--danger" @endif>
                                                {{ $data->trx_type == '+' ? '+' : '-' }}
                                                {{ showAmount($data->amount) }} {{ __($general->cur_text) }}</strong>
                                        </td>

                                        <td>
                                            @if ($data->position == 1)
                                                <span class="badge badge--success">@lang('Left')</span>
                                            @else
                                                <span class="badge badge--primary">@lang('Right')</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->details }}</td>
                                        <td>
                                            {{ showDateTime($data->created_at) }}<br>{{ diffForHumans($data->created_at) }}
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

                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-search-form />
@endpush
