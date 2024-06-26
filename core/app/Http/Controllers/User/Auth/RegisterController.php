<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Mlm;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
        $this->middleware('registration.status')->except('registrationNotAllowed');
    }

    public function showRegistrationForm()
    {
        $ref = @$_GET['ref'];
        if ($ref) {
            session()->put('ref', $ref);
        }
        $position = @$_GET['position'];
        if ($position) {
            session()->put('position', $position);
        }

        $pageTitle = "Register";
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $position = session('position');
        $refUser = null;
        $joining = null;
        $pos = 1;
        if ($position) {
            $refUser = User::where('username', session('ref'))->first();
            if ($position == 'left')
                $pos = 1;
            else {
                $pos = 2;
            }
            $positioner = Mlm::getPositioner($refUser, $pos);
            $join_under = $positioner;
            $joining = $join_under->username;
        }
        return view($this->activeTemplate . 'user.auth.register', compact('pageTitle', 'mobileCode', 'countries', 'refUser', 'position', 'joining', 'pos'));
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = gs();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));
        $validate = Validator::make($data, [
            'referral' => 'required|exists:users,username',
            'position' => 'required|in:1,2',
            'email' => 'required|string|email|unique:users',
            'mobile' => 'required|regex:/^([0-9]*)$/',
            'password' => ['required', 'confirmed', $passwordValidation],
            'username' => 'required|unique:users|min:6',
            'captcha' => 'sometimes|required',
            'mobile_code' => 'required|in:' . $mobileCodes,
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'required|in:' . $countries,
            'agree' => $agree
        ]);

        return $validate;
    }

    public function register(Request $request){
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }


        $exist = User::where('mobile', $request->mobile_code . $request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }
        
        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
    
    public function registerdownline(Request $request)
    {
        $validation = $this->validator($request->all());
        if ($validation->fails()) {
            Log::warning('Registration validation failed.', ['errors' => $validation->errors()->all()]);
            return back()->withErrors($validation)->withInput();
        }
    
        if (!verifyCaptcha()) {
            Log::warning('Captcha verification failed.', ['email' => $request->email]);
            return back()->with('error', 'Invalid captcha provided')->withInput();
        }
    
        $exist = User::where('mobile', $request->mobile_code . $request->mobile)->orWhere('email', $request->email)->first();
        if ($exist) {
            Log::warning('User already exists.', ['mobile' => $request->mobile, 'email' => $request->email]);
            return back()->with('error', 'The mobile number or email already exists')->withInput();
        }
    
        $referUser = User::where('username', $request->referral)->firstOrFail();
        $pusername = $request->pusername;
        $puserId = User::where('username', $pusername)->value('id');
        
        $user = $this->createdownline($request->all(), $puserId);
        \Log::info('Dispatching Registered event', ['user_id' => $user->id]);
        event(new Registered($user));
        
        $this->postRegistrationDownline($request, $user);
        Log::info('User registered successfully.', ['id' => $user->id, 'username' => $user->username]);

        Auth::login($user);
        return back()->with('success', 'Registration successful.');
    }
    
    protected function postRegistrationDownline(Request $request, $user)
    {

        $userExtras = new UserExtra();
        $userExtras->user_id = $user->id;
        $userExtras->save();
        Mlm::updateFreeCount($user);
    
        //return to_route('user.home'); // Adjust as necessary
    }
    
    protected function createdownline(array $data, $puserId = null)
    {
        $general = gs();
    
        // Retrieve the referring user
        $referUser = User::where('username', $data['referral'])->first();
    
        // Initialize the new User model
        $user = new User();
        $user->email = strtolower($data['email']);
        $user->password = Hash::make($data['password']);
        $user->username = $data['username'];
        $user->ref_by = $referUser->id;
        $user->country_code = $data['country_code'];
        $user->mobile = $data['mobile_code'] . $data['mobile'];
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => $data['country'] ?? null,
            'city' => ''
        ];
    
        if ($puserId) {
            $user->pos_id = $puserId;
            $user->position = $data['position'];
        }
    
        $user->kv = $general->kv ? Status::NO : Status::YES;
        $user->ev = $general->ev ? Status::NO : Status::YES;
        $user->sv = $general->sv ? Status::NO : Status::YES;
        $user->ts = 0;
        $user->tv = 1;
        
        $user->save();
    
        return $user;
    }

    protected function create(array $data)
    {
        $general = gs();

        $referUser = User::where('username', $data['referral'])->first();
        $position = $data['position'];
        $positioner = Mlm::getPositioner($referUser, $position);
        //User Create
        $user = new User();
        $user->email = strtolower($data['email']);
        $user->password = Hash::make($data['password']);
        $user->username = $data['username'];
        $user->ref_by = $referUser->id;
        $user->pos_id = $positioner->id;
        $user->position = $position;
        $user->country_code = $data['country_code'];
        $user->mobile = $data['mobile_code'] . $data['mobile'];
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => ''
        ];
        $user->kv = $general->kv ? Status::NO : Status::YES;
        $user->ev = $general->ev ? Status::NO : Status::YES;
        $user->sv = $general->sv ? Status::NO : Status::YES;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();


        //Login Log Create
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
        
        if ($user->save()) {
            Log::info('New user created.', ['id' => $user->id, 'username' => $user->username]);
        } else {
            Log::error('Failed to create user.', ['data' => $data]);
        }
        return $user;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile', $request->mobile)->exists();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username', $request->username)->exists();
            $exist['type'] = 'username';
        }
        return response($exist);
    }

    public function registered(Request $request, $user)
    {
        try {
            Log::info('Registered method started', ['user_id' => $user->id]);
    
            $userExtras = new UserExtra();
            $userExtras->user_id = $user->id;
            $userExtras->save();
    
            Log::info('UserExtras saved', ['user_id' => $user->id]);
    
            Mlm::updateFreeCount($user);
    
            Log::info('MLM updateFreeCount completed', ['user_id' => $user->id]);
    
            return to_route('user.home');
        } catch (\Exception $e) {
            Log::error('Error in registered method', [
                'user_id' => $user->id,
                'error_message' => $e->getMessage()
            ]);
    
            // Depending on your application's needs, you might want to return a different response here.
            return back()->with('error', 'An error occurred during registration.');
        }
    }
}
