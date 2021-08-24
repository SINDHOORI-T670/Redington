<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        // $setting=Setting::all();
        $settings = Setting::select('key', 'value')->get();
        $company = $settings->mapWithKeys(function ($item) {
                return [$item['key'] => $item['value']];
        });
        // dd($company);
        view()->share(['company' => $company]);
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
