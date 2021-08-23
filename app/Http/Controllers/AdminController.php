<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('admin.home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('admin/login');
    }
}
