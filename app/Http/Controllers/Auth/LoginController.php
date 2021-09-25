<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    // protected function authenticated(Request $request, $user)
    // {
    //     // dd($user);
    //     // to admin dashboard
    //     if($user->isAdmin()) {
    //         // dd("ttt");
    //         return redirect(route('Admin-Home'));
    //     }

    //     // to user dashboard
    //     else if($user->isCustomer()) {
    //         return redirect(route('Customer-Home'));
    //     }

    //     abort(404);
    // }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
