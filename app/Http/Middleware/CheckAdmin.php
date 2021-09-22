<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAdmin
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
        // return $next($request);
        if(Auth::User()->type == 1){
            if(Auth::User()->verify_status == 1){
                // dd("admin");
                return redirect('admin/home');
            }
            else{
                 Session::flash('message','Wrong Email and Password !');
                return redirect('admin/login');
            }
        }
        
        // else{
            return $next($request);
        // }
    }
}
