@php
    $latestTrx = getContent('transaction.content', true);
    $deposits = App\Models\Deposit::orderBy('id', 'desc')
        ->where('status', Status::PAYMENT_SUCCESS)
        ->take(10)
        ->with('user')
        ->get();
    $withdraws = App\Models\Withdrawal::orderBy('id', 'desc')
        ->where('status', Status::PAYMENT_SUCCESS)
        ->take(10)
        ->with('user')
        ->get();
@endphp

<section class="transaction-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <div class="section-header">
                    <h3 class="sub-title">{{ __(@$latestTrx->data_values->sub_heading) }}</h3>
                    <h2 class="section-title">{{ __(@$latestTrx->data_values->heading) }}</h2>
                </div>
            </div>
        </div>

        <ul class="nav nav-tab justify-content-center transaction-tab-menu">
            <li class="nav-item t-tab">
                <a href="#deposit" class="nav-link  active  text-dark " data-bs-toggle="tab">@lang('Latest Deposits')</a>
            </li>
            <li class="nav-item t-tab">
                <a href="#withdraw" class="nav-link text-dark" data-bs-toggle="tab">@lang('Latest Withdraws')</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane show fade active" id="deposit">
                <div class="transaction-table">
                    <table>
                        <thead>
                            <tr class="bg-2">
                                <th>@lang('Name')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deposits as $deposit)
                                <tr>
                                    <td>
                                        <div class="author">
                                            <div class="thumb">
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$deposit->user->image,isAvatar:true) }}" alt="user">
                                            </div>
                                            <div class="content">{{ @$deposit->user->fullName }}</div>
                                        </div>
                                    </td>
                                    <td>{{ showDateTime($deposit->created_at, 'd F, Y') }}</td>
                                    <td>{{ showAmount($deposit->amount) }} {{ __($general->cur_text) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="withdraw">
                <div class="transaction-table">
                    <table>
                        <thead>
                            <tr class="bg-2">
                                <th>@lang('Name')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdraws as $withdraw)
                                <tr>
                                    <td>
                                        <div class="author">
                                            <div class="thumb">
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$withdraw->user->image,isAvatar:true) }}" alt="user">
                                            </div>
                                            <div class="content">
                                                {{ @$withdraw->user->fullName }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ showDateTime($withdraw->created_at, 'd F, Y') }}</td>
                                    <td>{{ showAmount($withdraw->amount) }} {{ __($general->cur_text) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
