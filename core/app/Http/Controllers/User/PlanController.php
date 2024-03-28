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
	    $user    = auth()->user();

		$pageTitle       = "Plans";
		$plans           = Plan::active()->orderBy('price')->paginate(getPaginate());
		$gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
			$gate->where('status', 1);
		})->with('method')->orderby('name')->get();
		return view($this->activeTemplate . 'user.plan.index', compact('pageTitle', 'plans', 'gatewayCurrency', 'user'));
	}

	public function subscribe(Request $request)
	{
		$request->validate([
			
			'id'          => 'required',
			'v2p' => 'required|numeric',
		]);
        
		//$wallet  = $request->wallet_type;
		//$user    = auth()->user();
		//$oldPlan = $user->plan_id;
		//$plan    = Plan::findOrFail($request->id);
		
		$user = auth()->user();
		$oldPlan = $user->plan_id;
        $plan = Plan::findOrFail($request->id);
        $planPrice = $plan->price;
        $maxRpUsage = $planPrice * 0.1; // 10% of plan price for RP
        $rpInput = min($request->rp, $maxRpUsage); // Ensuring RP does not exceed its max limit
        $v2pInput = min($request->v2p, $planPrice - $rpInput);

		if ($request->id == $user->plan_id) {
			$notify[] = ['error', 'You are ready subscribe this plan'];
			return back()->withNotify($notify);
		}

		
        // Check if user has enough RP and V2P
        if ($user->RP < $rpInput || $user->V2P < $v2pInput) {
            $notify[] = ['error', 'Insufficient RP or V2P balance.'];
            return back()->withNotify($notify);
        }
        
        // Deduct the RP and V2P from user's account
        $user->RP -= $rpInput;
        $user->V2P -= $v2pInput;
		$user->plan_id = $plan->id;
		//$user->balance -= $plan->price;
		$user->total_invest += $plan->price;
		$user->daily_ad_limit = $plan->daily_ad_limit;
		$user->save();

		$transaction               = new Transaction();
		$transaction->user_id      = $user->id;
		$transaction->amount       = $plan->price;
		$transaction->post_balance = $v2pInput;
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
			'post_balance' => getAmount($v2pInput) . ' ' . gs()->cur_text,
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
		$pageTitle = "PB LOG";
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
