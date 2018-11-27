<?php

namespace App\Http\Middleware;

use Closure;

class ManageSessions
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
        if (!$request->is('collections/edit') && !$request->is('collections/delete'))
            $request->session()->forget('collection');

        if (!$request->is('distributions/edit') && !$request->is('distributions/delete'))
            $request->session()->forget('distribution');

        if (!$request->is('transfers/edit') && !$request->is('transfers/delete'))
            $request->session()->forget('transfer');

        if (!$request->is('collections/add') && !$request->is('distributions/add') && !$request->is('transfers/add'))
            $request->session()->forget('previous');

        if (!$request->is('zones/edit') && !$request->is('zones/delete'))
            $request->session()->forget('zone');

        if (!$request->is('regions/edit') && !$request->is('regions/delete'))
            $request->session()->forget('region');

        if (!$request->is('districts/edit') && !$request->is('districts/delete'))
            $request->session()->forget('district');

        if (!$request->is('centers/edit') && !$request->is('centers/delete'))
            $request->session()->forget('center');

        if (!$request->is('groups/edit') && !$request->is('groups/delete'))
            $request->session()->forget('group');

        if (!$request->is('users/edit') && !$request->is('users/delete'))
            $request->session()->forget('user');

        return $next($request);
    }
}
