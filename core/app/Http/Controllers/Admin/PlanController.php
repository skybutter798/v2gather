<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $pageTitle = 'Plan';
        $plans     = Plan::orderBy('price')->paginate(getPaginate());
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function planSave(Request $request, $id = 0)
    {
        $request->validate([
            'name'           => 'required|unique:plans,name,' . $id,
            'price'          => 'required|numeric|min:0',
            'bv'             => 'required|min:0|integer',
            'ref_com'        => 'required|numeric|min:0',
            'tree_com'       => 'required|numeric|min:0',
            'daily_ad_limit' => 'required|integer|min:0',
        ]);

        if ($id) {
            $plan    = Plan::findOrFail($id);
            $message = "Plan updated successfully";
        } else {
            $plan    = new Plan();
            $message = "Plan save successfully";
        }

        $plan->name           = $request->name;
        $plan->price          = $request->price;
        $plan->bv             = $request->bv;
        $plan->ref_com        = $request->ref_com;
        $plan->tree_com       = $request->tree_com;
        $plan->daily_ad_limit = $request->daily_ad_limit;
        $plan->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }
}
