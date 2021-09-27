<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
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
            // dd("test");
            return redirect(route('Admin_Dashboard'));
            // return redirect('admin/home');
        }else{
            Session::flash('message','Incorrect e-mail and password!');
            return redirect('login');
        }
            // dd("test");
        if(Auth::user()->isCustomer() && Auth::User()->verify_status == 1){
            return redirect(route('Customer_Dashboard'));
        }else{
            Session::flash('message','Your account not yet verified !');
            return redirect('login');
        }

        if(Auth::user()->isPartner() && Auth::User()->verify_status == 1){
            dd("im partner");
            // return redirect('customer/home');
            return redirect(route('Partner_Dashboard'));
        }else{
            Session::flash('message','Your account not yet verified !');
            return redirect('login');
        }

        if(Auth::user()->isEmployee() && Auth::User()->verify_status == 1){
            dd("im employee");
            // return redirect('customer/home');
            return redirect(route('Employee_Dashboard'));
        }else{
            Session::flash('message','Your account not yet verified !');
            return redirect('login');
        }
    //     if ( Auth::user()->isCutomer() ) {
    //         return redirect('customer/home');
    //    }

    //    // allow admin to proceed with request
    //    else if ( Auth::user()->isAdmin() ) {
    //        dd("ooo");
    //       return redirect('admin/home');
    //    }
        // if(Auth::User() && Auth::User()->type == 4 ){
        //     if(Auth::User()->verify_status == 1){
        //         dd("Hello Employee");
        //     }else{
        //         Session::flash('message','Your account not yet verified !');
        //         return redirect('login');
        //     }
            
        // }
        // else if(Auth::User()->type == 4 && Auth::User()->verify_status == 0){
        //     Session::flash('message','Wrong Password or your account is not yet verified !');
        //     return redirect('/login');
        // }
        // else{
        //     Session::flash('message','Please check your email and password!');
        //     return redirect('/login');
        // }
    }
    
}
