<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckAdmin');
        $this->middleware('CheckUser');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        if(Auth::User() && Auth::User()->type == 4 ){
            if(Auth::User()->verify_status == 1){
                dd("Hello Employee");
            }else{
                Session::flash('message','Your account not yet verified !');
                return redirect('login');
            }
            
        }
        else if(Auth::User()->type == 4 && Auth::User()->verify_status == 0){
            Session::flash('message','Wrong Password or your account is not yet verified !');
            return redirect('/login');
        }
    }
}
