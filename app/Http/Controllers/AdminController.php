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
use Illuminate\Support\Facades\Storage;
class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
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

    public function editprofile(){
        $admin = User::where('type',1)->first();
        return view('admin.profile',compact('admin'));
    }
    
    public function updateprofile(Request $request){
        // dd($request->all());
        $admin = User::find(Auth::User()->id);
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            // 'post' => $request->post,
            // 'password' => Hash::make($request->password),
        ];
        $admin->update($data);
        return redirect()->back()->with('message', 'Admin Updated');
        
    }

    public function editCompanyDetails(){
        $setting = Setting::select('key', 'value')->get();
        $settings = $setting->mapWithKeys(function ($item) {
                return [$item['key'] => $item['value']];
        });
        return view('admin.settings',compact('settings'));
    }

    public function updateCompanyDetails(Request $request){
        $settings = $request->all();
        unset($settings['_token']);
        foreach ($settings as $key => $setting) {
            $temp_setting = Setting::where('key', 'like', '%' . $key . '%')->first();
            // $temp_setting = Setting::where('key', $key)->first();
            if($temp_setting){
                if($temp_setting->key == 'site_favicon') {
                    if($request->file('site_favicon') == null) {
                        $logo = $temp_setting->value;
                    } else {
                        $logo = $temp_setting->value;
                        // if($temp_setting->value) {
                        //     remove_image($temp_setting->value);
                        // }
                        // $logo = upload_image($request->file('site_favicon'));
                    }
                    $temp_setting->value = $logo;

                }
                elseif($temp_setting->key == 'site_logo') {

                    if($request->file('site_logo') == null) {
                        $logo = $temp_setting->value;
                    } else {
                        $logo = $temp_setting->value;
                        // if($temp_setting->value) {
                        //     remove_image($temp_setting->value);
                        // }
                        // $logo = upload_image($request->file('site_logo'));
                    }
                    $temp_setting->value = $logo;
                }
                else {
                    $temp_setting->value = $request->$key;               
                } 
                $temp_setting->save();
            }else{
                Setting::set($key,$setting);
                Setting::save();
            }  
                
        }   
        return redirect()->back()->with('message', 'Admin Updated');
    }

    public function listUser(Request $request){
        $type = $request->type;
        $users = User::where('type',$request->type)->get();
        return view('admin.user.list',compact('users','type'));
    }

    public function createUser(Request $request){
        $type = $request->type;
        return view('admin.user.create',compact('type'));
    }

    public function createValidation($request)
    {
            if($request->type==4){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'post' => 'required',
                    'email' => 'required|email|max:255|regex:/(.*)@redington\.com/i|unique:users'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'post.required' => 'Please enter job position',
                    'email.regex' => 'Domain not valid for registration(example@redington.com).'
                ]);
            }elseif($request->type==3){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'post' => 'required',
                    'email' => 'required|email|max:255|regex:/(.*)@myemail\.com/i|unique:users'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'post.required' => 'Please enter job position',
                    'email.regex' => 'Domain not valid for registration(Business email only,example@myemail.com).'
                ]);
            }else{
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'post' => 'required',
                    'email' => 'required|email|max:255|unique:users'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'post.required' => 'Please enter job position',
                    'email.required' => 'Please enter email address'
                ]);
            }
        return $validator;
    }

    public function saveUser(Request $request){
        $validator = $this->createValidation($request);
        if ($validator->fails()) {
            $messages = $validator->messages();
            // dd($messages);
            return Redirect::back()->withErrors($messages)->withInput();
        } else  {
            DB::beginTransaction();
            $userId = User::insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'type'=>$request->type,
                'status'=>1,
                'verify_status'=>1,
                'password' => Hash::make(123456),
            ]);
            DB::commit();
            $usertype = (
                ($request->type == 2) ? "Customer" :
                (($request->type == 3) ? "Partner" :
                (($request->type == 4) ? "Employee" : "User"))
                );
            return redirect()->back()->with('success', $usertype.' created successfully');
        }
    }

    public function editUser(Request $request){
        $user = User::find($request->id);
        return view('admin.user.edit',compact('user'));
    }

    public function updateUser(Request $request){
        $validator = $this->createValidation($request);
        if ($validator->fails()) {
            $messages = $validator->messages();
            // dd($messages);
            return Redirect::back()->withErrors($messages)->withInput();
        } else  {
            $inputData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'post' => $request->post
            ];
            User::where('id',$request->id)->update($inputData);
            return redirect()->back()->with('success', $request->name.' updated successfully');
        }
    }











    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('admin/login');
    }
}
