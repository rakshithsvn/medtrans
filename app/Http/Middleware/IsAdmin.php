<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class IsAdmin
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
        if (!in_array(Auth::user()->register_by, ['ADMIN', 'EMPLOYEE', 'COORDINATOR'], true)) {
            return redirect('dashboard');
        }

        return $next($request);
    }
}
