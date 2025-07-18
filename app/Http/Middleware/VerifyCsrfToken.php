<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
	    'api/v1/register',
        'api/v1/auth',
        'api/v1/logout',
        'api/v1/trip-update',
        'api/v1/fuel-update',
        'api/v1/checklist-update',

        'api/v1/postAddDealer',
        'api/v1/postAddProduct',
        'api/v1/addPurchaseEntry',
        'api/v1/uploadMedia',
        'api/v1/claimRewards',
    ];
}
