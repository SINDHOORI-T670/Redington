<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckUser
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
        // if(Auth::User()->type == 2 && Auth::User()->verify_status == 1){
        //     // dd("customer");
        //     return redirect('customer/home');
        // }
        // else if(Auth::User()->type == 2 && Auth::User()->verify_status == 0){
        //     Session::flash('message','Wrong Password or your account is not yet verified !');
        //     return redirect('/login');
        // }
        // else if(Auth::User()->type == 3 && Auth::User()->verify_status == 1){
        //     dd("Hello Partner");
        // }
        // else if(Auth::User()->type == 3 && Auth::User()->verify_status == 0){
        //     Session::flash('message','Wrong Password or your account is not yet verified !');
        //     return redirect('/login');
        // }
        // else{
        //     return $next($request);
        // }
        if( Auth::check() )
        {
            // if user admin take him to his dashboard
            if ( Auth::user()->isAdmin() ) {
                 return redirect(route('Admin-Home'));
            }

            // allow user to proceed with request
            else if ( Auth::user()->isCustomer() ) {
                    return $next($request);
            }
            else if ( Auth::user()->isPartner() ) {
                
                return $next($request);
            }
            else if(Auth::user()->isEmployee()){

                return $next($request);
            }
            
            else{
                return redirect('login');
            }
        }

        abort(404);  // for other user throw 404 error
    }
}
