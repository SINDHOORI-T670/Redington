<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Models\Setting;
use Redirect;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\ValueJournal;
use App\Models\ValueStory;
use Carbon\Carbon;
use App\Models\Journal;
use App\Models\SalesConnect;
use App\Models\Events;
use App\Models\MainService;
use App\Models\SubService;
use App\Models\BusinessSolution;
use App\Models\Service;
use App\Models\Technology;
use App\Models\Product;
class EmployeeController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
        // $this->middleware('CheckUser');
        $this->middleware(function ($request, $next) {
            $settings = Setting::select('key', 'value')->get();
            $company = $settings->mapWithKeys(function ($item) {
                    return [$item['key'] => $item['value']];
            });
            view()->share(['company' => $company]);
            return $next($request);
        });
    }

    public function index(){
        return view('employee.home');
    }

    public function editprofile(){
        // dd("test");
        $customer = User::find(Auth::User()->id);
        return view('employee.profile',compact('customer'));
    }
    
    public function updateprofile(Request $request){
        // dd($request->all());
        $user = User::find(Auth::User()->id);
        $fileName = "";
            if ($request->file('image') != "") {
                $userFile = User::find(Auth::User()->id);
                if ($userFile->image != "") {
                    unlink($userFile->image);
                }
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('uploads/profiles/', $fileName);
                $fileName = 'uploads/profiles/'.$fileName;
            }else{
                $userFile = User::find(Auth::User()->id);
                $fileName=$userFile->image;
            }
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'image' => $fileName,
            'password' => isset($request->password)?Hash::make($request->password):$user->password,
        ];
        // dd($data);
        User::where('id',Auth::User()->id)->update($data);
        // dd($customer);
        return redirect()->back()->with('message', 'Profile Updated Successfully');
        
    }
    public function listsalesconnects(){
        $list = SalesConnect::where('poc_user_id',Auth::User()->id)->latest()->paginate(20);
        $techs = Technology::latest()->get();
        $products = Product::latest()->get();
        return view('employee.salesconnect.index',compact('list','techs','products','modalid'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('login');
    }
}
