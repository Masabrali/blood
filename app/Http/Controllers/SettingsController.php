<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App\Http\Middleware\RedirectUser;

use App\Http\Controllers\SMSController;
use App\Http\Controllers\ImageController;

use App\Mail\EmailVerification;

use App\User;
use App\Avatar;
use App\PasswordReset;

class SettingsController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('sessions');
    }

    /**
     * Get the User Avatars
     *
     * @param integer $user
     * @return View
     */
    public function getAvatars($user) {
        if (!Auth::check()) return redirect('/');
        else {
            if (!isset($user)) return;
            else return Avatar::where('user', $user)->get();
        }
    }

    /**
     * Edit User Settings
     * Validate Avatar Picture
     *
     * @param Array $data
     * @return Validator
    */
    protected function validateAvatar($data) {

        $validator = Validator::make($data, [
            'avatar'=>'sometimes|nullable|image|mimes:jpeg,png,jpg,svg|max:25600'
        ]);

        $validator->validate();

        return $validator;
    }

    /**
     * Edit the User Settings
     * Change the User Avatar
     *
     * @param integer $stage
     * @param Request $request
     * @return Redirect
     */
    public function editAvatar(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $user = Auth::user();

                $data = $request->all();

                $validator = $this->validateAvatar($data);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {

                    // Get the user
                    $_user = User::find($user->id);

                    // Upload user avatar and the file to the database
                    $_avatar = $request->file('avatar');

                    if ((isset($_avatar) && !empty($_avatar))) {

                        $name = $_avatar->getClientOriginalName();

                        $__avatar = Avatar::where('user', $user->id)->where('name', $name)->get();

                        if (!$__avatar->isEmpty()) {
                            $validator->getMessageBag()->add('avatar', 'Avatar already uploaded once. Choose a different one.');

                            return redirect()->back()->withErrors($validator)->withInput();
                        } else {

                            // Store avatars
                            $avatar = Avatar::create([
                                'user' => $user->id,
                                'name' => $name,
                                'type' => $_avatar->getMimeType(),
                                'size' => $_avatar->getSize(),
                                'url' => ImageController::compress($_avatar->store('public/users/'.date('FY')))
                            ]);

                            // Update user avatar
                            $_user->avatar = $avatar->url;

                            $_user->save();
                        }

                    } else if (isset($data['_avatar']) && !empty($data['_avatar'])) {

                        $avatar = Avatar::where('id', $data['_avatar'])->where('user', $user->id)->first();

                        // Update user avatar
                        $_user->avatar = $avatar->url;

                        $_user->save();

                    }

                    return redirect()->back()->with('success', true);
                }
            }
        }
    }

    /**
     * Edit User Settings
     * Validate Information
     *
     * @param Array $data
     * @return Validator
    */
    protected function validateInformation($data) {

        $validator = Validator::make($data, [
            'firstname'=>'required|string',
            'middlename'=>'required_if:middlename,~null,string',
            'lastname'=>'required|string'
        ]);
        $validator->validate();

        return $validator;

    }

    /**
     * Edit User Settings
     * Edit User Information
     *
     * @param Request $request
     * @return Redirect
     */
    public function editInfo(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $user = Auth::user();

                $data = $request->all();

                $validator = $this->validateInformation($data);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {

                    // Get the user
                    $_user = User::find($user->id);

                    // Edit basic user information
                    if ($user->firstname != $data['firstname']) $_user->firstname = ucfirst(strtolower($data['firstname']));

                    if ($user->middlename != $data['middlename']) $_user->middlename = ucfirst(strtolower($data['middlename']));

                    if ($user->lastname != $data['lastname']) $_user->lastname = ucfirst(strtolower($data['lastname']));

                    $_user->save();

                    return redirect()->back()->with('success', true);

                }
            }
        }
    }

    /**
     * Phone and Email verification
     * Generate Verfication Code
     *
     * @param Array $data
     * @return String
    */
    public function generateVerificationCode($data) {

        if (isset($data) && !empty($data)) {

            while(true) {

                $choice = random_int(0, 1);
                $_choice = random_int(0, 1);
                $_code = strtoupper(bin2hex(random_bytes(6)));

                $code = "";

                if ($_choice == 0)
                    while ($choice < 12) {
                        $code .= $_code[$choice];
                        $choice += 2;
                    }
                else if ($_choice == 1) {
                    $choice = 11 - $choice;

                    while ($choice >= 0) {
                        $code .= $_code[$choice];
                        $choice -= 2;
                    }
                }

                $validator = Validator::make(['code'=>$code],[
                    'code'=>'required|unique:users,email_verification|unique:users,phone_verification|size:6'
                ]);

                if (!$validator->fails()) break;

            }

            return $code;

        } else return false;

    }

    /**
     * Edit User Settings
     * Sending Verification Code to Phone or Email
     *
     * @param Array $data
     * @param Illuminate\Http\Request $request
     * @param Boolean $resend
     * @return App\User
    */
    protected function sendCode($user, Request $request, $resend) {

        $code_sent = $request->session()->get('code_sent');

        if (!empty($code_sent)) return true;
        else {
            if (!$user || !isset($user) || empty($user)) return redirect()->back();
            else {

                $verification = $request->session()->get('verification');

                if (empty($verification)) return redirect()->back();
                else {

                    if ($resend == true)
                        $verification_code = $this->generateVerificationCode($user);
                    else {

                        $verification_code = $request->session()->get('verification_code');

                        if (empty($verification_code))
                            $verification_code = $this->generateVerificationCode($user);

                    }

                    if (filter_var($verification, FILTER_VALIDATE_EMAIL))
                        Mail::to($verification)
                              ->send(new EmailVerification($user, $verification_code));
                    else
                        SMSController::send($verification, "Your NBTP Database verification code is $verification_code");

                    $request->session()->put(['verification_code'=>$verification_code]);

                    $request->session()->put(['code_sent'=>true]);

                    return true;
                }
            }
        }
    }

    /**
     * Phone or Email Verification
     * Re-Sending Verification Code to Phone or Email
     *
     * @param Integer $stage
     * @param Illuminate\Http\Request $request
     * @return string
    */
    public function resendCode($settings, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($settings != 'phone' && $settings != 'email') return redirect()->back();
            else {

                $request->session()->put(['resend'=>true]);

                $request->session()->forget('code_sent');

                $this->sendCode(Auth::user(), $request, true);

                return redirect()->back()->with('success', true);
            }
        }
    }

    /**
     * Edit User Settings
     * Phone regular expression.
     *
     * @var string
     */
    protected $phone_regex = "/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/";
    /**
     * Edit User Settings
     * Validate Phone
     *
     * @param Array $data
     * @return Validator
    */
    protected function validatePhone($data) {

        $validator = Validator::make($data, [
            'phone' => 'required|string|min:10|unique:users,phone|regex:'.$this->phone_regex
        ]);
        $validator->validate();

        return $validator;

    }
    /**
     * Edit User Settings
     * Edit User Phone number
     *
     * @param integer $stage
     * @param Request $request
     * @return Redirect
     */
    public function editPhone(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $user = Auth::user();

                $data = $request->all();

                $validator = $this->validatePhone($data);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {
                    // Get the user
                    $_user = User::find($user->id);

                    // Edit basic user information
                    if ($user->phone == $data['phone']) return redirect()->back();
                    else {

                        $request->session()->put(['verification'=>$data['phone']]);

                        $resend = $request->session()->get('resend');
                        $code_sent = $request->session()->get('code_sent');

                        if ((!isset($resend) || empty($resend)) && (!isset($code_sent) || empty($code_sent)))
                            $this->sendCode($user, $request, false);

                        return redirect()->back();

                    }
                }
            }
        }
    }
    /**
     * Edit User Settings
     * Validate Email
     *
     * @param Array $data
     * @return Validator
    */
    protected function validateEmail($data) {

        $validator = Validator::make($data, [
            'email' => 'required|string|email|min:10|unique:users,email'
        ]);
        $validator->validate();

        return $validator;

    }
    /**
     * Edit User Settings
     * Edit User Email Address
     *
     * @param integer $stage
     * @param Request $request
     * @return Redirect
     */
    public function editEmail(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $user = Auth::user();

                $data = $request->all();

                $validator = $this->validateEmail($data);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {
                    // Get the user
                    $_user = User::find($user->id);

                    // Edit basic user information
                    if ($user->email == $data['email']) return redirect()->back();
                    else {

                        $request->session()->put(['verification'=>$data['email']]);

                        $resend = $request->session()->get('resend');
                        $code_sent = $request->session()->get('code_sent');

                        if ((!isset($resend) || empty($resend)) && (!isset($code_sent) || empty($code_sent)))
                            $this->sendCode($user, $request, false);

                        return redirect()->back();

                    }
                }
            }
        }
    }
    /**
     * Edit User Settings
     * Cancel Email or Phone Verification
     *
     * @param Integer $stage
     * @param Illuminate\Http\Request $request
     * @return string
    */
    public function cancelVerification($settings, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($settings != 'phone' && $settings != 'email') return redirect()->back();
            else {

                $user = Auth::user();

                $request->session()->forget('verification');

                $request->session()->forget('code_sent');

                $request->session()->forget('resend');

                $request->session()->forget('verification_code');

                return redirect()->back()->with('success', true);

            }
        }
    }

    /**
     * Edit User Settings
     * Edit User Email Address and Phone
     * Validate Code
     *
     * @param Request $request
     * @return Validator
    */
    protected function validateCode($data, Request $request) {

        $validator = Validator::make($data, [
            'code'=>'required|size:6|string:=='.$request->session()->get('verification_code')
        ]);
        $validator->validate();

        return $validator;
    }
    /**
     * Edit User Settings
     * Edit User Email Address and Phone
     * verifyCode
     *
     * @param Illuminate\Http\Request $request
     * @param Integer $stage
     * @return redirect
    */
    public function verifyCode($settings, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if($request->method() != 'POST') return redirect()->back();
            else {
                if ($settings != 'email' && $settings != 'phone') return redirect()->back();
                else {

                    $user = Auth::user();

                    $verification = $request->session()->get('verification');

                    if (empty($verification)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $data['code'] = strtoupper($data['code']);

                        $validator = $this->validateCode($data, $request);

                        if ($validator->fails())
                            return redirect()->withErrors($validator)->withInputs();
                        else {

                            $code = $request->session()->get('verification_code');

                            if (!$code || !isset($code) || empty($code)) {
                                $validator->getMessageBag()->add('code', 'Verification Code has expired. Click Resend Code to get a new Verification code.');

                                return redirect()->back()->withErrors($validator)->withInput();

                            } else {

                                if ($code != $data['code']) {
                                    $validator->getMessageBag()->add('code', 'Verification failed. Wrong Verification Code.');

                                    return redirect()->back()->withErrors($validator)->withInput();

                                } else {

                                    $_user = User::find($user->id);

                                    if (!filter_var($verification, FILTER_VALIDATE_EMAIL)) {

                                        $_user->phone = $verification;

                                        $_user->phone_verification = $data['code'];

                                    } else {

                                        $_user->email = $verification;

                                        $_user->email_verification = $data['code'];
                                    }

                                    $_user->save();

                                    $request->session()->forget('verification');

                                    $request->session()->forget('code_sent');

                                    $request->session()->forget('resend');

                                    $request->session()->forget('verification_code');

                                    return redirect()->back()->with('success', true)->with('verified', true);

                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
    * Edit User Settings
    * Edit Password
    * validatePassword
    *
    * @param Array $data
    * @return Validator
    */
    protected function validatePassword($data, $user) {

        $validator = Validator::make($data, [
            'old_password' => 'required|string|min:6|different:password',
            'password' => 'required|string|min:6|different:old_password|confirmed'
        ]);
        $validator->validate();

        return $validator;

    }
    /**
     * Edit User Settings
     * Edit User Password
     *
     * @param Request $request
     * @return Redirect
     */
    public function editPassword(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $user = Auth::user();

                $data = $request->all();

                $validator = $this->validatePassword($data, $user);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {

                    if (!Hash::check($data['old_password'], $user->password)) {

                        $validator->getMessageBag()->add('old_password', 'The old password provided mismatches your password.');

                        return redirect()->back()->withErrors($validator)->withInput();

                    } else {

                        if (!Hash::check($data['password'], $user->password)) {
                            // Get the user
                            $_user = User::find($user->id);

                            // Edit basic user information
                            $_user->password = bcrypt($data['password']);

                            // Record password reset
                            $password_reset = PasswordReset::create([
                                'user' => $user->id,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'token' => bcrypt(random_bytes(255))
                            ]);

                            // Complete the editing
                            $_user->save();

                        }

                        return redirect()->back()->with('success', true);

                    }
                }
            }
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function load($settings, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (!isset($settings) || empty($settings)) return redirect()->back();
            else {

                $user = Auth::user();

                if (!empty($user->email) && empty($user->email_verification)) {
                    if ($settings != 'email') return redirect('/settings/email');

                } else if (!empty($user->phone) && empty($user->phone_verification)) {
                    if ($settings != 'phone') return redirect('/settings/phone');

                }

                $verification = $request->session()->get('verification');

                if (($settings == 'phone' || $settings == 'email') && !empty($verification)) {

                    $resend = $request->session()->get('resend');
                    $code_sent = $request->session()->get('code_sent');

                    if ((!isset($resend) || empty($resend)) && (!isset($code_sent) || empty($code_sent)))
                        $this->sendCode($user, $request, false);

                } else $verification = null;

                return view('settings.settings', [
                    'title'=>ucfirst($settings).'s', 'user'=>Auth::user(), 'settings'=>$settings, 'verification'=>$verification
                ]);
            }
        }
    }
}
