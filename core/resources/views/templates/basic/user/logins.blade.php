@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Browser')</th>
                                    <th>@lang('OS')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginLogs as $login)
                                    <tr>
                                        <td>{{ diffForHumans($login->created_at) }}</td>
                                        <td>{{ $login->user_ip }} </td>
                                        <td>{{ __($login->country) }}</td>
                                        <td>{{ __($login->browser) }} </td>
                                        <td> {{ __($login->os) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($loginLogs->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($loginLogs) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('user.home') }}"></x-back>
@endpush
