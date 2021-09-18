<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function adminLogin(){
        return view('Auth.adminlogin');
    }
    public function thanks(){
        return view('thanks');
    }
}
