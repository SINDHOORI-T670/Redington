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
        if(Auth::User() && Auth::User()->type == 1){
            return redirect('admin/home');
        }else{
            Session::flash('message','Invalid user name or password !');
            return redirect('admin/login');
        }
    }
}
