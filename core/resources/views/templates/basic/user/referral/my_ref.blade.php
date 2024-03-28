@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card">
        @php
            $loopNumber = 1;
            $totalLoop = 0;
            $spotId = 1;
        @endphp
        
        @for ($i = 1; $i <= 4; $i++)
            <div class="row justify-content-center llll text-center">
                @for ($in = 1; $in <= $loopNumber; $in++)
                    @php
                        $leftOrRight = ($spotId % 2 == 0) ? '1' : '2';
                        $parentId = intdiv($spotId, 2);
                    @endphp
                    
                    <div class="w-{{ $loopNumber }}" data-spot-id="{{ $spotId }}" data-parent-id="{{ $parentId }}" data-position="{{ $leftOrRight }}">
                        @if (is_null($tree[$mlm->getHands()[$totalLoop]]) || empty($tree[$mlm->getHands()[$totalLoop]]))
                            
                            <div class="user register-spot" style="cursor: pointer;" data-bs-toggle="" data-bs-target="#registerUserModal" data-spot-id="{{ $spotId }}" data-parent-id="{{ $parentId }}" data-position="{{ $leftOrRight }}">
                                <img src="{{ asset('assets/images/default.png') }}" alt="*" class="no-user">
                                <p class="user-name">[+]</p>
                                <span class="line"></span>
                            </div>
                            
                        @else
                            @php echo $mlm->showSingleUserinTree($tree[$mlm->getHands()[$totalLoop]]); @endphp
                        @endif
                    </div>
                    
                    @php
                        $totalLoop++;
                        $spotId++; // Increment spotId for each spot
                    @endphp
                @endfor
                
            </div>
            @php $loopNumber *= 2; @endphp
            
        @endfor
    </div>

    <div class="modal fade user-details-modal-area" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">@lang('User Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-details-modal">
                        <div class="user-details-header ">
                            <div class="thumb">
                                <img src="#" alt="*" class="tree_image w-h-100-p">
                            </div>
                            <div class="content">
                                <a class="user-name tree_url tree_name" href=""></a>
                                <span class="user-status tree_status"></span>
                                <span class="user-status tree_plan"></span>
                            </div>
                        </div>
                        <div class="user-details-body text-center">

                            <h6 class="my-3">@lang('Referred By'): <span class="tree_ref"></span></h6>


                            <table class="table table-bordered">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>@lang('LEFT')</th>
                                    <th>@lang('RIGHT')</th>
                                </tr>

                                <tr>
                                    <td>@lang('Current BV')</td>
                                    <td><span class="lbv"></span></td>
                                    <td><span class="rbv"></span></td>
                                </tr>
                                <tr>
                                    <td>@lang('Free Member')</td>
                                    <td><span class="lfree"></span></td>
                                    <td><span class="rfree"></span></td>
                                </tr>

                                <tr>
                                    <td>@lang('Paid Member')</td>
                                    <td><span class="lpaid"></span></td>
                                    <td><span class="rpaid"></span></td>
                                </tr>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Register User Modal -->
    <div class="modal fade" id="registerUserModal" tabindex="-1" role="dialog" aria-labelledby="registerUserModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerUserModalTitle">@lang('Register User')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Registration Form within Modal -->
                    <form id="registrationForm" class="account-form verify-gcaptcha" method="post" action="{{ route('user.registerdownline') }}">
                        @csrf
                        <div class="form-group">
                            <label for="modalReferralUsername">@lang('app.Direct Sponsor')</label>
                            <input type="text" class="form-control" id="modalReferralUsername" name="referral" required readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="modalName">@lang('Upline Tree')</label>
                            <input type="text" name="pusername" id="pusername" class="form-control" value="" required readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="modalPosition">@lang('Position')</label>
                            <select class="form-control" id="modalPosition" required disabled>
                                <option value="1">@lang('Left')</option>
                                <option value="2">@lang('Right')</option>
                            </select>
                            <input type="hidden" name="position" id="hiddenPosition" value="">
                        </div>

                        <!-- Additional fields like Country, Mobile, etc. -->
                        <div class="form-group">
                            <label for="modalName">@lang('Username')</label>
                            <input type="text" class="form-control" id="modalName" name="username" required>
                        </div>
                        
                        
                        <div class="form-group">
                            <label for="modalEmail">@lang('Email')</label>
                            <input type="email" class="form-control" id="modalEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="modalPassword">@lang('Password')</label>
                            <input type="password" class="form-control" id="modalPassword" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="modalPasswordConfirmation">@lang('Confirm Password')</label>
                            <input type="password" class="form-control" id="modalPasswordConfirmation" name="password_confirmation" required>
                        </div>
                        <div class="form-group">
                            <label for="modalPasswordConfirmation">@lang('Mobile')</label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>
                        
                        <input type="hidden" name="mobile_code" value="+60"> <!-- Example for US -->
                        <input type="hidden" name="country_code" value="MY">
                        <input type="hidden" name="country" value="Malaysia">
                        <input type="checkbox" id="agree" name="agree" required>
                        <label for="agree" >I agree to the terms and conditions</label><br>

                        <!-- Include CAPTCHA field if you're using one -->
                        <x-captcha />
                        <button type="submit" class="btn btn-primary">@lang('Register')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('style')
    <link href="{{ asset($activeTemplateTrue . 'css/tree.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.showDetails').on('click', function() {
                var modal = $('#exampleModalCenter');

                $('.tree_name').text($(this).data('name'));
                $('.tree_url').attr({
                    "href": $(this).data('treeurl')
                });
                $('.tree_status').text($(this).data('status'));
                $('.tree_plan').text($(this).data('plan'));
                $('.tree_image').attr({
                    "src": $(this).data('image')
                });
                $('.user-details-header').removeClass('Paid');
                $('.user-details-header').removeClass('Free');
                $('.user-details-header').addClass($(this).data('status'));
                $('.tree_ref').text($(this).data('refby'));
                $('.lbv').text($(this).data('lbv'));
                $('.rbv').text($(this).data('rbv'));
                $('.lpaid').text($(this).data('lpaid'));
                $('.rpaid').text($(this).data('rpaid'));
                $('.lfree').text($(this).data('lfree'));
                $('.rfree').text($(this).data('rfree'));
                $('#exampleModalCenter').modal('show');
            });
        })(jQuery);
        
        var currentUserId = "{{ auth()->user()->id }}";
        
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.user.register-spot').forEach(function(spot) {
                spot.addEventListener('click', function() {
                    var parentSpot = this.closest('[data-spot-id]').getAttribute('data-parent-id');
                    var parentElement = document.querySelector('[data-spot-id="' + parentSpot + '"] .user-name');
                    
                    if (parentElement) {
                        var parentUsername = parentElement.getAttribute('data-username') || parentElement.textContent;
                    } else {
                        alert('Parent user not found.');
                    }
                });
            });
        });

        $('.register-spot').on('click', function() {
            var spotId = $(this).data('spot-id');
            var parentId = $(this).data('parent-id');
            var position = String($(this).data('position'));
            var positionText = position === '1' ? 'Left' : 'Right';
            var currentUserUsername = "{{ auth()->user()->username }}";
            var selectElement = document.getElementById('modalPosition');
    
            // Set modal fields
            $('#modalReferralUsername').val(currentUserUsername);
            $('#modalPosition').val(positionText);
            $('#hiddenPosition').val(position);
            selectElement.value = position;
            

            
            var parentElement = $('[data-spot-id="' + parentId + '"]').find('.user-name');
            var parentUsername = parentElement.data('username') || parentElement.text(); // Retrieve username
            $('#pusername').val(parentUsername);
        
            if (parentUsername !== '[+]') {
                $('#registerUserModal').modal('show');
            } else {
                console.log('Parent username is empty, modal not shown.');
            }

            console.log('Parent ID:', parentId);
            console.log('Current User Username:', currentUserUsername);
            console.log('Clicked Spot ID:', spotId);
            console.log('Parent User:', parentUsername); // Now correctly logs the parent's username
            console.log('Position (Left or Right):', positionText);
            
        });
    </script>
@endpush