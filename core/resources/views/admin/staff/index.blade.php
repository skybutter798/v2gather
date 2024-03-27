@extends('admin.layouts.app')
<!-- In the <head> section -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Before the closing </body> tag -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Email-Phone')</th>
                                    <th>@lang('Role')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffs as $staff)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $staff->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $staff->id) }}"><span>@</span>{{ $staff->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            {{ $staff->email }}<br>{{ $staff->mobile }}
                                        </td>
                                        <td>
                                            {{ $staff->role == 1 ? 'Admin' : 'Staff' }}
                                        </td>

                                        <td>
                                            {{ showDateTime($staff->created_at) }} <br>
                                            {{ diffForHumans($staff->created_at) }}
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.users.detail', $staff->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                                @if (request()->routeIs('admin.users.kyc.pending'))
                                                    <a href="{{ route('admin.users.kyc.details', $staff->id) }}"
                                                        target="_blank" class="btn btn-sm btn-outline--dark">
                                                        <i class="las la-user-check"></i>@lang('KYC Data')
                                                    </a>
                                                @endif
                                            </div>
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
                @if ($staffs->hasPages())
                    <div class="card-footer py-4">
                        {{ $staffs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

<!-- Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Staff')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.staff.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">@lang('Email')</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="username">@lang('Username')</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="role">@lang('Role')</label>
                        <select class="form-control" name="role">
                            <option value="1">Admin</option>
                            <option value="2">Staff</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary">@lang('Add Staff')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
    <button type="button" class="btn btn-sm btn-outline--primary planAdd h-45" data-toggle="modal" data-target="#addStaffModal">
        <i class="la la-plus"></i> @lang('Add New')
    </button>
@endpush
