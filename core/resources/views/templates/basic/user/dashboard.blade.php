    @extends($activeTemplate . 'layouts.master')
    @section('content')
    @php
    $kycContent = getContent('kyc.content', true);
    $notices = getContent('notice.element', orderById: true);
    @endphp
    <div class="row gy-4">
        <!-- Modal -->
        <div class="modal fade" id="convertModal" tabindex="-1" aria-labelledby="convertModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="convertModalLabel">{{ __('app.CONVERT TO V2P') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="convertForm">
                  <!-- Input for amount -->
                  <div class="mb-3">
                    <label for="convertAmount" class="form-label">{{ __('app.Amount') }}</label>
                    <input type="number" class="form-control" id="convertAmount" name="amount" required>
                  </div>
                  <!-- Optionally, add other fields here -->
                  <button type="submit" class="btn btn-primary" style="color:white">{{ __('app.Submit') }}</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel" >{{ __('app.BALANCE TRANSFER') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="transferForm">
                  <!-- Input for amount -->
                  <div class="mb-3">
                    <label for="transferAmount" class="form-label">{{ __('app.Username') }}</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                    
                    <label for="transferAmount" class="form-label">{{ __('app.Amount') }}</label>
                    <input type="number" class="form-control" id="transferAmount" name="transferAmount" required>
                  </div>
                  <!-- Optionally, add other fields here -->
                  <button type="submit" class="btn btn-primary" style="color:white">{{ __('app.Submit') }}</button>
                </form>
              </div>
            </div>
          </div>
        </div>
    
        @if (auth()->user()->kv == Status::KYC_UNVERIFIED)
        <div class="col-md-12">
            <div class="card alert alert-info">
                <div class="card-header">@lang('KYC Verification required')</div>
                <div class="card-body">
                    {{ __(@$kycContent->data_values->unverified_content) }} <a
                        href="{{ route('user.kyc.form') }}">@lang('Click Here to Verify')</a>
                </div>
            </div>
        </div>
    
        @elseif(auth()->user()->kv == Status::KYC_PENDING)
        <div class="col-md-12">
            <div class="card alert alert-warning">
                <div class="card-header">
                    @lang('KYC Verification pending')
                </div>
                <div class="card-body">
                    {{ __(@$kycContent->data_values->pending_content) }}<a href="{{ route('user.kyc.data') }}">@lang('See
                        KYC Data')</a>
                </div>
            </div>
        </div>
        @endif
    
        {{--@foreach ($notices as $notice)
        <div class="col-12">
            <div class="card">
                <div class="card-header">{{ __($notice->data_values->title) }}</div>
                <div class="card-body">
                    @php echo $notice->data_values->description; @endphp
                </div>
            </div>
        </div>
        @endforeach--}}
    
        {{--<div class="col-xxl-3 col-sm-6">
            <x-widget link="{{ route('user.ptc.clicks') }}" icon="las la-mouse-pointer f-size--56" title="Total Clicks"
                value="{{ $data['clicks'] }}" bg="blue" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget link="{{ route('user.ptc.index') }}" icon="las la-clock f-size--56" title="Remain clicks for today"
                value="{{ $data['rem_clicks'] }}" bg="blue" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget link="{{ route('user.ptc.clicks') }}" icon="las la-check-circle f-size--56" title="Today's Click"
                value="{{ $data['today_clicks'] }}" bg="blue" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--16 has-link box--shadow2 h-100">
                <a href="{{ route('user.ptc.index') }}" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <i class="las la-stopwatch f-size--56"></i>
                        </div>
                        <div class="col-10 text-end">
                            <span class="text-white text--small">@lang('Next Reminder')</span>
                            <h2 class="text-white" id="counter"></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->--}}
    
        {{--<div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.withdraw.history') }}?search={{ Status::PAYMENT_PENDING }}"
                icon="las la-sync" title="Pending Withdrawals" value="{{ $data['pendingWithdraw'] }}" color="warning" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.withdraw.history') }}?search={{ Status::PAYMENT_REJECT }}"
                icon="las la-times-circle" title="Rejected Withdrawals" value="{{ $data['rejectedWithdraw'] }}"
                color="danger" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.withdraw.history') }}" icon="las la-hand-holding-usd"
                icon_style="false" title="Total Withdraw"
                value="{{ $general->cur_sym }}{{ showAmount($data['totalWithdraw']) }}" color="danger" />
        </div><!-- dashboard-w1 end -->
        
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.withdraw.history') }}?search={{ Status::PAYMENT_SUCCESS }}"
                icon="las la-check-double" icon_style="false" title="Completed Withdraw"
                value="{{ $data['completeWithdraw'] }}" color="primary" />
        </div>--}}
            
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="3" link="{{ route('user.transactions') }}" icon="fas fa-money-bill" icon_style="false"
                title="{{ __('app.V2P Wallet') }}" value="0" value="{{ $general->cur_sym }}{{ showAmount($data['V2P']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="3" link="{{ route('user.transactions') }}" icon="fas fa-money-bill" icon_style="false"
                title="{{ __('app.WD Wallet') }}" value="{{ $general->cur_sym }}{{ showAmount($data['balance']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="3" link="{{ route('user.transactions') }}" icon="fas fa-money-bill" icon_style="false"
                title="{{ __('app.EV Wallet') }}" value="0" value="{{ $general->cur_sym }}{{ showAmount($data['EP']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="3" link="{{ route('user.transactions') }}" icon="fas fa-money-bill" icon_style="false"
                title="{{ __('app.RP Wallet') }}" value="0" value="{{ $general->cur_sym }}{{ showAmount($data['RP']) }}" color="info" />
        </div>
        <div class="my-3">
            <div class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#convertModal">{{ __('app.CONVERT TO V2P') }}</div>
            <div class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#transferModal">{{ __('app.BALANCE TRANSFER') }}</div>
        </div>

        <h3 style="margin-top:60px">Summary</h3>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.deposit.history') }}" icon="las la-university"
                title="{{ __('app.Total Deposit') }}" value="{{ $general->cur_sym }}{{ showAmount($data['totalDeposit']) }}"
                color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.invest') }}" icon="lar la-credit-card" title="{{ __('app.Total Investment') }}"
                value="{{ $general->cur_sym }}{{ showAmount($data['totalInvest']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.refCom') }}" icon="las la-percent" title="{{ __('app.Total Referrral Comission') }}"
                value="{{ $general->cur_sym }}{{ showAmount($data['totalRefCom']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.binaryCom') }}" icon="las la-tree" title="{{ __('app.Total Bainary Comission') }}"
                value="{{ $general->cur_sym }}{{ showAmount($data['totalBinaryCom']) }}" color="info" />
        </div>
        
        <h3>Referral</h3>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.referral') }}" icon="fas fa-users" title="{{ __('app.Total Referral') }}"
                value="{{ $data['total_ref'] }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.myTree') }}" icon="fas fa-arrow-circle-left" title="{{ __('app.Total Left') }}"
                value="{{ $data['totalLeft'] }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.myTree') }}" icon="fas fa-arrow-circle-right" title="{{ __('app.Total Right') }}"
                value="{{ $data['totalRight'] }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.plan.bvLog') }}?type=paidBV" icon="fas fa-cart-arrow-down"
                title="{{ __('app.Total BV') }}" value="{{ $data['totalBv'] }}" color="info" />
        </div>
        
        
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.plan.bvLog') }}?type=leftBV" icon="fas fa-arrow-left" title="{{ __('app.Left BV') }}"
                value="{{ getAmount($data['leftBv']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.plan.bvLog') }}?type=rightBV" icon="fas fa-arrow-right"
                title="{{ __('app.Right BV') }}" value="{{ getAmount($data['rightBv']) }}" color="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="2" link="{{ route('user.plan.bvLog') }}?type=cutBV" icon="las la-cut" title="{{ __('app.Total BV Cut') }}"
                value="{{ getAmount($data['totalBvCut']) }}" color="info" />
        </div>
    
        {{--<div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Click & Earn Report')</h5>
                    <div id="apex-bar-chart"></div>
                </div>
            </div>
        </div>--}}
    
    </div><!-- row end-->
    
    @endsection
    
    
    @push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/apexchart.js') }}"></script>
    <script>
        (function ($) {
    
            var options = {
                series: [{
                    name: 'Clicks',
                    data: [
                        @foreach($chart['click'] as $key => $click) {
                            {
                                $click
                            }
                        },
                        @endforeach
                    ]
                }, {
                    name: 'Earn Amount',
                    data: [
                        @foreach($chart['amount'] as $key => $amount) {
                            {
                                $amount
                            }
                        },
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 580,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [
                        @foreach($chart['amount'] as $key => $amount)
                        '{{ $key }}',
                        @endforeach
                    ],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val
                        }
                    }
                }
            };
            var chart = new ApexCharts(document.querySelector("#apex-bar-chart"), options);
            chart.render();
        })(jQuery);
    
        function createCountDown(elementId, sec) {
            var tms = sec;
            var x = setInterval(function () {
                var distance = tms * 1000;
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById(elementId).innerHTML = days + "d: " + hours + "h " + minutes + "m " +
                    seconds + "s ";
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById(elementId).innerHTML = `@lang('COMPLETE')`;
                }
                tms--;
            }, 1000);
        }
        createCountDown('counter', {
            {
                \
                Carbon\ Carbon::tomorrow() - > diffInSeconds()
            }
        });
    </script>
    <script>
        document.getElementById('convertForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("Form submission prevented with vanilla JS");
        
            var amount = document.getElementById('convertAmount').value;
            console.log('Amount to convert:', amount);
        
            var csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMetaTag) {
                console.error('CSRF token not found');
                return; // Exit the function if CSRF token is missing
            }
            var csrfToken = csrfMetaTag.getAttribute('content');
        
            fetch('/user/convert', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount: amount })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                } else {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (error) {
                            console.error("Response was not valid JSON:", text);
                            throw new Error("Response was not valid JSON.");
                        }
                    });
                }
            })
            .then(data => {
                console.log('Success:', data);
                // Close modal and show success message
                var convertModalEl = document.getElementById('convertModal');
                var convertModal = bootstrap.Modal.getInstance(convertModalEl);
                convertModal.hide();
                alert('Conversion successful! Please refresh the page for balance update !');
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    
        });
        
        document.getElementById('transferForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("Form submission prevented with vanilla JS");
        
            var username = document.getElementById('username').value;
            var amount = document.getElementById('transferAmount').value;
            console.log('Amount to transfer:', amount, ' to: ', username);
        
            var csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMetaTag) {
                console.error('CSRF token not found');
                return; // Exit the function if CSRF token is missing
            }
            var csrfToken = csrfMetaTag.getAttribute('content');
        
            fetch('/user/transferto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount: amount, username: username })
            })
            .then(response => {
                if (!response.ok) {
                    // If server responds with a bad status, parse the JSON to get the error and then throw it
                    return response.json().then(data => {
                        throw new Error(data.error ? data.error : 'Something went wrong');
                    });
                }
                return response.json(); // Parse the JSON response on success
            })
            .then(data => {
                console.log('Success:', data);
                var transferModalEl = document.getElementById('transferModal');
                var transferModal = bootstrap.Modal.getInstance(transferModalEl);
                transferModal.hide();
                // Use the success message from the server or a default success message
                alert(data.notify.message || 'Conversion successful! Please refresh the page for balance update !');
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message); // Show the error message from the server or the default error
            });
        });
</script>
@endpush
