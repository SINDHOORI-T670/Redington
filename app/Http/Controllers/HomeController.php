<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
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
        // $this->middleware('CheckAdmin');
        // $this->middleware('CheckUser');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        if(Auth::User()->isAdmin()){
            return redirect(route('Admin_Dashboard'));
            // return redirect('admin/home');
        }else{
            // dd(Auth::User()->verify_status);
            if(Auth::user()->isCustomer() && Auth::User()->verify_status == 1){
                return redirect(route('Customer_Dashboard'));
            }elseif(Auth::user()->isPartner() && Auth::User()->verify_status == 1){
                dd("im partner");
                // return redirect('customer/home');
            }elseif(Auth::User()->isEmployee()==true && Auth::User()->verify_status == 1){
                return redirect(route('Employee_Dashboard'));
            }else{
                Session::flash('message','Your account not yet verified !');
                return redirect('login');
            }

        }
    }
}
