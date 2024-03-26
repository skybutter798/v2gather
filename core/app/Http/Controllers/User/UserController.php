<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Lib\Mlm;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\PtcView;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function home()
    {


        $pageTitle                = 'Dashboard';
        $user                     = auth()->user();
        $data['clicks']           = $user->clicks->count();
        $data['rem_clicks']       = $user->daily_ad_limit - $user->clicks->where('vdt', Date('Y-m-d'))->count();
        $data['today_clicks']     = $user->clicks->where('vdt', Date('Y-m-d'))->count();
        $data['balance']          = $user->balance;
        $data['totalDeposit']     = Deposit::where('user_id', $user->id)->successful()->sum('amount');
        $data['totalWithdraw']    = Withdrawal::where('user_id', $user->id)->approved()->sum('amount');
        $data['completeWithdraw'] = Withdrawal::where('user_id', $user->id)->approved()->count();
        $data['pendingWithdraw']  = Withdrawal::where('user_id', $user->id)->pending()->count();
        $data['rejectedWithdraw'] = Withdrawal::where('user_id', $user->id)->rejected()->count();
        $data['total_ref']        = User::where('ref_by', $user->id)->count();
        $data['totalBvCut']       = BvLog::where('user_id', $user->id)->decreaseTran()->sum('amount');
        $data['totalInvest']      = $user->total_invest;
        $data['totalRefCom']      = $user->total_ref_com;
        $data['totalBinaryCom']   = $user->total_binary_com;
        $data['totalLeft']        = $user->userExtra->free_left + $user->userExtra->paid_left;
        $data['totalRight']       = $user->userExtra->free_right + $user->userExtra->paid_left;
        $data['totalBv']          = $user->userExtra->bv_left + $user->userExtra->bv_right;
        $data['leftBv']           = $user->userExtra->bv_left;
        $data['rightBv']          = $user->userExtra->bv_right;
        $data['RP']               = $user->RP; 
        $data['V2P']              = $user->V2P;
        $data['EP']               = $user->EP;

        $ptc = PtcView::where('user_id', $user->id)->get(['vdt', 'amount']);

        $data['amount'] = $ptc->groupBy('vdt')
            ->map(function ($item, $key) {
                return collect($item)->sum('amount');
            })->sort()->reverse()->take(7)->toArray();

        $chart['click'] = $ptc->groupBy('vdt')
            ->map(function ($item, $key) {
                return collect($item)->count();
            })->sort()->reverse()->take(7)->toArray();

        $chart['amount'] = $ptc->groupBy('vdt')
            ->map(function ($item, $key) {
                return collect($item)->sum('amount');
            })->sort()->reverse()->take(7)->toArray();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'data', 'chart'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $general   = gs();
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);

        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);

        if ($response) {
            $user->tsc = null;
            $user->ts  = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }

        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function invest()
    {
        $pageTitle    = 'Investment Log';
        $transactions = $this->report('purchased_plan');
        return view($this->activeTemplate . 'user.invests', compact('pageTitle', 'transactions',));
    }

    public function refCom()
    {
        $pageTitle    = 'Referral Commissions';
        $transactions = $this->report('referral_commission');
        return view($this->activeTemplate . 'user.invests', compact('pageTitle', 'transactions',));
    }

    public function binaryCom()
    {
        $pageTitle    = "Binary Commission";
        $transactions = $this->report('binary_commission');
        return view($this->activeTemplate . 'user.invests', compact('pageTitle', 'transactions',));
    }

    protected function report($remark)
    {
        $transactions = Transaction::where('user_id', auth()->id())->where('remark', $remark)->searchable(['trx'])->orderBy('id', 'desc')->paginate(getPaginate());
        return $transactions;
    }

    public function attachmentDownload($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function kycForm()
    {

        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }

        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = 'KYC Form';
        $form      = Form::where('act', 'kyc')->first();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user      = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form           = Form::where('act', 'kyc')->first();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData       = $formProcessor->processFormData($request, $formData);
        $user           = auth()->user();
        $user->kyc_data = $userData;
        $user->kv       = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::PROFILE_COMPLETE) {
            return to_route('user.home');
        }

        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::PROFILE_COMPLETE) {
            return to_route('user.home');
        }

        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = Status::PROFILE_COMPLETE;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function loginHistory()
    {
        $pageTitle = "User Login History";
        $loginLogs = auth()->user()->loginLogs()->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.logins', compact('pageTitle', 'loginLogs'));
    }

    public function balanceTransfer()
    {

        if (gs()->balance_transfer == Status::NO) {
            $notify[] = ['error', 'Balance transfer currently off by system. Please try again later'];
            return back()->withNotify($notify);
        }

        $pageTitle = "Balance Transfer";
        return view($this->activeTemplate . 'user.balance_transfer', compact('pageTitle'));
    }
    
    public function convert(Request $request) {
        $amount = $request->input('amount');
        $user = auth()->user();
    
        // Validate that the user has enough balance
        if ($amount > $user->balance) {
            Log::warning('Conversion failed due to insufficient balance', ['user_id' => $user->id, 'attempted_amount' => $amount]);
             return response()->json(['error' => 'Insufficient balance.'], 422); // 422 Unprocessable Entity
        }
    
        // Deduct the amount from the user's current balance and add to V2P
        $user->balance -= $amount;
        $user->V2P += $amount;
        $user->save();
    
        $trx = getTrx(); // Assuming getTrx() is a method to generate a unique transaction ID
    
        // Log the conversion
        Log::info('Balance conversion initiated', ['user_id' => $user->id, 'amount' => $amount, 'trx_id' => $trx]);
    
        // Record the transaction for deducting the balance
        $transaction = new Transaction();
        $transaction->fill([
            'user_id'      => $user->id,
            'amount'       => $amount,
            'post_balance' => $user->balance,
            'charge'       => 0,
            'trx_type'     => '-',
            'details'      => 'Balance convert to V2P',
            'trx'          => $trx,
            'remark'       => 'convert_sent',
        ]);
        $transaction->save();
    
        // Notify the user about the balance deduction
        notify($user, 'BAL_SEND', [
            'amount'      => $amount,
            'username'    => $user->username,
            'trx'         => $trx,
            'currency'    => gs()->cur_text,
            'charge'      => 0,
            'balance_now' => getAmount($user->balance),
        ]);
    
        // Record the transaction for adding to V2P
        $transactionV2P = new Transaction();
        $transactionV2P->fill([
            'user_id'      => $user->id,
            'amount'       => $amount,
            'post_balance' => $user->V2P,
            'trx_type'     => '+',
            'details'      => 'V2P convert from WP',
            'trx'          => $trx,
            'remark'       => 'convert_receive',
        ]);
        $transactionV2P->save();
    
        // Notify the user about the V2P balance addition
        notify($user, 'BAL_RECEIVE', [
            'amount'      => $amount,
            'currency'    => gs()->cur_text,
            'trx'         => $trx,
            'username'    => $user->username,
            'charge'      => 0,
            'balance_now' => getAmount($user->V2P),
        ]);
    
        // Log the successful conversion
        Log::info('Balance conversion successful', ['user_id' => $user->id, 'amount' => $amount, 'trx_id' => $trx]);
    
        return response()->json(['success' => 'Balance transferred successfully.', 'newBalance' => $user->balance, 'newV2P' => $user->V2P]);
    }
    
    public function transferto(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'amount'   => 'required|numeric|gte:0',
        ]);
    
        $user         = auth()->user();
        $transferUser = User::where('username', $request->username)->orWhere('email', $request->username)->first();
    
        if (!$transferUser) {
            $notify[] = ['error', 'User not found'];
            Log::warning('Balance transfer failed: User not found', ['requested_username' => $request->username]);
            return response()->json(['error' => 'User not found'], 422);
        }
    
        if ($user->id == $transferUser->id) {
            $notify[] = ['error', 'Balance transfer not possible in your own account'];
            Log::warning('Balance transfer failed: Self transfer attempt', ['user_id' => $user->id]);
            return response()->json(['error' => 'Balance transfer not possible in your own account'], 422);
        }
    
        $charge      = gs()->balance_transfer_fixed_charge + (($request->amount * gs()->balance_transfer_percent_charge) / 100);
        $totalAmount = $request->amount + $charge;
    
        if ($totalAmount > $user->V2P) {
            $notify[] = ['error', 'Insufficient balance.'];
            Log::warning('Balance transfer failed: Insufficient balance', ['user_id' => $user->id, 'required' => $totalAmount, 'available' => $user->V2P]);
            return response()->json(['error' => 'Insufficient balance.'], 422);
        }
    
        $user->V2P -= $totalAmount;
        $user->save();

        $trx = getTrx();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'V2P transferred to ' . $transferUser->username;
        $transaction->trx          = $trx;
        $transaction->remark       = 'v2p_transfer';
        $transaction->save();
        
        $transferUser->V2P += $request->amount;
        $transferUser->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $transferUser->id;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $transferUser->balance;
        $transaction->trx_type     = '+';
        $transaction->details      = 'V2P received form   ' . $user->username;
        $transaction->trx          = $trx;
        $transaction->remark       = 'v2p_receive';
        $transaction->save();
    
        Log::info('Balance transfer successful', [
            'from_user_id' => $user->id,
            'to_user_id' => $transferUser->id,
            'amount' => $request->amount,
            'charge' => $charge,
            'total_amount' => $totalAmount,
        ]);
    
        $notify = [
            'type' => 'success',
            'message' => 'Operation successful!',
        ];
        
        return response()->json([
            'notify' => $notify
        ]);
    }


    public function transfer(Request $request)
    {

        if (gs()->balance_transfer == Status::NO) {
            $notify[] = ['error', 'Balance transfer currently off by system. Please try again later'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'username' => 'required',
            'amount'   => 'required|numeric|gte:0',
        ]);

        $user         = auth()->user();
        $transferUser = User::where('username', $request->username)->orWhere('email', $request->username)->first();

        if (!$transferUser) {
            $notify[] = ['error', 'User not found'];
            return back()->withNotify($notify)->withInput();
        }

        if ($user->id == $transferUser->id) {
            $notify[] = ['error', 'Balance transfer not possible in your own account'];
            return back()->withNotify($notify)->withInput();
        }

        $charge      = gs()->balance_transfer_fixed_charge + (($request->amount * gs()->balance_transfer_percent_charge) / 100);
        $totalAmount = $request->amount + $charge;

        if ($totalAmount > $user->balance) {
            $notify[] = ['error', 'Insufficient balance.'];
            return back()->withNotify($notify);
        }

        $user->balance -= $totalAmount;
        $user->save();

        $trx = getTrx();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Balance transferred to ' . $transferUser->username;
        $transaction->trx          = $trx;
        $transaction->remark       = 'transfer';
        $transaction->save();

        notify($user, 'BAL_SEND', [
            'amount'      => getAmount($request->amount),
            'username'    => $transferUser->username,
            'trx'         => $transaction->trx,
            'currency'    => gs()->cur_text,
            'charge'      => getAmount($charge),
            'balance_now' => getAmount($user->balance),
        ]);

        $transferUser->balance += $request->amount;
        $transferUser->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $transferUser->id;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $transferUser->balance;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Balance received form   ' . $user->username;
        $transaction->trx          = $trx;
        $transaction->remark       = 'balance_receive';
        $transaction->save();

        notify($transferUser, 'BAL_RECEIVE', [
            'amount'      => getAmount($request->amount),
            'currency'    => gs()->cur_text,
            'trx'         => $trx,
            'username'    => $user->username,
            'charge'      => 0,
            'balance_now' => getAmount($transferUser->balance),
        ]);
        $notify[] = ['success', 'Balance transferred successfully.'];
        return back()->withNotify($notify);
    }

    public function referral()
    {
        $pageTitle = "My Referral";
        $referrals = User::where('ref_by', auth()->user()->id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.referral.index', compact('pageTitle', 'referrals'));
    }

    public function myTree()
    {
        $pageTitle = "My Tree";
        $mlm       = new Mlm();
        $tree      = $mlm->showTreePage(auth()->user());
        return view($this->activeTemplate . 'user.referral.my_ref', compact('pageTitle', 'mlm', 'tree'));
    }

    public function binarySummary()
    {
        $pageTitle = "Binary Summary";
        $binaries  = UserExtra::where('user_id', auth()->id())->firstOrFail();
        return view($this->activeTemplate . 'user.binary_summary', compact('binaries', 'pageTitle'));
    }

    public function searchUser(Request $request)
    {
        $user = User::where('username', $request->username)->orWhere('email', $request->username)->count();

        if ($user) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    
    public function getUserIdByUsername($username)
    {
        $user = User::where('username', $username)->first(['id']); // Only select the id to optimize the query
    
        if ($user) {
            return response()->json(['userId' => $user->id]);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

}
