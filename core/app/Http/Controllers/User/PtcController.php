<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ptc;
use App\Models\PtcView;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class PtcController extends Controller
{
    public function index()
    {
        $pageTitle = "PTC Ads";

        if (!auth()->user()->plan_id) {
            $notify[] = ['error', 'Please buy a plan to view ad'];
            return to_route('user.plan.index')->withNotify($notify);
        }

        $viewAds = PtcView::where('user_id', auth()->id())->where('vdt', Date('Y-m-d'))->pluck('ptc_id')->toArray();
        $ads     = Ptc::active()->where('remain', '>', 0)->whereNotIn('id', $viewAds)->searchable(['title'])->orderBy('remain', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.ptc.index', compact('ads', 'pageTitle'));
    }

    public function show($id)
    {

        $pageTitle = "Show PTC";
        try {
            $userAd = decrypt($id);
        } catch (Exception $e) {
            $notify[] = ['error', 'Something Wrong please try again'];
            return back()->withNotify($notify);
        }

        $ids = explode('|', $userAd);

        $adId   = $ids[0];
        $userId = $ids[1];

        if ($userId != auth()->id()) {
            $notify[] = ['error', "Oops! You are not eligible for this link"];
            return to_route('user.home')->withNotify($notify);
        }

        $ptc     = Ptc::active()->where('id', $adId)->where('remain', '>', 0)->firstOrFail();
        $viewAds = PtcView::where('user_id', auth()->id())->where('vdt', Date('Y-m-d'))->get();

        if ($viewAds->count() > auth()->user()->daily_ad_limit) {
            $notify[] = ['error', 'Oops! Your limit is over. You cannot see more ads today'];
            return back()->withNotify($notify);
        }

        if ($viewAds->where('ptc_id', $ptc->id)->first()) {
            $notify[] = ['error', 'You cannot see this add before 24 hour'];
            return back()->withNotify($notify);
        }

        $n1  = rand(0, 9);
        $n2  = rand(0, 9);
        $res = $n1 + $n2;

        return view($this->activeTemplate . 'user.ptc.show', compact('pageTitle', 'ptc', 'n1', 'n2', 'res'));
    }

    public function confirm(Request $request, $id)
    {
        $user = auth()->user();
        $request->validate([
            'num1'   => 'required|integer|between:0,10',
            "num2"   => 'required|integer|between:0,10',
            "result" => "required|integer|between:0,20",
            "res"    => "required|string",
        ]);

        if ($request->result != intval(decrypt($request->res))) {
            $notify[] = ['error', "Sorry the result of your calculation is invalid"];
            return redirect()->back()->withNotify($notify);
        }

        $userAd = decrypt($id);
        $ids    = explode('|', $userAd);
        $adId   = $ids[0];
        $userId = $ids[1];

        if ($userId != $user->id) {

            $notify[] = ['error', "Oops! You are not eligible for this link"];
            return redirect()->route('user.home')->withNotify($notify);
        }

        $ptc     = Ptc::active()->where('id', $adId)->where('remain', '>', 0)->firstOrFail();
        $viewAds = PtcView::where('user_id', $user->id)->where('vdt', Date('Y-m-d'))->get();

        if ($viewAds->count() >= $user->daily_ad_limit) {
            $notify[] = ['error', 'Oops! Your limit is over. You cannot see more ads today'];
            return back()->withNotify($notify);
        }

        if ($viewAds->where('ptc_id', $ptc->id)->first()) {
            $notify[] = ['error', 'You cannot see this add before 24 hour'];
            return back()->withNotify($notify);
        }

        $ptc->increment('showed');
        $ptc->decrement('remain');
        $ptc->save();
        $user->balance += $ptc->amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $ptc->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Earn amount from ads';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'earn';
        $transaction->save();

        $ptcView          = new PtcView();
        $ptcView->ptc_id  = $ptc->id;
        $ptcView->user_id = $user->id;
        $ptcView->amount  = $ptc->amount;
        $ptcView->vdt     = Date('Y-m-d');
        $ptcView->save();

        $notify[] = ['success', 'Successfully viewed this ads'];
        return redirect()->route('user.ptc.index')->withNotify($notify);
    }

    public function clicks()
    {
        $pageTitle = "PTC Clicks";
        $ptc       = PtcView::where('user_id', auth()->id())->selectRaw("SUM(amount) as amount,count(*) as clicks,vdt")->groupBy('vdt')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.ptc.clicks', compact('ptc', 'pageTitle'));
    }
}
