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
        // if(Auth::User()->type == 1){
        //     if(Auth::User()->verify_status == 1){
        //         // dd("admin");
        //         return redirect('admin/home');
        //     }
        //     else{
        //          Session::flash('message','Wrong Email and Password !');
        //         return redirect('admin/login');
        //     }
        // }
        
        // else{
            // return $next($request);
        // }
        if( Auth::check() )
        {
            // dd(Auth::user()->isAdmin());
            if ( Auth::user()->isAdmin() ) {
                return $next($request);
            }else{
                return redirect('admin/login');
            }

            if ( Auth::user()->isCustomer() ) {
                 return redirect(route('Customer-Home'));
            }else{
                return redirect('login');
            }

            if ( Auth::user()->isPartner() ) {
                
                return redirect(route('Partner-Home'));
            }else{
                return redirect('login');
            }
            
            if(Auth::user()->isEmployee()){

                return redirect(route('Employee-Home'));
            }
            else{
                return redirect('login');
            }
        }

        abort(404);  // for other user throw 404 error
    }
}
