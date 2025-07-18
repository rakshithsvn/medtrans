<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    
        // if(!$request->expectsJson()) {
        //     if(count($guards) > 0 && $guards[0] === 'admission') {
        //         return route('auth.frontpage');
        //     } else {
        //         return route('login');
        //     }
        // }
    }

    // same as https://github.com/laravel/framework/blob/5.8/src/Illuminate/Auth/Middleware/Authenticate.php#L55
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }
        throw new \Illuminate\Auth\AuthenticationException(
             // we only change the redirectTo to refer to our method instead of the protected one
            'Unauthenticated.', $guards, $this->redirectTo($request, $guards)
        );
    }
}
