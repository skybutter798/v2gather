@extends($activeTemplate . 'layouts.master')
<style>
    .arrow {
        cursor: pointer;
        font-size: 12px; /* Adjust size as needed */
        margin-top: 5px;
    }
    
    .child-node {
        align-items: center;
        display: flex;
        flex-direction: column;
    }

</style>
@section('content')
    <div class="card" style="position:absolute">
        <div class="row justify-content-center llll text-center" style="overflow:auto; max-height: 100%;">
            <!-- Root node -->
            <div id="treeRoot" class="w-1 child-node" data-spot-id="{{ auth()->user()->id }}" data-parent-id="0" data-position="root">
                <div class="user register-spot" style="cursor: pointer;" data-bs-toggle="" data-bs-target="#registerUserModal">
                    <img src="{{ asset('assets/images/default.png') }}" alt="*" class="no-user">
                    <p class="user-name"><strong>{{ auth()->user()->username }}</strong></p> <!-- Change made here -->
                </div>
                <div class="arrow" style="cursor: pointer;">▼</div>
                <span class="line"></span>
            </div>
        </div>
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
                            <select class="form-control" id="modalPosition" required disabled readonly>
                                <option value="1" disabled readonly>@lang('Left')</option>
                                <option value="2" disabled readonly>@lang('Right')</option>
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

$(document).ready(function() {
    $(document).on('click', '.user.register-spot', function() {
        var $closestNode = $(this).closest('.child-node');
        var spotId = $closestNode.data('spot-id');
        var parentId = $closestNode.data('parent-id');
        var position = $closestNode.data('position');
        
        console.log('Spot ID:', spotId);
        console.log('Parent ID:', parentId);
        console.log('Position:', position);

    
        // Check if position is defined to prevent errors
        if (typeof position !== 'undefined') {
            position = position.toString();
        } else {
            // Handle the case where position is undefined
            console.log('Position is undefined. Ensure this spot has a defined position.');
            return; // Exit the function or handle appropriately
        }
    
        if ($(this).hasClass('empty')) {
            // Logic for empty spot click
            showRegisterFormWithCorrectUpline(parentId, position);
        } else {
            showUserDetails(spotId);
        }
    });

    
    function showRegisterFormWithCorrectUpline(parentId, position) {
        console.log("parent: ", parentId);
        
        var currentUserUsername = "{{ auth()->user()->username }}";
        console.log("Current User Username:", currentUserUsername);
        
        // Fetch the parent node to get the correct upline username
        var uplineUserElement = $(`[data-spot-id="${parentId}"]`);
        console.log("Upline User Element:", uplineUserElement);
        
        var uplineUsername = uplineUserElement.find('.user-name').first().text().trim();
        console.log("Upline Username:", uplineUsername);
        
        // Set form values
        $('#modalReferralUsername').val(currentUserUsername); // Direct Sponsor
        $('#pusername').val(uplineUsername); // Upline Tree
        $('#modalPosition').prop('disabled', false).val(position); // Position, enabling the dropdown for clarity
        $('#hiddenPosition').val(position); // Hidden input for form submission
        
        $('#registerUserModal').modal('show');
    }

    function showUserDetails(spotId) {
        $.ajax({
            url: '/user/user-details/' + spotId, // Make sure this URL matches your actual API endpoint
            type: 'GET',
            success: function(response) {
                console.log("AJAX success response:", response);
            
                if (response.user.user_extra) {
                    $('.lbv').text(response.user.user_extra.bv_left || '0');
                    $('.rbv').text(response.user.user_extra.bv_right || '0');
                    $('.lpaid').text(response.user.user_extra.paid_left || '0');
                    $('.rpaid').text(response.user.user_extra.paid_right || '0');
                    $('.lfree').text(response.user.user_extra.free_left || '0');
                    $('.rfree').text(response.user.user_extra.free_right || '0');
                }
            
                // Update the referrer username display
                if (response.referrerUsername) {
                    $('.tree_ref').text(response.referrerUsername);
                } else {
                    $('.tree_ref').text('N/A');
                }
            
                $('#exampleModalCenter').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching user details: ", error);
            }
        });
    }

    $(document).on('click', '.arrow', function() {
        var $node = $(this).closest('.child-node');
        var userId = $node.data('spot-id');
    
        if ($node.hasClass('loaded')) {
            $node.find('> .children-container').toggle();
            updateArrowState($(this), $node.find('> .children-container').is(':visible'));
            return;
        }
    
        var fetchChildrenUrl = '/user/fetch-children/' + userId;
    
        $.ajax({
            url: fetchChildrenUrl,
            type: 'GET',
            success: function(response) {
                console.log("Success response: ", response);
        
                var leftChildId = response.children.left ? response.children.left.id : userId;
                var rightChildId = response.children.right ? response.children.right.id : userId;
        
                var childrenHtml = '<div class="children-container" style="display: flex;">';
                childrenHtml += generateUserHtml(response.children.left || null, '1', leftChildId); // Pass user ID for left child
                childrenHtml += generateUserHtml(response.children.right || null, '2', rightChildId); // Pass user ID for right child if exists
                childrenHtml += '</div>';
        
                console.log("Left child ID:", leftChildId); // Log left child ID
                console.log("Right child ID:", rightChildId); // Log right child ID
        
                $node.append(childrenHtml);
                $node.addClass('loaded');
                updateArrowState($node.find('.arrow'), true);
            },
        });
    });
    
    function fetchAndDisplayChildren($node, level = 1) {
        var userId = $node.data('spot-id'); // This is correct for the initial call
        console.log("Fetching children for user ID:", userId, "at level", level);
    
        if (level > 3) {
            console.log("Reached maximum depth at userId " + userId);
            return;
        }
    
        $.ajax({
            url: '/user/fetch-children/' + userId,
            type: 'GET',
            success: function(response) {
                console.log("AJAX success response for userId " + userId + ":", response);
    
                if (!response.children.left && !response.children.right) {
                    console.log("Both left and right children are empty for userId " + userId);
                    return; // No children to process, so exit
                }
    
                var childrenHtml = '<div class="children-container" style="display: flex;">';
                
                // Process left child
                if (response.children.left) {
                    childrenHtml += generateUserHtml(response.children.left, '1', response.children.left.id);
                } else {
                    // If no left child, create an empty spot
                    childrenHtml += generateUserHtml(null, '1', userId);
                }
                
                // Process right child
                if (response.children.right) {
                    childrenHtml += generateUserHtml(response.children.right, '2', response.children.right.id);
                } else {
                    // If no right child, create an empty spot
                    childrenHtml += generateUserHtml(null, '2', userId);
                }
                
                childrenHtml += '</div>';
                $node.append(childrenHtml).addClass('loaded');
                updateArrowState($node.find('.arrow'), true);
    
                // Recursive call for each non-empty child node, incrementing the level
                $node.find('.child-node').each(function() {
                    var childId = $(this).data('spot-id'); // Extract child's ID
                    // Make sure the childId is defined before attempting to fetch its children
                    if (typeof childId !== 'undefined') {
                        fetchAndDisplayChildren($(this), level + 1);
                    } else {
                        console.log("Skipping fetch for undefined childId at level", level);
                    }
                });
    
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error fetching children for userId " + userId + ":", textStatus, errorThrown);
            }
        });
    }
    
    $(document).ready(function() {
        console.log("Document is ready.");
    
        // Assuming your root node has an id="treeRoot"
        var $rootNode = $('#treeRoot');
        console.log("Root node length:", $rootNode.length);
    
        if ($rootNode.length) {
            console.log("Fetching and displaying children for root node.");
            fetchAndDisplayChildren($rootNode, 1);
        } else {
            console.log("#treeRoot not found.");
        }
    });

    
    function generateUserHtml(user, position, userId) {
        // Initialize planLabel with an empty string
        var planLabel = '';
    
        // Check if the user object exists and set the planLabel accordingly
        if (user) {
            if (user.plan_3 === 1) {
                planLabel = '<span class="badge bg-success">v300</span>'; // If plan_id is 1
            } else if (user.plan_3 === 0) {
                planLabel = '<span class="badge bg-secondary">none</span>'; // If plan_id is 0
            }
        }
    
        if (!user) {
            return `<div class="child-node empty-spot" style="align-items: center; display: flex; flex-direction: column; margin: 5px;"
                     data-position="${position}" data-parent-id="${userId}">
                <div class="user register-spot empty" data-empty="true" style="cursor: pointer;">
                    <img src="https://v2gather.org/assets/images/default.png" alt="Empty Spot" class="empty-user">
                    <p class="user-name">[+]</p>
                </div>
            </div>`;
        } else {
            return `<div class="child-node" data-spot-id="${userId}" data-position="${position}" style="align-items: center; display: flex; flex-direction: column; margin: 5px;">
                <div class="user register-spot" style="cursor: pointer;" data-spot-id="${userId}">
                    <img src="{{ asset('assets/images/default.png') }}" alt="*" class="no-user">
                    <p class="user-name" style="font-size: 10px; !important">${user.username}</p>
                    ${planLabel} <!-- Include the plan label with appropriate badge -->
                </div>
                <div class="arrow" style="cursor: pointer;">▼</div>
                <span class="line"></span>
            </div>`;
        }
    }


    
    function updateArrowState($arrow, isVisible) {
        if (isVisible) {
            $arrow.text('▲');
        } else {
            $arrow.text('▼');
        }
    }
});
    
</script>

@endpush