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
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
	public function index()
	{
	    $user    = auth()->user();

		$pageTitle       = "Plans";
		$plans = Plan::active()->where('name', '=', 'V100')->orderBy('price')->paginate(getPaginate());
		$plans300 = Plan::active()->where('name', '=', 'V300')->orderBy('price')->paginate(getPaginate());
		$gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
			$gate->where('status', 1);
		})->with('method')->orderby('name')->get();
		return view($this->activeTemplate . 'user.plan.index', compact('pageTitle', 'plans', 'gatewayCurrency', 'user', 'plans300'));
	}

	public function subscribe(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'v2p' => 'required|numeric',
        ]);
    
        $user = auth()->user();
        $oldPlan = $user->plan_id;
        $plan = Plan::findOrFail($request->id);
        $planPrice = $plan->price;
        $maxRpUsage = $planPrice * 0.1; // 10% of plan price for RP
        $rpInput = min($request->rp, $maxRpUsage); // Ensuring RP does not exceed its max limit
        $v2pInput = min($request->v2p, $planPrice - $rpInput);
    
        if ($request->id == $user->plan_id) {
            $notify[] = ['error', 'You are already subscribed to this plan'];
            Log::channel('plan')->warning('Plan subscription attempt for already subscribed plan.', [
                'user_id' => $user->id,
                'plan_id' => $request->id,
            ]);
            return back()->withNotify($notify);
        }
    
        // Check if user has enough RP and V2P
        if ($user->RP < $rpInput || $user->V2P < $v2pInput) {
            $notify[] = ['error', 'Insufficient RP or V2P balance.'];
            Log::channel('plan')->warning('Insufficient RP or V2P for plan purchase.', [
                'user_id' => $user->id,
                'plan_id' => $request->id,
                'required_RP' => $rpInput,
                'available_RP' => $user->RP,
                'required_V2P' => $v2pInput,
                'available_V2P' => $user->V2P,
            ]);
            return back()->withNotify($notify);
        }
        
        // Deduct the RP and V2P from user's account
        $user->RP -= $rpInput;
        $user->V2P -= $v2pInput;
        $user->plan_id = $plan->id;
        $user->total_invest += $plan->price;
        $user->daily_ad_limit = $plan->daily_ad_limit;
        $user->save();
    
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $plan->price;
        $transaction->post_balance = $user->V2P; // Assuming you want to log the remaining V2P balance
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased ' . $plan->name;
        $transaction->trx = $trx;
        $transaction->remark = 'purchased_plan';
        $transaction->save();
    
        Log::channel('plan')->info('--------------------------------------------------');

        Log::channel('plan')->info('Plan purchased', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'used_RP' => $rpInput,
            'used_V2P' => $v2pInput,
            'transaction_id' => $trx,
        ]);

    
        notify($user, 'PLAN_PURCHASED', [
            'plan' => $plan->name,
            'amount' => getAmount($plan->price),
            'currency' => gs()->cur_text,
            'trx' => $transaction->trx,
            'post_balance' => getAmount($user->V2P) . ' ' . gs()->cur_text,
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

	public function subscribev300(Request $request)
    {
        $request->validate([
            'rp' => 'required',
            'v2p' => 'required|numeric',
        ]);
    
        $user = auth()->user();
        $oldPlan = $user->plan_id;
        $current = $user->plan_3;
        $plan = Plan::findOrFail('1');

        $planPrice = 300;
        $maxRpUsage = $planPrice * 0.1;
        $rpInput = min($request->rp, $maxRpUsage);
        $v2pInput = min($request->v2p, $planPrice - $rpInput);
    
        if ($current == 1) {
            $notify[] = ['error', 'You are already subscribed to this plan'];
            Log::channel('plan')->warning('Subscription attempt to V300 by already subscribed user.', [
                'user_id' => $user->id,
            ]);
            return back()->withNotify($notify);
        }
    
        if ($oldPlan == 0) {
            $notify[] = ['error', 'You need to subscribe to V100 before this plan'];
            Log::channel('plan')->warning('Subscription attempt to V300 without V100 subscription.', [
                'user_id' => $user->id,
            ]);
            return back()->withNotify($notify);
        }
    
        // Check if user has enough RP and V2P
        if ($user->RP < $rpInput || $user->V2P < $v2pInput) {
            $notify[] = ['error', 'Insufficient RP or V2P balance.'];
            Log::channel('plan')->warning('Insufficient RP or V2P for V300 plan purchase.', [
                'user_id' => $user->id,
                'required_RP' => $rpInput,
                'available_RP' => $user->RP,
                'required_V2P' => $v2pInput,
                'available_V2P' => $user->V2P,
            ]);
            return back()->withNotify($notify);
        }
    
        // Deduct the RP and V2P from user's account
        $user->RP -= $rpInput;
        $user->V2P -= $v2pInput;
        $user->plan_3 = 1;
        $user->total_invest += $planPrice;
        $user->daily_ad_limit = 0;
        $user->save();
    
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $planPrice;
        $transaction->post_balance = $user->V2P; // Assuming post_balance should reflect remaining V2P
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased V300';
        $transaction->trx = $trx;
        $transaction->remark = 'purchased_plan';
        $transaction->save();
        
        Log::channel('plan')->info('--------------------------------------------------');
        Log::channel('plan')->info('V300 plan purchased', [
            'user_id' => $user->id,
            'plan_price' => $planPrice,
            'used_RP' => $rpInput,
            'used_V2P' => $v2pInput,
            'transaction_id' => $trx,
        ]);
    
        notify($user, 'PLAN_PURCHASED', [
            'plan' => 'V300',
            'amount' => getAmount($planPrice),
            'currency' => gs()->cur_text,
            'trx' => $transaction->trx,
            'post_balance' => getAmount($user->V2P) . ' ' . gs()->cur_text,
        ]);
    
        $mlm = new Mlm($user, $plan, $transaction->trx);
        $mlm->updateBv300();
    
        if ($plan->tree_com > 0) {
            $mlm->treeCommission();
        }
    
        $mlm->referralCommission300();
    
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
