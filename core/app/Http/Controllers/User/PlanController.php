<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\Mlm;
use App\Models\BvLog;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PlanController extends Controller
{
	public function index()
	{
		$pageTitle       = "Plans";
		$plans           = Plan::active()->orderBy('price')->paginate(getPaginate());
		$gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
			$gate->where('status', 1);
		})->with('method')->orderby('name')->get();
		return view($this->activeTemplate . 'user.plan.index', compact('pageTitle', 'plans', 'gatewayCurrency'));
	}

	public function subscribe(Request $request)
	{
		$request->validate([
			'wallet_type' => 'required',
			'id'          => 'required',
		]);
        
		$wallet  = $request->wallet_type;
		$user    = auth()->user();
		$oldPlan = $user->plan_id;
		$plan    = Plan::findOrFail($request->id);

		if ($request->id == $user->plan_id) {
			$notify[] = ['error', 'You are ready subscribe this plan'];
			return back()->withNotify($notify);
		}

		/*if ($wallet != 'deposit_wallet') {
			$gate = GatewayCurrency::whereHas('method', function ($gate) {
				$gate->where('status', 1);
			})->find($request->wallet_type);

			if (!$gate) {
				$notify[] = ['error', 'Invalid gateway'];
				return back()->withNotify($notify);
			}

			if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
				$notify[] = ['error', 'Please follow deposit limit'];
				return back()->withNotify($notify);
			}

			$data = PaymentController::insertDeposit($gate, $plan);

			session()->put('Track', $data->trx);

			return to_route('user.deposit.confirm');
		}
		
		if ($user->balance < $plan->price) {
			$notify[] = ['error', 'Oops! You\'ve no sufficient balance'];
			return back()->withNotify($notify);
		}*/
		
		
		$walletBalance = 0;
        $balanceField = '';
    
        // Determine the wallet balance and corresponding field
        if ($wallet == 'wp_wallet') {
            $walletBalance = $user->balance;
            $balanceField = 'balance';
        } elseif ($wallet == 'rp_wallet') {
            $walletBalance = $user->RP; // Assuming RP is the field name for Reward Points
            $balanceField = 'RP';
        } else {
            $notify[] = ['error', 'Invalid wallet type'];
            return back()->withNotify($notify);
        }
        
        if ($walletBalance < $plan->price) {
            $notify[] = ['error', 'Oops! You have insufficient balance in your selected wallet'];
            return back()->withNotify($notify);
        }

		

		$user->plan_id = $plan->id;
		$user->balance -= $plan->price;
		$user->total_invest += $plan->price;
		$user->daily_ad_limit = $plan->daily_ad_limit;
		$user->save();

		$transaction               = new Transaction();
		$transaction->user_id      = $user->id;
		$transaction->amount       = $plan->price;
		$transaction->post_balance = $user->balance;
		$transaction->trx_type     = '-';
		$transaction->details      = 'Purchased ' . $plan->name;
		$transaction->trx          = getTrx();
		$transaction->remark       = 'purchased_plan';
		$transaction->save();

		notify($user, 'PLAN_PURCHASED', [
			'plan'         => $plan->name,
			'amount'       => getAmount($plan->price),
			'currency'     => gs()->cur_text,
			'trx'          => $transaction->trx,
			'post_balance' => getAmount($user->balance) . ' ' . gs()->cur_text,
		]);

		$mlm = new Mlm($user, $plan, $transaction->trx);

		if ($oldPlan == 0) {
			$mlm->updatePaidCount();
		}

		$mlm->updateBv();

		if ($plan->tree_com > 0) {
			$mlm->treeCommission();
		}

		$mlm->referralCommission();

		$notify[] = ["success", 'Plan purchased successfully'];
		return to_route('user.home')->withNotify($notify);
	}

	public function bvLog(Request $request)
	{
		$pageTitle = "BV LOG";
		$bvLogs    = BvLog::where('user_id', auth()->id());

		if ($request->type) {

			if ($request->type == "leftBV") {
				$pageTitle = "Left BV";
				$bvLogs    = $bvLogs->left()->increaseTran();
			} elseif ($request->type == "rightBV") {
				$pageTitle = "Right BV";
				$bvLogs    = $bvLogs->right()->increaseTran();
			} elseif ($request->type == "cutBV") {
				$pageTitle = "Cut BV";
				$bvLogs    = $bvLogs->decreaseTran();
			} else {
				$pageTitle = "All Paid BV";
				$bvLogs    = $bvLogs->increaseTran();
			}
		}

		$bvLogs = $bvLogs->orderBy('id', 'desc')->paginate(getPaginate());
		return view($this->activeTemplate . 'user.bv_log', compact('bvLogs', 'pageTitle'));
	}
}
