<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Ptc;
use Illuminate\Http\Request;

class PtcController extends Controller
{
    public function index()
    {
        $pageTitle = 'PTC Ads';
        $ptcs      = Ptc::Searchable(['title'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.ptc.index', compact('pageTitle', 'ptcs'));
    }

    public function create()
    {
        $pageTitle = 'Create new PTC Ad';
        return view('admin.ptc.create', compact('pageTitle'));
    }

    public function store(Request $request, $id = 0)
    {

        $linkValidation = '';
        $bannerImage    = '';
        $script         = '';

        if (!$id) {
            $linkValidation = 'required_without_all:banner_image,script';
            $bannerImage    = 'required_without_all:website_link,script';
            $script         = 'required_without_all:website_link,banner_image';
        }

        $request->validate([
            'title'        => 'required|max:255',
            'ads_type'     => 'required|integer|in:1,2,3',
            'amount'       => 'required|numeric|gt:0',
            'duration'     => 'required|integer|min:1',
            'max_show'     => 'required|integer|min:1',
            'website_link' => 'nullable|url|' . $linkValidation,
            'banner_image' => 'nullable|mimes:jpeg,jpg,png,gif|' . $bannerImage,
            'script'       => 'nullable|' . $script,
        ]);

        if ($id) {
            $ptc     = Ptc::findOrFail($id);
            $message = "PTC ad updated successfully";
        } else {
            $ptc     = new Ptc();
            $message = "PTC ad created successfully";
        }

        $ptc->title    = $request->title;
        $ptc->amount   = $request->amount;
        $ptc->duration = $request->duration;
        $ptc->max_show = $request->max_show;
        $ptc->remain   = $request->max_show;
        $ptc->ads_type = $request->ads_type;

        if ($request->ads_type == Status::ADS_LINK) {
            $ptc->ads_body = $request->website_link;
        } elseif ($request->ads_type == Status::ADS_IMAGE) {

            if ($request->hasFile('banner_image')) {
                try {
                    $old       = $ptc->ads_body;
                    $directory = date("Y") . "/" . date("m") . "/" . date("d");
                    $path      = getFilePath('ptc') . '/' . $directory;
                    $filename  = fileUploader($request->banner_image, $path, null, $old);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Image could not be uploaded.'];
                    return back()->withNotify($notify);
                }

                $ptc->ads_body = $directory . '/' . $filename;
            }
        } else {
            $ptc->ads_body = $request->script;
        }

        $ptc->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit PTC Ad';
        $ptc       = Ptc::findOrFail($id);
        return view('admin.ptc.edit', compact('pageTitle', 'ptc'));
    }

    public function status($id)
    {
        return Ptc::changeStatus($id);
    }
}
