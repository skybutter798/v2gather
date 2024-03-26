@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card  b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Sl')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                    <tr>
                                        <td>{{ $loop->index + $ads->firstItem() }}</td>
                                        <td>{{ __($ad->title) }}</td>
                                        <td>
                                            <a href="{{ route('user.ptc.show', encrypt($ad->id . '|' . auth()->user()->id)) }}"
                                                class="btn btn-outline--primary btn-sm">
                                                <i class="las la-eye"></i> @lang('View Now')
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($ads->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($ads) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by title" />
@endpush
