@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Maximum View')</th>
                                    <th>@lang('Viewed')</th>
                                    <th>@lang('Remain')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ptcs as $ptc)
                                    <tr>
                                        <td>{{ strLimit($ptc->title, 20) }}</td>
                                        <td>
                                            @php echo $ptc->adsTypeBadge  @endphp
                                        </td>
                                        <td>{{ $ptc->duration }} @lang('Sec')</td>
                                        <td>{{ $ptc->max_show }}</td>
                                        <td>{{ $ptc->showed }}</td>
                                        <td> {{ $ptc->remain }}</td>
                                        <td>{{ showAmount($ptc->amount) }} {{ __($general->cur_text) }}</td>
                                        <td>
                                            @php echo $ptc->statusBadge @endphp
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.ptc.edit', $ptc->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </a>
                                            @if ($ptc->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this ptc?')" data-action="{{ route('admin.ptc.status', $ptc->id) }}">
                                                    <i class="la la-eye"></i>@lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this ptc?')" data-action="{{ route('admin.ptc.status', $ptc->id) }}">
                                                    <i class="la la-eye-slash"></i>@lang('Disable')
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($ptcs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ptcs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by title ..." />
    <a href="{{ route('admin.ptc.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-plus"></i> @lang('Add New')
    </a>
@endpush
