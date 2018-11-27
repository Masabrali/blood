<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
     * Where to redirect users after registration.
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Phone regular expression.
     *
     * @var string
     */
    protected $phone_regex = "/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/";

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        $data['phone'] = NULL;
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
          $data['phone'] = $data['email'];
          $data['email'] = NULL;
        }

        if (isset($data['email'])) $data['email'] = strtolower($data['email']);

        $validation = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6'
        ];

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {

            unset($validation['email']);

            $validation['phone'] = 'required|string|min:10|unique:users,phone|regex:'.$this->phone_regex;
        }

        return Validator::make($data, $validation);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $data['phone'] = NULL;
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
          $data['phone'] = $data['email'];
          $data['email'] = NULL;
        }

        return User::create([
            'firstname' => ucfirst(strtolower($data['firstname'])),
            'lastname' => ucfirst(strtolower($data['lastname'])),
            'email' => strtolower($data['email']),
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
        ]);

    }
}
