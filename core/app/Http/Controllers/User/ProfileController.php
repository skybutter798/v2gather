<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user      = auth()->user();
        return view($this->activeTemplate . 'user.profile_setting', compact('pageTitle', 'user'));
    }

    public function submitProfile(Request $request)
    {

        $request->validate([
            'firstname' => 'required|string',
            'lastname'  => 'required|string',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required'  => 'Last name field is required',
        ]);

        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }


        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;

        $user->address = [
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'country' => @$user->address->country,
            'city'    => $request->city,
        ];

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        $user      = auth()->user();
        return view($this->activeTemplate . 'user.password', compact('pageTitle', 'user'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        $general            = gs();

        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        $user = auth()->user();

        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changes successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}