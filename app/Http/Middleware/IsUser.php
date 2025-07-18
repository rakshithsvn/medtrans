<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class IsUser
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
        if (Auth::user()->register_by == 'ADMIN' || Auth::user()->register_by == 'EMPLOYEE') {
            return redirect('dashboard');
        }

        return $next($request);
    }
}
