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
use App\Models\Service;
use App\Models\Technology;
use App\Models\UserSpec;
use App\Models\Reward;
use App\Models\Redeem;
use App\Models\Redeemdeduction;
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
            }
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'image' => $fileName
            // 'password' => Hash::make($request->password),
        ];
        // dd($data);
        User::where('id',Auth::User()->id)->update($data);
        // dd($admin);
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
                        
                    }
                    $temp_setting->value = $logo;

                }
                elseif($temp_setting->key == 'site_logo') {

                    if($request->file('site_logo') == null) {
                        $logo = $temp_setting->value;
                    } else {
                        $logo = $temp_setting->value;
                        
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
        $services= Service::all();
        $technologies= Technology::all();
        return view('admin.user.create',compact('type','services','technologies'));
    }

    public function createValidation($request)
    {
            if($request->type==4){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'email' => 'required|email|max:255|regex:/(.*)@redington\.com/i|unique:users'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email',
                    'email.regex' => 'Domain not valid for registration(example@redington.com).'
                ]);
            }elseif($request->type==3){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'email' => 'required|email|max:255|unique:users'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email'
                ]);
            }else{
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'email' => 'required|email|max:255|unique:users'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email address'
                ]);
            }
        return $validator;
    }

    public function saveUser(Request $request){
        $validator = $this->createValidation($request);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } else  {
            // dd($request->all());
            DB::beginTransaction();
            $fileName = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('uploads/profiles/', $fileName);
                $fileName = 'uploads/profiles/' . $fileName;
            }
            $userId = User::insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'type'=>$request->type,
                'status'=>1,
                'verify_status'=>1,
                'image'=>$fileName,
                'password' => Hash::make(123456),
                'company'=> $request->company,
                'post'=> $request->post,
                'linkedin' => $request->linkedin,
            ]);
            $check=UserSpec::where('user_id',$userId)->get();
            $feature = UserSpec::insertGetId([
                'user_id' => $userId,
                'service_id' => implode(",",$request->services),
                'technology_id' => implode(",",$request->technologies),
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
        $services= Service::all();
        $technologies= Technology::all();
        $userspecs=UserSpec::where('user_id',$request->id)->first();
        return view('admin.user.edit',compact('user','services','technologies','userspecs'));
    }

    public function updateUser(Request $request){
        $domain = explode("@", $request->email);
        $domain = $domain[(count($domain)-1)];
        $blacklist = array('gmail.com', 'yahoo.com', 'outlook.com');
        if (in_array($domain, $blacklist)) {
            $messages = ['email'=> 'Domain not valid for E-mail,use only business email'];
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $fileName = "";
            if ($request->file('image') != "") {
                $userFile = User::find($request->id);
                if ($userFile->image != "") {
                    unlink($userFile->image);
                }
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('uploads/profiles/', $fileName);
                $fileName = 'uploads/profiles/' . $fileName;
            }
        
            $inputData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'image' => $fileName,
                'company'=> $request->company,
                'post'=> $request->post,
                'linkedin' => $request->url,
            ];
            User::where('id',$request->id)->update($inputData);
            $check = UserSpec::where('user_id',$request->id)->count();
            if($check==0){
                $feature = UserSpec::insertGetId([
                    'user_id' => $request->id,
                    'service_id' => isset($request->services)?implode(",",$request->services):null,
                    'technology_id' => isset($request->technologies)?implode(",",$request->technologies):null,
                ]);
            }else{
                $feature =[
                    'service_id' => isset($request->services)?implode(",",$request->services):"",
                    'technology_id' => isset($request->technologies)?implode(",",$request->technologies):"",
                ];
                UserSpec::where('user_id',$request->id)->update($feature);
            }
            
            return redirect()->back()->with('success', $request->name.' updated successfully');
       
    }

    public function activeUser($id){
        // User::where('id',$id)->delete();
        $status="";
        $user=User::find($id);
        if($user->status==0){
            $status=1;
        }else{
            $status=0;
        }
        User::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','User deleted successfully');
    }
    
    public function ListRedingtonFeatures($type){
        if($type=='services'){
            $services = Service::all();
            // dd($services);
            return view('admin.service.list',compact('services'));
        }elseif($type=='technologies'){
            $technologys = Technology::all();
            return view('admin.technology.list',compact('technologys'));
        }else{
            dd("$type");
        }
    }

    public function AddRedingtonFeatures(Request $request,$type){
        // dd($type);
        if($type=='technology'){
            $validator = Validator::make($request->all(),
            [
                'techname' => 'required',
                'techdescription' => 'required',
            ],[
                'techname.required' => 'Please enter technology name',
                'techdescription.required' => 'Please add description about the technology',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::back()->withErrors($messages)->withInput();
            } else  {
                DB::beginTransaction();
                
                $techId = Technology::insertGetId([
                    'name' => $request->techname,
                    'description' => $request->techdescription,
                ]);
                DB::commit();
                return redirect()->back()->with('success','Technology created successfully');
            }

        }elseif($type=='service'){
            $validator = Validator::make($request->all(),
            [
                'servicename' => 'required',
                'servicedescription' => 'required',
            ],[
                'servicename.required' => 'Please enter service name',
                'servicedescription.required' => 'Please add description about the service',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::back()->withErrors($messages)->withInput();
            } else  {
                DB::beginTransaction();
                
                $serviceId = Service::insertGetId([
                    'name' => $request->servicename,
                    'description' => $request->servicedescription,
                ]);
                DB::commit();
                return redirect()->back()->with('success','Service created successfully');
            }
        }else{
            dd($type);
        }
        
    }

    public function editRedingtonTechnology(Request $request,$id){
        $validator = Validator::make($request->all(),
                [
                    'editname' => 'required',
                    'editdescription' => 'required',
                ],[
                    'editname.required' => 'Please enter technology name',
                    'editdescription.required' => 'Please add description about the technology',
                ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $inputData = [
            'name' => $request->editname,
            'description' => $request->editdescription
        ];
        Technology::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', $request->editname.' updated successfully');
    }

    public function editRedingtonService(Request $request,$id){
        $validator = Validator::make($request->all(),
                [
                    'editname' => 'required',
                    'editdescription' => 'required',
                ],[
                    'editname.required' => 'Please enter service name',
                    'editdescription.required' => 'Please add description about the service',
                ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $inputData = [
            'name' => $request->editname,
            'description' => $request->editdescription
        ];
        Service::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', $request->editname.' updated successfully');
    }

    public function ListRewards(){
        $rewards = Reward::all();
        return view('admin.rewards.list',compact('rewards'));

    }

    public function createReward(){
        $users = User::where('type',3)->get();
        return view('admin.rewards.create',compact('users'));
    }

    public function Savereward(Request $request){
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'partner' => 'required',
                'point' => 'required'
            ],[
                'title.required' => 'Please enter title',
                'partner.required' => 'Please select the partner',
                'point.required' => 'Please enter points'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        $rewardId = Reward::insertGetId([
            'heading' => $request->title,
            'partner_id' => $request->partner,
            'point' => $request->point
        ]);

        $update = Redeemdeduction::where('partner_id',$request->partner)->first();
        if(isset($update)){
            // dd("hghg");
            $inputData = [
                'total_reward' => ($update->total_reward + $request->point)
            ];
            // dd($inputData);
            Redeemdeduction::where('partner_id',$request->partner)->update($inputData);
        }else{
            // dd("hbnb");
            $totalrewards = Reward::where('partner_id',$request->partner)->selectRaw('sum(rewards.point) as score')->get();
            $totalredeems = Redeem::where('partner_id',$request->partner)->selectRaw('sum(redeems.amount) as score')->get();
            $score1 = isset($totalrewards[0]->score)?$totalrewards[0]->score:0;
            $score2 = isset($totalredeems[0]->score)?$totalredeems[0]->score:0;
            $redeemId = Redeemdeduction::insertGetId([
                'total_reward' => ($score1 - $request->amount),
                'partner_id' => $request->partner,
                'total_redeem' => $score2 + $request->amount
            ]);
        }
        DB::commit();
        return redirect('admin/list/rewards')->with('success', 'Success');

    }

    public function RedeemHistory($id){
        $user = User::find($id);
        $total = Redeemdeduction::where('partner_id',$id)->first();
        // dd($totalrewards[0]->score);
        $redeems = Redeem::where('partner_id',$id)->get();
        return view('admin.rewards.redeemhistory',compact('redeems','user','total'));
    }

    public function Createredeem($id){
        $user = User::find($id);
        return view('admin.rewards.redeem',compact('user'));
    }

    public function SaveRedeem(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'amount' => 'required'
            ],[
                'amount.required' => 'Please enter redeem amount'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        $rewards = Reward::where('partner_id',$request->id)->count();
        $check = Redeemdeduction::where('partner_id',$request->id)->first();
        if($request->amount>$check->total_reward){
            $messages = ['greater'=> 'There is not much points to redeem from the rewards'];
            return Redirect::back()->withErrors($messages)->withInput();
        }
        if($rewards!=0){
            $redeemId = Redeem::insertGetId([
                'amount' => $request->amount,
                'partner_id' => $request->id,
                'description' => isset($request->description)?$request->description:""
            ]);
            
            $deduction = Redeemdeduction::where('partner_id',$request->id)->first();
            if(isset($deduction)){
                $inputData = [
                    'total_reward' => $deduction->total_reward - $request->amount,
                    'total_redeem' => $deduction->total_redeem + $request->amount
                ];
                // dd($inputData);
                Redeemdeduction::where('id',$deduction->id)->update($inputData);
            }else{
                $totalrewards = Reward::where('partner_id',$request->id)->selectRaw('sum(rewards.point) as score')->get();
                $totalredeems = Redeem::where('partner_id',$request->id)->selectRaw('sum(redeems.amount) as score')->get();
                $score1 = isset($totalrewards[0]->score)?$totalrewards[0]->score:0;
                $score2 = isset($totalredeems[0]->score)?$totalredeems[0]->score:0;
                $redeemId = Redeemdeduction::insertGetId([
                    'total_reward' => ($score1 - $request->amount),
                    'partner_id' => $request->id,
                    'total_redeem' => $score2 + $request->amount
                ]);
            }
            
            DB::commit();
            return redirect('admin/redeem/history/'.$request->id)->with('success', 'Success');
    
        }else{
            $messages = ['check'=> 'There is no rewards for redeem,Please try again'];
            return Redirect::back()->withErrors($messages)->withInput();
        }
        
    }
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('admin/login');
    }
}
