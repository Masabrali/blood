<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;

class RedirectUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) return redirect('/');
        else {

            $user = Auth::user();

            if (empty($user->email_verification) && empty($user->phone_verification)) {

                $verification = $request->session()->get('verification');

                if (!empty($user->email)) {
                    if (empty($verification))
                        $request->session()->put(['verification'=>$user->email]);

                    return redirect('/settings/email');

                } else if (!empty($user->phone)) {
                    if (empty($verification))
                        $request->session()->put(['verification'=>$user->phone]);

                    return redirect('/settings/phone');

                }

            } else return $next($request);
        }
    }
}
