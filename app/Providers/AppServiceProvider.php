<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;
use Auth;
use DB;
use Route;
use Illuminate\Pagination\Paginator;
use Dotenv\Dotenv;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

	public function register()
	{
		if (file_exists(base_path('.env'))) {
			$dotenv = Dotenv::createImmutable(base_path());
			$dotenv->load();
		}
	}


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function($view){
            if(Auth::user()){
           
                view()->share('route', Route::current()->getName());
                
                if(Auth::user()->register_by == 'ADMIN' || Auth::user()->register_by == 'EMPLOYEE')
                {
                    $auth = Auth::user();
                    View::share('auth',$auth);
                }
            }
        });

        Paginator::useBootstrap();
    }
}
