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
use App\Models\PartnerReward;
use App\Models\Resource;
use App\Models\SubResource;
use App\Models\ValueJournal;
use App\Models\ValueStory;
use Carbon\Carbon;
use App\Models\Journal;
use File;
use Response;
use App\Models\Brand;
use App\Models\Region;
use App\Models\Poc;
use App\Models\RegionConnection;
use Illuminate\Support\Str;
use App\Models\SalesConnect;
use App\Models\Reschedule;
use App\Models\PresetQuestion;
use App\Models\QueryRequest;
use App\Models\ReplyRequest;
use App\Models\Product;
class AdminController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
        // $this->middleware('CheckAdmin');
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
            }else{
                $userFile = User::find(Auth::User()->id);
                $fileName=$userFile->image;
            }
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'image' => $fileName,
            'password' => Hash::make($request->password),
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
        $users = User::where('type',$request->type)->latest()->paginate(20);
        // $users = User::where('type',$request->type)->get();
        // dd($users);
        $rewards = Reward::where('status',0)->get();
        $regions = Region::where('status',0)->get();
        return view('admin.user.list',compact('users','type','rewards','regions'));
    }

    public function createUser(Request $request){
        $type = $request->type;
        $services= Service::latest()->get();
        $technologies= Technology::latest()->get();
        $pocs = POC::where('status',0)->latest()->get();
        return view('admin.user.create',compact('type','services','technologies','pocs'));
    }

    public function createValidation($request)
    {
            if($request->type==4){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required|unique:users,phone',
                    'email' => 'required|email|max:255|regex:/(.*)@redington\.com/i',
                    'poc' => 'required'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email',
                    'email.regex' => 'Domain not valid for registration(example@redington.com).',
                    'poc.required' => 'Please select type'
                ]);
            }elseif($request->type==3){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required|unique:users,phone',
                    'email' => 'required|email|max:255'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email'
                ]);
            }else{
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required|unique:users,phone',
                    'email' => 'required|email|max:255'
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
                'status'=>0,
                'verify_status'=>1,
                'image'=>$fileName,
                'password' => Hash::make(123456),
                'company'=> $request->company,
                'post'=> $request->post,
                'linkedin' => $request->linkedin,
                'poc_id'=>$request->poc,
                'api_token'=>Str::random(60),
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

            ]);
            $check=UserSpec::where('user_id',$userId)->get();
            $feature = UserSpec::insertGetId([
                'user_id' => $userId,
                'service_id' => isset($request->services)?implode(",",$request->services):null,
                'technology_id' => isset($request->technologies)?implode(",",$request->technologies):null,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

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
        $pocs = POC::where('status',0)->latest()->get();
        return view('admin.user.edit',compact('user','services','technologies','userspecs','pocs'));
    }

    public function updateUser(Request $request){
        $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required',
                    'email' => 'required|email|max:255|regex:/(.*)@redington\.com/i',
                    'poc' => 'required'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email',
                    'email.regex' => 'Domain not valid for registration(example@redington.com).',
                    'poc.required' => 'Please select type'
                ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        // $domain = explode("@", $request->email);
        // $domain = $domain[(count($domain)-1)];
        // $blacklist = array('gmail.com', 'yahoo.com', 'outlook.com');
        // if (in_array($domain, $blacklist)) {
        //     $messages = ['email'=> 'Domain not valid for E-mail,use only business email'];
        //     return Redirect::back()->withErrors($messages)->withInput();
        // }
        // $email = '';
        $phone = '';
        $phone = $request->phone;
        $phNoExist = User::where('id','!=',$request->id)->where('phone',$phone)->count();
        if($phNoExist > 0){
            return redirect()->back()->with('error', 'Phone number  already exist.Please try with another phone!')->withInput();
        }
        // $email = $request->email;
        // $emailExist = User::where('email',$email)->count();
        // if($emailExist > 0){
        //     return redirect()->back()->with('error', 'E-mail already exist.Please try with another E-mail!')->withInput();
        // }
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
                'poc_id'=>$request->poc
            ];
            User::where('id',$request->id)->update($inputData);
            // if($request->poc_id!=2){
            //     $regionData = RegionConnection::where('user_id',$request->id)->delete();
            // }
            $check = UserSpec::where('user_id',$request->id)->count();
            if($check==0){
                $feature = UserSpec::insertGetId([
                    'user_id' => $request->id,
                    'service_id' => isset($request->services)?implode(",",$request->services):null,
                    'technology_id' => isset($request->technologies)?implode(",",$request->technologies):null,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

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
        return redirect()->back()->with('success','User status updated successfully');
    }
    
    public function AssignRegion(Request $request){
        $validator = Validator::make($request->all(),
            [
                'region' => 'required',
                'check' => 'required',
            ],[
                'region.required' => 'Please select any region',
                'check.required' => 'Please choose some employees',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::back()->withErrors($messages)->withInput();
            }
        $users=$request->check;
        if((isset($users)) && (isset($request->region))){
            foreach(explode(",",$users) as $user){
                $regionData = RegionConnection::where('user_id',$user)->get();
                // dd($regionData);
                if(count($regionData)>0){
                    RegionConnection::where('user_id',$user)->update(['region_id' => $request->region]);
                   
                }else{
                    $RegConID = RegionConnection::insertGetId([
                        'user_id' => $user,
                        'region_id' => $request->region,
                        "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        
                    ]);
                    DB::commit();
                
                }
            }
            return redirect()->back()->with('success', 'Region assigned successfully');
        }
        else{
            return redirect()->back()->with('error', 'Something went wrong,please try again !!!');
        }
    }
    public function ListRedingtonFeatures($type){
        if($type=='services'){
            $services = Service::latest()->paginate(20);
            // dd($services);
            return view('admin.service.list',compact('services'));
        }elseif($type=='technologies'){
            $technologys = Technology::latest()->paginate(20);
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
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
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
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

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
 
    public function activeService($id){
        $status="";
        $service=Service::find($id);
        if($service->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Service::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Service status updated successfully');

    }

    public function activeTechnology($id){
        $status="";
        $technology=Technology::find($id);
        if($technology->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Technology::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Technology status updated successfully');

    }
    public function ListRewards(){
        $rewards = Reward::latest()->paginate(20);
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
                // 'partner' => 'required',
                'point' => 'required'
            ],[
                'title.required' => 'Please enter title',
                // 'partner.required' => 'Please select the partner',
                'point.required' => 'Please enter points'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        $rewardId = Reward::insertGetId([
            'heading' => $request->title,
            // 'partner_id' => $request->partner,
            'point' => $request->point,
            'status' => 0,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);

        // $update = Redeemdeduction::where('partner_id',$request->partner)->first();
        // if(isset($update)){
        //     // dd("hghg");
        //     $inputData = [
        //         'total_reward' => ($update->total_reward + $request->point)
        //     ];
        //     // dd($inputData);
        //     Redeemdeduction::where('partner_id',$request->partner)->update($inputData);
        // }else{
        //     // dd("hbnb");
        //     $totalrewards = PartnerReward::where('partner_id',$request->partner)->selectRaw('sum(partner_rewards.amount) as score')->get();
        //     $totalredeems = Redeem::where('partner_id',$request->partner)->selectRaw('sum(redeems.amount) as score')->get();
        //     $score1 = isset($totalrewards[0]->score)?$totalrewards[0]->score:0;
        //     $score2 = isset($totalredeems[0]->score)?$totalredeems[0]->score:0;
        //     $redeemId = Redeemdeduction::insertGetId([
        //         'total_reward' => ($score1 - $request->amount),
        //         'partner_id' => $request->partner,
        //         'total_redeem' => $score2 + $request->amount,
        //         "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
        //     "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        //     ]);
        // }
        DB::commit();
        return redirect('admin/list/rewards')->with('success', 'Success');

    }
    public function activeReward($id){
        $status="";
        $reward=Reward::find($id);
        if($reward->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Reward::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Reward status updated successfully');
    }

    public function RedeemHistory($id){
        $user = User::find($id);
        $total = Redeemdeduction::where('partner_id',$id)->first();
        // dd($totalrewards[0]->score);
        $redeems = Redeem::where('partner_id',$id)->latest()->paginate(20);
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
        $rewards = PartnerReward::where('partner_id',$request->id)->count();
        $check = Redeemdeduction::where('partner_id',$request->id)->first();
        if($request->amount>$check->total_reward){
            $messages = ['greater'=> 'There is not much points to redeem from the rewards'];
            return Redirect::back()->withErrors($messages)->withInput();
        }
        if($rewards!=0){
            $redeemId = Redeem::insertGetId([
                'amount' => $request->amount,
                'partner_id' => $request->id,
                'description' => isset($request->description)?$request->description:"",
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
    
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

    public function RewardHistory($id){
        $user = User::find($id);
        $total = Redeemdeduction::where('partner_id',$id)->first();
        // dd($totalrewards[0]->score);
        $rewards = PartnerReward::where('partner_id',$id)->latest()->paginate(20);
        return view('admin.rewards.rewardhistory',compact('rewards','user','total'));
    }

    // public function CreateReward($id){
    //     $user = User::find($id);
    //     return view('admin.rewards.reward',compact('user'));
    // }

    public function SaveRewardforPartner(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'partner' => 'required',
                'reward' => 'required'
            ],[
                'partner.required' => 'Please select partner',
                'reward.required' => 'Please select reward'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        // $rewards = PartnerReward::where('partner_id',$request->id)->count();
        // $check = Redeemdeduction::where('partner_id',$request->id)->first();
        // if($request->amount>$check->total_reward){
        //     $messages = ['greater'=> 'There is not much points to redeem from the rewards'];
        //     return Redirect::back()->withErrors($messages)->withInput();
        // }
        // if($rewards!=0){
            $reward = Reward::find($request->reward);
            foreach($request->partner as $partner){
                $rewardId = PartnerReward::insertGetId([
                    'amount' => $reward->point,
                    'reward_id'=>$request->reward,
                    'partner_id' => $partner,
                    // 'description' => isset($request->description)?$request->description:"",
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        
                ]);
                
                $deduction = Redeemdeduction::where('partner_id',$partner)->first();
                // dd($deduction);
                if(isset($deduction)){
                    $inputData = [
                        'total_reward' => ($deduction->total_reward+$reward->point),
                        // 'total_redeem' => $deduction->total_redeem + $reward->point
                    ];
                    Redeemdeduction::where('id',$deduction->id)->update($inputData);
                }else{
                    
                    $totalrewards = PartnerReward::where('partner_id',$partner)->selectRaw('sum(partner_rewards.amount) as score')->get();
                    $totalredeems = Redeem::where('partner_id',$partner)->selectRaw('sum(redeems.amount) as score')->get();
                    $score1 = isset($totalrewards[0]->score)?$totalrewards[0]->score:0;
                    $score2 = isset($totalredeems[0]->score)?$totalredeems[0]->score:0;
                    // dd($score1,$score2);
                    $redeemId = Redeemdeduction::insertGetId([
                        'total_reward' => ($score1 - $request->amount),
                        'partner_id' => $partner,
                        'total_redeem' => $score2 + $request->amount
                    ]);
                }
                
                DB::commit();
            }
            return redirect()->back()->with('success', 'Reward applied successfully');
            // return redirect('admin/reward/history/'.$request->id)->with('success', 'Success');
    
        // }else{
        //     $messages = ['check'=> 'There is no rewards for redeem,Please try again'];
        //     return Redirect::back()->withErrors($messages)->withInput();
        // }
        
    }
    public function getRewardPoint(Request $request){
        $reward = Reward::find($request->reward_id);
        return $reward->point;
    }

    public function resources(){
        $list = Resource::latest()->paginate(20);
        return view('admin.resource.list',compact('list'));
    }

    public function addResource(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'resourcename' => 'required',
                'type' => 'required'
            ],[
                'resourcename.required' => 'Please enter resource name',
                'type.required' => 'Please select user type '
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        $resourceId = Resource::insertGetId([
            'name' => $request->resourcename,
            'type'=>implode(',',$request->type),
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Resource added successfully');
    }

    public function editResource(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'editname' => 'required',
                'type' => 'required'
            ],[
                'editname.required' => 'Please enter resource name',
                'type.required' => 'Please select user type '
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 

        $inputData = [
            'name' => $request->editname,
            'type' => implode(',',$request->type)
        ];
        Resource::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Resource added successfully');
    }

    public function activeResource($id){
        $status="";
        $resource=Resource::find($id);
        if($resource->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Resource::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Resource status updated successfully');
    }

    public function subresources($id){
        $list = SubResource::where('resource_id',$id)->latest()->paginate(20);
        $resource = $id;
        $icons = [
            'pdf' => 'pdf',
            'doc' => 'word',
            'docx' => 'word',
            'xls' => 'excel',
            'xlsx' => 'excel',
            'ppt' => 'powerpoint',
            'pptx' => 'powerpoint',
            'txt' => 'text',
            'png' => 'image',
            'jpg' => 'image',
            'jpeg' => 'image',
        ];
        return view('admin.resource.sublist',compact('list','resource','icons'));
    }

    public function addsubResource(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'name' => 'required'
            ],[
                'name.required' => 'Please enter sub resource name'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        // $fileName = [];
        if ($request->file('file') != "") {
            foreach ($request->file('file') as $file) {
                // $file = $request->file('file');
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                // $file1 = $file->getClientOriginalName(); //Get Image Name

                // $extension = $file->getClientOriginalExtension();  //Get Image Extension
                
                // $name = $file1.'.'.$extension;  //Concatenate both to get FileName (eg: file.jpg)
                $file->move('uploads/subresource/', $name);
                $fileName[] = $name;
            }
        }
        $resourceId = SubResource::insertGetId([
            'resource_id' => $request->resource_id,
            'heading' => $request->name,
            'details'=>$request->detail1,
            'file' => isset($fileName)?implode(',',$fileName):null,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Resource added successfully');
    }

    public function editsubResource(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required'
            ],[
                'name.required' => 'Please enter sub resource name'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        // $fileName = [];
        if ($request->file('file') != "") {
            foreach ($request->file('file') as $file) {
                // $file = $request->file('file');
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                // $file1 = $file->getClientOriginalName(); //Get Image Name

                // $extension = $file->getClientOriginalExtension();  //Get Image Extension
                
                // $name = $file1.'.'.$extension;  //Concatenate both to get FileName (eg: file.jpg)
                $file->move('uploads/subresource/', $name);
                $fileName[] = $name;
            }
        }
        
        $sub= SubResource::find($id);
        if(isset($sub->file)){
            $array = implode(',',(array) $fileName).','.$sub->file;
        }else{
            $array = implode(',',(array) $fileName);
        }
        // dd($array);
        $inputData = [
            'heading' => $request->name,
            'details' => $request->detail2,
            'file' => $array,
        ];
        SubResource::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'SubResource updated successfully');
    }
    public function downloadfile($file)
    {
        $filepath = public_path('uploads/subresource/'.$file.'');
        return Response::download($filepath); 
    }
    public function activesubResource($id){
        $status="";
        $resource=SubResource::find($id);
        if($resource->status==0){
            $status=1;
        }else{
            $status=0;
        }
        SubResource::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','SubResource status updated successfully');
    }

    public function journals(){
        $list = Journal::latest()->paginate(20);
        return view('admin.journals.journals',compact('list'));
    }

    public function addJournal(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                // 'description' => 'required',
                // 'file' => 'required',
                // 'detail2' => 'required',
                // 'date' => 'required'
            ],[
                'name.required' => 'Please enter value journal title',
                // 'image.required'=> 'Please add an image for value journals',
                // 'detail1.required' => 'Please enter short description',
                // 'detail2.required' => 'Please add details about value journals',
                // 'date.required' => 'Please add date'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $fileName = "";
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/journals/', $fileName);
            $fileName = 'uploads/journals/' . $fileName;
        }
        $journalId = Journal::insertGetId([
            'journal' => $request->name,
            'image' => $fileName,
            'description' => $request->detail1,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Value Journal added successfully');

    }

    public function editJournal(Request $request,$id){
            $validator = Validator::make($request->all(),
            [
                'editname' => 'required',
                // 'description' => 'required',
                // 'file' => 'required',
                // 'detail2' => 'required',
                // 'date' => 'required'
            ],[
                'editname.required' => 'Please enter value journal title',
                // 'image.required'=> 'Please add an image for value journals',
                // 'detail1.required' => 'Please enter short description',
                // 'detail2.required' => 'Please add details about value journals',
                // 'date.required' => 'Please add date'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $fileName = "";
        if ($request->file('image') != "") {
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/journals/', $fileName);
            $fileName = 'uploads/journals/' . $fileName;
        }else{
            $journal = Journal::find($id);
            $fileName = $journal->image;

        }
        $inputData=[
            'journal' => $request->editname,
            'image' => $fileName,
            'description' => $request->detail2,
        ];
        Journal::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Value Journal Updated successfully');

    }
    
    public function activemainjournals($id){
        $status="";
        $journal=Journal::find($id);
        if($journal->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Journal::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Value journal status updated successfully');
    }
    public function ValueJournalList($id){
        $journal = Journal::find($id);
        $list = ValueJournal::where('journal_id',$id)->latest()->paginate(20);
        return view('admin.journals.list',compact('list','journal'));
    }

    public function storevalueJournal(Request $request){
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'image' => 'required',
                'detail1' => 'required',
                'detail2' => 'required',
                'date' => 'required'
            ],[
                'title.required' => 'Please enter value journal title',
                'image.required'=> 'Please add an image for value journals',
                'detail1.required' => 'Please enter short description',
                'detail2.required' => 'Please add details about value journals',
                'date.required' => 'Please add date'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $fileName = "";
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/journals/', $fileName);
            $fileName = 'uploads/journals/' . $fileName;
        }
        $journalId = ValueJournal::insertGetId([
            'title' => $request->title,
            'image' => $fileName,
            'short' => $request->detail1,
            'detail' => $request->detail2,
            'journal_date' => Carbon::parse($request->date),
            'journal_id' => $request->j_id,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Value Journal added successfully');

    }
    public function editvaluejournals($id,Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
        [
            'title' => 'required',
            // 'image' => 'required',
            'detail3' => 'required',
            'detail4' => 'required',
            'date' => 'required'
        ],[
            'title.required' => 'Please enter value journal title',
            // 'image.required'=> 'Please add an image for value journals',
            'detail3.required' => 'Please enter short description',
            'detail4.required' => 'Please add details about value journals',
            'date.required' => 'Please add date'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }

        $fileName = "";
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/journals/', $fileName);
            $fileName = 'uploads/journals/' . $fileName;
        }else{
            $journal = ValueJournal::find($id);
            $fileName = $journal->image;

        }
        $inputData=[
            'title' => $request->title,
            'image' => $fileName,
            'short' => $request->detail3,
            'detail' => $request->detail4,
            'journal_date' => Carbon::parse($request->date)
        ];
        ValueJournal::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Value Journal Updated successfully');

    }

    public function activejournals($id){
        $status="";
        $journal=ValueJournal::find($id);
        if($journal->status==0){
            $status=1;
        }else{
            $status=0;
        }
        ValueJournal::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Value journal status updated successfully');

    }

    public function ValuestoriesList(){
        $list = ValueStory::latest()->paginate(20);
        return view('admin.stories.list',compact('list'));
    }

    public function storevaluestories(Request $request){
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'image' => 'required',
                'detail1' => 'required',
                'detail2' => 'required',
                'date' => 'required',
                'by' => 'required'
            ],[
                'title.required' => 'Please enter value story title',
                'image.required'=> 'Please add an image for value story',
                'detail1.required' => 'Please enter short description',
                'detail2.required' => 'Please add details about value story',
                'date.required' => 'Please add date',
                'by.required'=>'Please enter addedby' 
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $fileName = "";
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/stories/', $fileName);
            $fileName = 'uploads/stories/' . $fileName;
        }
        $storyId = ValueStory::insertGetId([
            'title' => $request->title,
            'image' => $fileName,
            'short' => $request->detail1,
            'detail' => $request->detail2,
            'journal_date' => Carbon::parse($request->date),
            'by'=> $request->by,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Data added successfully');

    }
    public function editvaluestories($id,Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
        [
            'title' => 'required',
            // 'image' => 'required',
            'detail3' => 'required',
            'detail4' => 'required',
            'date' => 'required',
            'by' => 'required'
        ],[
            'title.required' => 'Please enter value journal title',
            // 'image.required'=> 'Please add an image for value journals',
            'detail3.required' => 'Please enter short description',
            'detail4.required' => 'Please add details about value journals',
            'date.required' => 'Please add date',
            'by.required' => 'Please enter addedby'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }

        $fileName = "";
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/journals/', $fileName);
            $fileName = 'uploads/journals/' . $fileName;
        }else{
            $story = ValueStory::find($id);
            $fileName = $story->image;

        }
        $inputData=[
            'title' => $request->title,
            'image' => $fileName,
            'short' => $request->detail3,
            'detail' => $request->detail4,
            'journal_date' => Carbon::parse($request->date),
            'by' =>$request->by
        ];
        ValueStory::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Data Updated successfully');

    }

    public function activestories($id){
        $status="";
        $story=ValueStory::find($id);
        if($story->status==0){
            $status=1;
        }else{
            $status=0;
        }
        ValueStory::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Status updated successfully');

    }

    public function BrandList(){
        $list = Brand::latest()->paginate(20);
        return view('admin.brands.list',compact('list'));
    }

    public function addBrand(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required'
            ],[
                'name.required' => 'Please enter brand name',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $brandId = Brand::insertGetId([
            'name' => $request->name,
            'status' => $request->status,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Brand details added successfully');

    }

    public function editBrand(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'editname' => 'required'
            ],[
                'editname.required' => 'Please enter brand name',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $inputData=[
            'name' => $request->editname,
            'status' => $request->status,
        ];
        Brand::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Data Updated successfully');

    }

    public function RegionList(){
        $list = Region::latest()->paginate(20);
        return view('admin.regions.list',compact('list'));
    }

    public function addRegion(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required'
            ],[
                'name.required' => 'Please enter region name',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $regionId = Region::insertGetId([
            'name' => $request->name,
            'status' => $request->status,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'New region added successfully');

    }

    public function editRegion(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'editname' => 'required'
            ],[
                'editname.required' => 'Please enter region name',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $inputData=[
            'name' => $request->editname,
            'status' => $request->status,
        ];
        Region::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Data Updated successfully');

    }

    public function SalesConnects(){
        $list = SalesConnect::latest()->paginate(20);
        $techs = Technology::latest()->get();
        $products = Product::latest()->get();
        return view('admin.salesconnect.index',compact('list','techs','products'));
    }

    public function Reschedule($id,Request $request){
        $validator = Validator::make($request->all(),
            [
                'tech' => 'required',
                'product' => 'required',
                'date' => 'required',
                'time' => 'required'
            ],[
                'tech.required'=>'Please select technology',
                'product.required'=>'Please select product',
                'time.required' => 'Please enter time',
                'date.required' => 'Please enter date'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $item = Reschedule::find($id);
        SalesConnect::where('id',$id)->update(['tech_id'=>$request->tech,'product_id'=>$request->product,'date_time' => $request->date.' '.$request->time,'status'=>1]);
        if(isset($item)){
            Reschedule::where('salecon_id',$id)->update(['date_time'=>$request->date.' '.$request->time]);
        }else{
            
            $rescheduleId = Reschedule::insertGetId([
                'salecon_id' => $id,
                'date_time' => $request->date.' '.$request->time,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
    
            ]);
            
            DB::commit();
            
        }
        return redirect()->back()->with('success', 'Rescheduled successfully');
    }

    public function PresetQuestions($techid,$brandid){
        $list = PresetQuestion::where('tech_id',$techid)->where('brand_id',$brandid)->withCount('request')->latest()->paginate(20);
        return view('admin.salesconnect.query',compact('list'));
    }

    public function allqueries(){
        $list = PresetQuestion::latest()->paginate(20);
        $techs = Technology::where('status',0)->get();
        $brands = Brand::where('status',0)->get();
        return view('admin.salesconnect.allquery',compact('list','techs','brands'));
    }

    public function addsalesquery(Request $request){
        $validator = Validator::make($request->all(),
            [
                'tech' => 'required',
                'brand' => 'required',
                'query' => 'required'
            ],[
                'query.required' => 'Please enter question',
                'tech.required' => 'Please select technology',
                'brand.required' => 'Please select brand'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $queryId = PresetQuestion::insertGetId([
            'tech_id' => $request->tech,
            'brand_id' => $request->brand,
            'question' => $request->get('query'),
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Preset Question added successfully');
    }

    public function editsalesquery($id,Request $request){
        $validator = Validator::make($request->all(),
            [
                'tech' => 'required',
                'brand' => 'required',
                'query' => 'required'
            ],[
                'query.required' => 'Please enter question',
                'tech.required' => 'Please select technology',
                'brand.required' => 'Please select brand'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        PresetQuestion::where('id',$id)->update(['tech_id'=>$request->tech,'brand_id'=>$request->brand,'question'=>$request->get('query'),'status'=>$request->status]);
        return redirect()->back()->with('success', 'Preset Question updated successfully');
    }

    public function QueryRequest($id){
        $list = QueryRequest::where('query_id',$id)->latest()->paginate(20);
        QueryRequest::where('query_id',$id)->where('read_status',0)->update(['read_status'=>1]);
        return view('admin.salesconnect.requests',compact('list'));
    }

    public function replyquery($id,Request $request){
        $validator = Validator::make($request->all(),
            [
                'reply' => 'required'
            ],[
                'reply.required' => 'Please enter reply',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $replyId = ReplyRequest::insertGetId([
            'req_id' => $id,
            'from_id'=>Auth::User()->id,
            'reply'=>$request->reply,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Reply sended successfully');
    }

    public function ProductList(){
        $list = Product::latest()->paginate(20);
        return view('admin.products.list',compact('list'));
    }

    public function addProduct(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required'
            ],[
                'name.required' => 'Please enter brand name',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $productId = Product::insertGetId([
            'name' => $request->name,
            'status' => $request->status,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Brand details added successfully');

    }

    public function editProduct(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'editname' => 'required'
            ],[
                'editname.required' => 'Please enter brand name',
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $inputData=[
            'name' => $request->editname,
            'status' => $request->status,
        ];
        Product::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Data Updated successfully');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('admin/login');
    }
}
