@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form role="form" method="post" action="{{ route('admin.ptc.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="amount" class="form-control"
                                        value="{{ old('amount') }}" required>
                                    <div class="input-group-text"> {{ __($general->cur_text) }} </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Duration')</label>
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control"
                                        value="{{ old('duration') }}"required>
                                    <div class="input-group-text">@lang('SECONDS')</div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Maximum Show')</label>
                                <div class="input-group">
                                    <input type="number" name="max_show" class="form-control" value="{{ old('max_show') }}"
                                        required>
                                    <div class="input-group-text">@lang('Times')</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Advertisement Type')</label>
                                <select class="form-control" name="ads_type" required>
                                    <option value="1" @selected(old('ads_type') == Status::ADS_LINK)>
                                        @lang('Link / URL')
                                    </option>
                                    <option value="2" @selected(old('ads_type') == Status::ADS_IMAGE)>
                                        @lang('Banner / Image')
                                    </option>
                                    <option value="3" @selected(old('ads_type') == Status::ADS_SCRIPT)>
                                        @lang('Script / Code')
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="websiteLink">
                                <label>@lang('Link')</label>
                                <input type="text" name="website_link" class="form-control"
                                    value="{{ old('website_link') }}">
                            </div>
                            <div class="form-group col-md-6 d-none" id="bannerImage">
                                <label>@lang('Banner')</label>

                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url({{ getImage('','') }})"></div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" name="banner_image" class="profilePicUpload d-none"
                                                id="image" accept=".png, .jpg, .jpeg, .gif" />
                                            <label for="image" class="bg--primary mt-3">@lang('Upload')</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-6 d-none" id="script">
                                <label>@lang('Script')</label>
                                <textarea name="script" class="form-control" rows="4">{{ old('script') }}</textarea>
                            </div>
                        </div>
                        <div class="form-gorup">
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.ptc.index') }}" />
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#ads_type').on('change', function() {
                var adType = $(this).val();
                if (adType == 1) {
                    $("#websiteLink").removeClass('d-none');
                    $("#bannerImage").addClass('d-none');
                    $("#script").addClass('d-none');
                } else if (adType == 2) {
                    $("#bannerImage").removeClass('d-none');
                    $("#websiteLink").addClass('d-none');
                    $("#script").addClass('d-none');
                } else {
                    $("#bannerImage").addClass('d-none');
                    $("#websiteLink").addClass('d-none');
                    $("#script").removeClass('d-none');
                }
            });
        })(jQuery);
    </script>
@endpush

@push('style')
<style>
.profilePicPreview{
    background-position: center !important;
}
</style>
@endpush
