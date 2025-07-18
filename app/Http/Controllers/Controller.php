<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function checkpermission($permission)
    {
        return true;
        $user = Auth::user();
        if (in_array(Auth::user()->register_by, ['ADMIN', 'EMPLOYEE'])) {
            $check_permission = DB::table('modules')->where('url', $permission)->first();
        } else if($user->register_by == 'EMPLOYEE') {
            $user_role = DB::table('user_roles')->where('user_id', @$user->id)->get();
            foreach (@$user_role as $role) {
                $module_role[] = DB::table('role_modules')->where('role_id', @$role->role_id)->get();
            }
            if(@$module_role) {
                foreach($module_role as $module) {
                    foreach($module as $id) {
                        $module_id[] = $id->module_id;
                    }
                }
            }
            if(is_array(@$module_id)){
                $check_permission = DB::table('modules')->orderBy('hierarchy')->where('view','=',1)->whereIn('id', @$module_id)->where('url', $permission)->first();
            } else {
                $check_permission = null;
            }
        } else {
            $check_permission = DB::table('modules')->where('url', $permission)->where('view','=',1)->first();
        }

        $access = false;
        if ($check_permission !== null) {
            $access = true;
            return false;
        }
        if ($access == false) {
            return \Redirect::to('dashboard')->send()->with('error', 'Sorry, You Have No Permission.');
        }
    }
}
