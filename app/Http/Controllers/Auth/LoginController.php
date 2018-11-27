<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo() {
        return '/dashboard';
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    // protected function guard() {
    //     return Auth::guard('guard-name');
    // }

    /**
     * What to use to login users, email or username or phone.
     *
     * @var string
     */
    //  public function username() {
    //    return 'email';
    //  }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {

        $data = $request->all();

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            return [
                'phone'=>$data['email'],
                'password'=>$data['password']
            ];
        else
            return $request->only($this->username(), 'password');

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'getLogout']]);
    }
}
