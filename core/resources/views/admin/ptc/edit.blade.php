@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form role="form" method="post" action="{{ route('admin.ptc.store', $ptc->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input type="text" name="title" class="form-control" value="{{ $ptc->title }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="amount" class="form-control" value="{{ getAmount($ptc->amount) }}" required>
                                    <div class="input-group-text"> {{ __($general->cur_text) }} </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Duration')</label>
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control" value="{{ $ptc->duration }}" required>
                                    <div class="input-group-text">@lang('SECONDS')</div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Maximum Show')</label>
                                <div class="input-group">
                                    <input type="number" name="max_show" class="form-control" value="{{ $ptc->max_show }}" required>
                                    <div class="input-group-text">@lang('Times')</div>
                                </div>
                            </div>
                        </div>

                        <div class="row ">

                            <div class="form-group col-md-6">
                                <label>@lang('Advertisement Type')</label>
                                <input type="hidden" name="ads_type" value="{{ $ptc->ads_type }}">
                                <div class="pt-1">
                                    @php
                                        echo $ptc->adsTypeBadge;
                                    @endphp
                                </div>
                            </div>
                            @if ($ptc->ads_type == Status::ADS_LINK)
                                <div class="form-group col-md-6">
                                    <label>@lang('Link')</label>
                                    <input type="text" name="website_link" class="form-control" value="{{ $ptc->ads_body }}">
                                </div>
                            @elseif($ptc->ads_type == Status::ADS_IMAGE)
                                <div class="form-group col-md-6">
                                    <label>@lang('Banner')</label>
                                    <div class="image-upload">
                                        <div class="thumb">.
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{ getImage(getFilePath('ptc') . '/' . $ptc->ads_body, null) }})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>

                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="banner_image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg, .gif" />
                                                <label for="image" class="bg--primary">@lang('Upload')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="form-group col-md-6">
                                    <label>@lang('Script')</label>
                                    <textarea name="script" class="form-control" rows="4">{{ $ptc->ads_body }}</textarea>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
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

@push('style')
<style>
.profilePicPreview{
    background-position: center !important;
}
</style>
@endpush
