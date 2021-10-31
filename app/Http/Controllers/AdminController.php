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

use App\Models\Events;
use App\Models\MainService;
use App\Models\SubService;
use App\Models\BusinessSolution;
use App\Models\SubResourceFile;
use App\Models\Description;
use App\Models\EventRegister;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Requests;
use App\Models\Promotion;
class AdminController extends Controller

{

    public function __construct(){

        // $this->middleware('auth');

        // $this->middleware('CheckAdmin');

        $this->middleware(function ($request, $next) {
            $settings = Setting::select('key', 'value')->get();
            $company = $settings->mapWithKeys(function ($item) {
                    return [$item['key'] => $item['value']];
            });
            $not_count = Notification::where('to_id',Auth::User()->id)->where('status',0)->count();
            $new_notifs = Notification::where('to_id',Auth::User()->id)->where('status',0)->latest()->get();
            view()->share(['company' => $company,'not_count' => $not_count,'new_notifs' => $new_notifs]);
            return $next($request);
        });
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

                // $fileName = 'uploads/profiles/'.$fileName;
                $fileName = $request->root().'/uploads/profiles/'.$fileName;

            }else{

                $userFile = User::find(Auth::User()->id);

                $fileName=$userFile->image;

            }

        $data = [

            'name' => $request->name,

            'phone' => $request->phone,

            'email' => $request->email,

            'image' => $fileName,

            'password' => isset($request->password)?Hash::make($request->password):$admin->password,

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
        // dd($pocs);

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
                    'poc' => 'required',
                    'password' => 'min:6|required_with:confpassword|same:confpassword',
                    'confpassword' => 'required|min:6'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email',
                    'email.regex' => 'Domain not valid for registration(example@redington.com).',
                    'poc.required' => 'Please select type',
                    'password.same' => 'The password and confirm password must match.',
                    'confpassword.required' => 'Please enter confirm passowrd',
                    'password.required_with'=>'Please enter password ',
                    'password.min' => 'The password must be atleast 6 characters',
                    'confpassword.min'=>'The Confirm password must be atleast 6 characters'
                ]);
            }elseif($request->type==3){
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required|unique:users,phone',
                    'email' => 'required|email|max:255',
                    'password' => 'min:6|required_with:confpassword|same:confpassword',
                    'confpassword' => 'required|min:6'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email',
                    'password.same' => 'The password and confirm password must match.',
                    'confpassword.required' => 'Please enter confirm passowrd',
                    'password.required_with'=>'Please enter password ',
                    'password.min' => 'The password must be atleast 6 characters',
                    'confpassword.min'=>'The Confirm password must be atleast 6 characters'
                ]);
            }else{
                $validator = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'phone' => 'required|unique:users,phone',
                    'email' => 'required|email|max:255',
                    'password' => 'min:6|required_with:confpassword|same:confpassword',
                    'confpassword' => 'required|min:6'
                ],[
                    'name.required' => 'Please enter name',
                    'phone.required' => 'Please enter phone number',
                    'email.required' => 'Please enter email address',
                    'password.same' => 'The password and confirm password must match.',
                    'confpassword.required' => 'Please enter confirm passowrd',
                    'password.required_with'=>'Please enter password ',
                    'password.min' => 'The password must be atleast 6 characters',
                    'confpassword.min'=>'The Confirm password must be atleast 6 characters'
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

                // $fileName = 'uploads/profiles/' . $fileName;
                $fileName = $request->root().'/uploads/profiles/'.$fileName;
                

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

                'password' => Hash::make($request->password),

                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()

                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
            ]);
            if($request->type!=4){
                $check=UserSpec::where('user_id',$userId)->get();

                $feature = UserSpec::insertGetId([

                    'user_id' => $userId,

                    'service_id' => isset($request->services)?implode(",",$request->services):null,

                    'technology_id' => isset($request->technologies)?implode(",",$request->technologies):null,

                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()

                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                ]); 
            }

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
        $products = Product::all();
        $userspecs=UserSpec::where('user_id',$request->id)->first();

        $pocs = POC::where('status',0)->latest()->get();
        $regionData = RegionConnection::where('user_id',$request->id)->get();
        $regions = Region::all();
        // dd($pocs);
        return view('admin.user.edit',compact('user','services','technologies','products','userspecs','pocs','regionData','regions'));

    }


    public function updateValidation($request)

    {

            if($request->type==4){

                $validator = Validator::make($request->all(),

                [

                    'name' => 'required',

                    'phone' => 'required',

                    'email' => 'required|email|max:255|regex:/(.*)@redington\.com/i',

                    'poc' => 'required',
                    // 'password' => 'min:6|required_with:confpassword|same:confpassword',
                    // 'confpassword' => 'min:6'

                ],[

                    'name.required' => 'Please enter name',

                    'phone.required' => 'Please enter phone number',

                    'email.required' => 'Please enter email',

                    'email.regex' => 'Domain not valid for registration(example@redington.com).',

                    'poc.required' => 'Please select type',
                    // 'password.same' => 'The password and confirm password must match.',
                    // 'confpassword.required' => 'Please enter confirm passowrd',
                    // 'password.required_with'=>'Please enter password ',
                    // 'password.min' => 'The password must be atleast 6 characters',
                    // 'confpassword.min'=>'The Confirm password must be atleast 6 characters'

                ]);

            }elseif($request->type==3){

                $validator = Validator::make($request->all(),

                [

                    'name' => 'required',

                    'phone' => 'required',

                    'email' => 'required|email|max:255',
                    // 'password' => 'min:6|required_with:confpassword|same:confpassword',
                    // 'confpassword' => 'required|min:6'

                ],[

                    'name.required' => 'Please enter name',

                    'phone.required' => 'Please enter phone number',

                    'email.required' => 'Please enter email',
                    // 'password.same' => 'The password and confirm password must match.',
                    // 'confpassword.required' => 'Please enter confirm passowrd',
                    // 'password.required_with'=>'Please enter password ',
                    // 'password.min' => 'The password must be atleast 6 characters',
                    // 'confpassword.min'=>'The Confirm password must be atleast 6 characters'

                ]);

            }else{

                $validator = Validator::make($request->all(),

                [

                    'name' => 'required',

                    'phone' => 'required',

                    'email' => 'required|email|max:255',
                    // 'password' => 'min:6|required_with:confpassword|same:confpassword',
                    // 'confpassword' => 'min:6'

                ],[

                    'name.required' => 'Please enter name',

                    'phone.required' => 'Please enter phone number',

                    'email.required' => 'Please enter email address',
                    // 'password.same' => 'The password and confirm password must match.',
                    // 'confpassword.required' => 'Please enter confirm passowrd',
                    // 'password.required_with'=>'Please enter password ',
                    // 'password.min' => 'The password must be atleast 6 characters',
                    // 'confpassword.min'=>'The Confirm password must be atleast 6 characters'

                ]);

            }

        return $validator;

    }

    public function updateUser(Request $request){
        // dd($request->all());
        $validator = $this->updateValidation($request);

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
        // dd($phNoExist);

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

                // $fileName = 'uploads/profiles/' . $fileName;
                $fileName = $request->root().'/uploads/profiles/'.$fileName;

            }

        

            $inputData = [

                'name' => $request->name,

                'phone' => $request->phone,

                'email' => $request->email,

                'image' => $fileName,

                'company'=> $request->company,

                'post'=> $request->post,

                'linkedin' => $request->url,

                'poc_id'=>$request->poc,

                'password' => Hash::make($request->password),

            ];

            User::where('id',$request->id)->update($inputData);

            if($request->region){
                foreach($request->region as $key => $item){
                    $ifexist = RegionConnection::where('user_id',$request->id)->where('region_id',$item)->count();
                    if($ifexist==0){
                        $RegConID = RegionConnection::insertGetId([

                            'user_id' => $request->id,
    
                            'region_id' => $item,
    
                            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
    
                            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                        ]);
                    }
                }
            }
            // if($request->poc_id!=2){

            //     $regionData = RegionConnection::where('user_id',$request->id)->delete();

            // }
            if($request->type!=4){
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
        $fileName ="";
        if ($request->file('image') != "") {
            $file = $request->file('image');
            $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/rewards/', $name);
            $fileName = $request->root().'/uploads/rewards/'.$name;
        }
        $rewardId = Reward::insertGetId([
            'heading' => $request->title,
            'image' => $fileName,
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

    public function editreward($id){
        $users = User::where('type',3)->get();
        $reward = Reward::find($id);
        return view('admin.rewards.edit',compact('reward','users'));
    }

    public function updatereward(Request $request,$id){
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
        $fileName ="";$item = Reward::find($id);
        if ($request->file('image') != "") {
            if(isset($item->image)){
                $filename = explode('/',$item->image);
                File::delete('uploads/rewards/'.$filename[5]);
            }
           
            $file = $request->file('image');
            $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/rewards/', $name);
            $fileName = $request->root().'/uploads/rewards/'.$name;
        }else{
            
            $fileName = $item->image;
        }
        $inputData = [
            'heading' => $request->title,
            'image' => $fileName,
            'point' => $request->point
        ];
        Reward::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Updated');
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
                'status' => 1,
                'description' => isset($request->description)?$request->description:"",

                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()

                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

    

            ]);

            

           
            

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
        // dd($list);
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
        $resourceId = SubResource::insertGetId([
            'resource_id' => $request->resource_id,
            'heading' => $request->name,
            'details'=>$request->detail1,
            // 'file' => isset($fileName)?implode(',',$fileName):null,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        $i = 0;
        if ($request->file('file') != "") {
            foreach ($request->file('file') as $file) {
                // $file = $request->file('file');
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                // $file1 = $file->getClientOriginalName(); //Get Image Name

                // $extension = $file->getClientOriginalExtension();  //Get Image Extension
                
                // $name = $file1.'.'.$extension;  //Concatenate both to get FileName (eg: file.jpg)
                $file->move('uploads/subresource/', $name);
                // $fileName[] = $name;
                $subresourcefileId = SubResourceFile::insertGetId([
                    'sub_id' => $resourceId,
                    'file' => $request->root().'/uploads/subresource/'.$name,
                    'filename' => $request->filenamearray11[$i],
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        
                ]);
                $i++;
            }
        }
        
        
        DB::commit();
        return redirect()->back()->with('success', 'Resource added successfully');
    }

    public function updatesubres($id){
        $item = SubResource::find($id);
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
        return view('admin.resource.editsub',compact('item','icons'));
    }
    public function editsubResource(Request $request,$id){
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
        $i = 0;
        if ($request->file('file12') != "") {
            foreach ($request->file('file12') as $file) {
                // $file = $request->file('file');
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                // $file1 = $file->getClientOriginalName(); //Get Image Name

                // $extension = $file->getClientOriginalExtension();  //Get Image Extension
                
                // $name = $file1.'.'.$extension;  //Concatenate both to get FileName (eg: file.jpg)
                $file->move('uploads/subresource/', $name);
                // $fileName[] = $name;
                $subresourcefileId = SubResourceFile::insertGetId([
                    'sub_id' => $id,
                    'file' => $request->root().'/uploads/subresource/'.$name,
                    'filename' => $request->filenamearray[$i],
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        
                ]);
                $i++;
            }
        }
        
        $sub= SubResource::find($id);
        // if(isset($sub->file)){
        //     $array = implode(',',(array) $fileName).','.$sub->file;
        // }else{
        //     $array = implode(',',(array) $fileName);
        // }
        // dd($array);
        $inputData = [
            'heading' => $request->name,
            'details' => $request->detail2,
            // 'file' => $array,
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

    public function deletesubresourcefile(Request $request){
        $file = SubResourceFile::find($request->id);
        $filename = explode('/',$file->file);
        // dd($filename[5]);
        
        if($file->delete()){
            echo "deleted";
            File::delete('uploads/subresource/'.$filename[5]);
        }
        
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
                'date' => 'required'
            ],[
                'name.required' => 'Please enter value journal title',
                // 'image.required'=> 'Please add an image for value journals',
                // 'detail1.required' => 'Please enter short description',
                // 'detail2.required' => 'Please add details about value journals',
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
        $journalId = Journal::insertGetId([
            'journal' => $request->name,
            'image' => $fileName,
            'description' => $request->detail1,
            'journal_date' => Carbon::parse($request->date),
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
                'date' => 'required'
            ],[
                'editname.required' => 'Please enter value journal title',
                // 'image.required'=> 'Please add an image for value journals',
                // 'detail1.required' => 'Please enter short description',
                // 'detail2.required' => 'Please add details about value journals',
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
            $journal = Journal::find($id);
            $fileName = $journal->image;

        }
        $inputData=[
            'journal' => $request->editname,
            'image' => $fileName,
            'description' => $request->detail2,
            'journal_date' => Carbon::parse($request->date),
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
                
            ],[
                'title.required' => 'Please enter value journal title',
                'image.required'=> 'Please add an image for value journals',
                'detail1.required' => 'Please enter short description',
                'detail2.required' => 'Please add details about value journals',
                
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
            // 'date' => 'required'
        ],[
            'title.required' => 'Please enter value journal title',
            // 'image.required'=> 'Please add an image for value journals',
            'detail3.required' => 'Please enter short description',
            'detail4.required' => 'Please add details about value journals',
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
            $journal = ValueJournal::find($id);
            $fileName = $journal->image;

        }
        $inputData=[
            'title' => $request->title,
            'image' => $fileName,
            'short' => $request->detail3,
            'detail' => $request->detail4,
            // 'journal_date' => Carbon::parse($request->date)
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



    public function SalesConnects($modal=null){
        $list = SalesConnect::latest()->paginate(20);
        $techs = Technology::latest()->get();
        $products = Product::latest()->get();
        $modalid=$modal;
        return view('admin.salesconnect.index',compact('list','techs','products','modalid'));
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
        $item = Reschedule::where('salecon_id',$id)->first();
        SalesConnect::where('id',$id)->update(['date_time'=>$request->date.' '.$request->time,'tech_id'=>$request->tech,'product_id'=>$request->product,'meeting_status' => 1,'status'=>1]);
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
        $sales = SalesConnect::find($id);
        $requests = Requests::where('req_id',$sales->id)->where('type','Sales_connect')->first();
        $notificationId = Notification::insertGetId([
            'req_from' => $request->id,
            'from_id'=>Auth::user()->id,
            'to_id' => $sales->from_id,
            'type' => "Sales_Connect",
            'message' => "Meeting Rescheduled for the sales connect request granted",
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        if($notificationId){
            $requests->update(['notifid'=>$notificationId]);
        }
        return redirect()->back()->with('success', 'Rescheduled successfully');
    }



    public function PresetQuestions($techid,$brandid,$fromid){

        $list = PresetQuestion::where('tech_id',$techid)->where('brand_id',$brandid)
        ->whereHas('request', function($q) use($fromid){
            $q->where('from_id', $fromid);
        })
        ->latest()->paginate(20);
        // dd($list);

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
                'name.required' => 'Please enter product name',
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
                'editname.required' => 'Please enter productbrand name',
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

    public function latestevents(){
        $now_str = \Carbon\Carbon::now();
        // dd($now_str);
        $events = Events::where('date_time', '>=', $now_str)->latest()->paginate(20);
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
        return view('admin.events.new',compact('events','icons'));
    }

    public function pastevents(){
        
        $now_str = \Carbon\Carbon::now();
        // dd($now_str);
        $events = Events::where('date_time', '<', $now_str)->latest()->paginate(20);
        // dd($events);
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
        return view('admin.events.past',compact('events','icons'));
    }

    public function addevent(Request $request){
        $validator = Validator::make($request->all(),
            [
                'eventname' => 'required',
                'eventdescription' => 'required',
                'image' => 'required',
                'date'=>'required',
                'time'=>'required',
                'type'=>'required',
                'eventshortdescription'=>'required'
            ],[
                'eventname.required' => 'Please enter event title',
                'eventdescription.required' => 'Please add description for event',
                'image.required' => 'Please add event image',
                'date.required'=>'Please specify the event date',
                'time.required'=>'Please specify the event time',
                'eventshortdescription.required' => 'Please add short description for event',
                'type.required'=>'Please select any user to use the event for registration'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $eventimage = "";
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/event/images', $fileName);
            $eventimage = 'uploads/event/images/' . $fileName;
        }
        if ($request->file('doc') != "") {
            foreach ($request->file('doc') as $file) {
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('uploads/event/documents/', $name);
                $document[] = $name;
            }
        }
        $eventId = Events::insertGetId([
            'user_id' => Auth::User()->id,
            'title'=>$request->eventname,
            'description'=>$request->eventdescription,
            'image'=>$eventimage,
            'document'=>isset($document)?implode(',',$document):null,
            'date_time'=>$request->date.' '.$request->time,
            'access'=>implode(',',$request->type),
            'short'=>$request->eventshortdescription,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'New event added successfully');

    }

    public function updateevent($id,Request $request){
        $validator = Validator::make($request->all(),
        [
            'eventname' => 'required',
            'eventdescription' => 'required',
            // 'image' => 'required',
            'date1'=>'required',
            'time'=>'required',
            'type'=>'required',
            'eventshortdescription'=>'required'
        ],[
            'eventname.required' => 'Please enter event title',
            'eventdescription.required' => 'Please add description for event',
            // 'image.required' => 'Please add event image',
            'date1.required'=>'Please specify the event date',
            'time.required'=>'Please specify the event time',
            'eventshortdescription.required' => 'Please add short description for event',
            'type.required'=>'Please select any user to use the event for registration'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $event = Events::find($id);
        $eventimage = "";$document=[];
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/event/images', $fileName);
            $eventimage = 'uploads/event/images/' . $fileName;
        }else{
            $eventimage = $event->image;
        }
        if ($request->file('doc') != "") {
            foreach ($request->file('doc') as $file) {
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('uploads/event/documents/', $name);
                $document[] = $name;
            }
        }

        if(isset($event->document)){
            if(!empty($document)){
                $array = $event->document.','.implode(',',(array) $document);
            }
            else{
                $array = $event->document;
            }
        }else{
            if(!empty($document)){
                $array = implode(',',(array) $document);
            }else{
                $array = null;
            }
            
        }

        $inputData = [
            'user_id' => Auth::User()->id,
            'title'=>$request->eventname,
            'description'=>$request->eventdescription,
            'image'=>$eventimage,
            'document'=>$array,
            'date_time'=>$request->date1.' '.$request->time,
            'access'=>implode(',',$request->type),
            'short'=>$request->eventshortdescription,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ];
        Events::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Event details updated successfully');

    }

    public function activeEvent($id){
        $status="";
        $event=Events::find($id);
        if($event->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Events::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Event status updated successfully');
    }

    public function copyEvent(Request $request,$id){
        $validator = Validator::make($request->all(),
        [
            'eventname' => 'required',
            'eventdescription' => 'required',
            // 'image' => 'required',
            'date'=>'required',
            'time'=>'required',
            'type'=>'required',
            'eventshortdescription'=>'required'
        ],[
            'eventname.required' => 'Please enter event title',
            'eventdescription.required' => 'Please add description for event',
            // 'image.required' => 'Please add event image',
            'date.required'=>'Please specify the event date',
            'time.required'=>'Please specify the event time',
            'eventshortdescription.required' => 'Please add short description for event',
            'type.required'=>'Please select any user to use the event for registration'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $eventimage = ""; $old = Events::find($id);
        if ($request->file('image') != "") {
            
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/event/images', $fileName);
            $eventimage = 'uploads/event/images/' . $fileName;
        }
        if ($request->file('doc') != "") {
            foreach ($request->file('doc') as $file) {
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('uploads/event/documents/', $name);
                $document[] = $name;
            }
        }
        $eventId = Events::insertGetId([
            'user_id' => Auth::User()->id,
            'title'=>$request->eventname,
            'description'=>$request->eventdescription,
            'image'=>!empty($eventimage)?$eventimage:$old->image,
            'document'=>isset($document)?implode(',',$document):null,
            'date_time'=>$request->date.' '.$request->time,
            'access'=>implode(',',$request->type),
            'short'=>$request->eventshortdescription,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect('admin/list/new/events')->with('success', 'New event added successfully');

    }
    public function mainservices(){
        $list = MainService::latest()->paginate(20);
        return view('admin.mainservices.list',compact('list'));
    }

    public function addMainService(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'servicename' => 'required',
                'type' => 'required'
            ],[
                'servicename.required' => 'Please enter service name',
                'type.required' => 'Please select user type '
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        $resourceId = MainService::insertGetId([
            'name' => $request->servicename,
            'type'=>implode(',',$request->type),
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Service added successfully');
    }

    public function editMainService(Request $request,$id){
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
        MainService::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Service added successfully');
    }

    public function activeMainService($id){
        $status="";
        $resource=MainService::find($id);
        if($resource->status==0){
            $status=1;
        }else{
            $status=0;
        }
        MainService::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Service status updated successfully');
    }

    public function subMainService($id){
        $list = SubService::where('main_id',$id)->latest()->paginate(20);
        $main =$id;
        return view('admin.mainservices.sub',compact('list','main'));
    }

    public function addsubMainService(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'servicename' => 'required',
                'detail1' => 'required',
            ],[
                'servicename.required' => 'Please enter service name',
                'detail1.required' => 'Please add description about service'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        } 
        $resourceId = SubService::insertGetId([
            'main_id' => $request->main,
            'name' => $request->servicename,
            'description'=>$request->detail1,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        DB::commit();
        return redirect()->back()->with('success', 'Sub service added successfully');
    }

    public function editsubMainService(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'editname' => 'required',
                'detail2' => 'required',
            ],[
                'editname.required' => 'Please enter service name',
                'detail2.required' => 'Please add description about service'
            ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        
        $inputData = [
            'name' => $request->editname,
            'description' => $request->detail2
        ];
        SubService::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Sub service updated successfully');
    }

    public function activesubMainService($id){
        $status="";
        $event=SubService::find($id);
        if($event->status==0){
            $status=1;
        }else{
            $status=0;
        }
        SubService::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Service status updated successfully');
    }

    public function businessSolutions(){
        $list = BusinessSolution::latest()->paginate(20);
        return view('admin.business.list',compact('list'));
    }

    public function addbusinesssolution(Request $request){
        $validator = Validator::make($request->all(),
            [
                'businessname' => 'required',
                'businessdescription' => 'required',
            ],[
                'businessname.required' => 'Please enter business name',
                'businessdescription.required' => 'Please add description about the business',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::back()->withErrors($messages)->withInput();
            }
            $businessId = BusinessSolution::insertGetId([
                'name' => $request->businessname,
                'description' => $request->businessdescription,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

            ]);
            DB::commit();
            return redirect()->back()->with('success','Business solution created successfully');
    }

    public function editbusinesssolution(Request $request,$id){
        $validator = Validator::make($request->all(),
            [
                'editname' => 'required',
                'editdescription' => 'required',
            ],[
                'editname.required' => 'Please enter business name',
                'editdescription.required' => 'Please add description about the business',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::back()->withErrors($messages)->withInput();
            }
            $inputData = [
                'name' => $request->editname,
                'description' => $request->editdescription,
                'status'=>$request->status

            ];
            BusinessSolution::where('id',$id)->update($inputData);
            return redirect()->back()->with('success','Business solution updated successfully');
    }

    public function pagesetiing(){
        $list = Description::latest()->paginate(20);
        return view('pagesetting',compact('list'));
    }

    public function addpagedetails(Request $request){
        $validator = Validator::make($request->all(),
        [
            'page' => 'required',
            'pagedescription' => 'required',
        ],[
            'page.required' => 'Please select a page',
            'pagedescription.required' => 'Please add description for the page',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $count = Description::where('page',$request->page)->count();
        if($count==0){
            $dataId = Description::insertGetId([
                'page' => $request->page,
                'description' => $request->pagedescription,
                'status'=>0,//active
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

            ]);
            DB::commit();
            return redirect()->back()->with('success',$request->page.' details added successfully');
    
        }else{
            return redirect()->back()->with('error','This page is already done.Please check !!');
        }
    }

    public function editpage(Request $request,$id){
        $validator = Validator::make($request->all(),
        [
            // 'page' => 'required',
            'editdescription' => 'required',
        ],[
            // 'page.required' => 'Please select a page',
            'editdescription.required' => 'Please add description about the page',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $item = Description::find($id);
        $inputData = [
            // 'page' => $request->page,
            'description' => $request->editdescription,
        ];
        Description::where('id',$id)->update($inputData);
        return redirect()->back()->with('success',$item->page.' details updated successfully');
        
    }

    public function feedbacks(){
        $list = Feedback::latest()->paginate(20);
        return view('feedbacks',compact('list'));
    }

    public function scheduleacceptforsalesconnect(Request $request,$id){
        SalesConnect::where('id',$id)->update(['date_time'=>$request->date.' '.$request->time,'meeting_status' => 1]);
        $sales = SalesConnect::find($id);
        $notificationId = Notification::insertGetId([
            'req_from' => $id,
            'from_id'=>Auth::user()->id,
            'to_id' => $sales->from_id,
            'type' => "Sales_Connect",
            'message' => "Meeting scheduled for the sales connect request granted",
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        return redirect()->back()->with('success','Meeting scheduled for the sales connect request granted');
    }

    public function requestslist($type,$id,$modal=null){
        if($modal!=null){
            // dd($modal);
            if($type=='Redeem_Request'){
                $item=Requests::where('notifid',$modal)->first();
                $modalid = 'viewModal'.$item->id;
                $id=$item->from_id;
            }else{
                $item=Requests::where('notifid',$modal)->first();
                $modalid = 'viewModal'.$item->id;
                $id=$item->req_id;
            }
            
        }else{
            $modalid =$modal;
        }
        
        if($type=="Sub_service"){
            $list = Requests::where('req_id',$id)->where('type',$type)->with(['subservice' => function ($q) use ($id) {
                $q->where('id',$id);
            }])->latest()->paginate(20);
            $sub = SubService::find($id);
            return view('admin.mainservices.requestlist',compact('list','modalid','sub'));
        }elseif($type=="Business_Solution"){
            $list = Requests::where('req_id',$id)->where('type',$type)->with(['business' => function ($q) use ($id) {
                $q->where('id',$id);
            }])->latest()->paginate(20);
            return view('admin.business.requestlist',compact('list','modalid'));
        }elseif($type=="Redeem_Request"){
            $list = Requests::where('from_id',$id)->where('type',$type)->with(['redeem' => function ($q) use ($id) {
                $q->where('partner_id',$id);
            }])->latest()->paginate(20);
            // dd($list);
            return view('admin.rewards.requestlist',compact('list','modalid'));
        }
        
    }
    public function RequestRespond(Request $request,$id){
        
        $data = Requests::find($id);
        if($request->status==1){
            $status = "confirmed";
        }elseif($request->status==2){
            $status = "rejected";
        }
        if($request->type=="Sub_service"){
            $item = SubService::where('id',$data->req_id)->first();
            $heading= $item->name;
            $type = "Sub_service";
        }elseif($request->type=="Business_Solution"){
            $item = BusinessSolution::where('id',$data->req_id)->first();
            $heading= $item->name;
            $type = "Business_Solution";
        }
        $notificationId = Notification::insertGetId([
            'req_from' => $data->id,
            'from_id'=>Auth::user()->id,
            'to_id' => $data->from_id,
            'type' => $type,
            'message' => "Appoinment for ".$heading." is ".$status,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        Requests::where('id',$id)->update(['status'=>$request->status,'notifid'=>$notificationId]);
        return redirect()->back()->with('success','Request updated successfully');
    }

    public function readNotification($id,$type){
        if($type=='All'){
            Notification::where('to_id',Auth::User()->id)->update(['status'=>1]);
            return redirect()->back();
        }elseif($type=='Sales_Connect'){
            Notification::where('id',$id)->update(['status'=>1]);
            $item = Notification::find($id);
            $modalid = 'viewModal'.$item->req_from;
            return redirect()->route('Sales_Connect',['modal'=>$modalid]);
        }elseif($type=='Sub_service'){
            Notification::where('id',$id)->update(['status'=>1]);
            $item = Notification::find($id);
            $modalid = $id;
            return redirect()->route('Request_Call',['type'=> $type,'id'=>$item->req_from,'modal'=>$modalid]);
        }elseif($type=='Business_Solution'){
            Notification::where('id',$id)->update(['status'=>1]);
            $item = Notification::find($id);
            $modalid = $id;
            return redirect()->route('Request_Call',['type'=> $type,'id'=>$item->req_from,'modal'=>$modalid]);
        }elseif($type=='Redeem_Request'){
            Notification::where('id',$id)->update(['status'=>1]);
            $item = Notification::find($id);
            $modalid = $id;
            return redirect()->route('Request_Call',['type'=> $type,'id'=>$item->req_from,'modal'=>$modalid]);
        }
        
        
    }
    public function promotions(){
        $list = Promotion::latest()->paginate(20);
        return view('admin.promotion.list',compact('list'));

    }
    public function addpromotion(Request $request){
        $validator = Validator::make($request->all(),
        [
            'promotionname' => 'required',
            'promotiondescription1' => 'required',
            'image' => 'required',
            'date1'=>'required',
            'date2'=>'required'
        ],[
            'promotionname.required' => 'Please enter promotion title',
            'promotiondescription1.required' => 'Please add description for promotion',
            'image.required' => 'Please add event image',
            'date1.required'=>'Please specify the from date',
            'date2.required'=>'Please specify the to date',
            
        ]);
    if ($validator->fails()) {
        $messages = $validator->messages();
        return Redirect::back()->withErrors($messages)->withInput();
    }
    $image = "";
    if ($request->file('image') != "") {
        
        $file = $request->file('image');
        $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
        $file->move('uploads/promotion/images', $fileName);
        // $eventimage = 'uploads/event/images/' . $fileName;
        $image = $request->root().'/uploads/promotion/images/'.$fileName;
    }
    $promotionId = Promotion::insertGetId([
        'name'=>$request->promotionname,
        'description'=>$request->promotiondescription1,
        'image'=>$image,
        'from_date'=>$request->date1,
        'to_date'=>$request->date2,
        "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
        "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

    ]);
    
    DB::commit();
    return redirect()->back()->with('success', 'New promotion added successfully');
    }
    public function updatepromotion(Request $request,$id){
        $validator = Validator::make($request->all(),
        [
            'promotionname' => 'required',
            'promotiondescription' => 'required',
            // 'image' => 'required',
            'date1'=>'required',
            'date2'=>'required'
        ],[
            'promotionname.required' => 'Please enter promotion title',
            'promotiondescription1.required' => 'Please add description for promotion',
            // 'image.required' => 'Please add event image',
            'date1.required'=>'Please specify the from date',
            'date2.required'=>'Please specify the to date',
            
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }
        $promotion = Promotion::find($id);
        $image = "";$document=[];
        if ($request->file('image') != "") {
            $file = $request->file('image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('uploads/promotion/images', $fileName);
            // $eventimage = 'uploads/event/images/' . $fileName;
            $image = $request->root().'/uploads/promotion/images/'.$fileName;
        }else{
            $image = $promotion->image;
        }

        $inputData = [
            'name'=>$request->promotionname,
            'description'=>$request->promotiondescription,
            'image'=>$image,
            'from_date'=>$request->date1,
            'to_date'=>$request->date2,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ];
        Promotion::where('id',$id)->update($inputData);
        return redirect()->back()->with('success', 'Promotion details updated successfully');

    }
    public function activepromotion($id){
        $status="";
        $promotion=Promotion::find($id);
        if($promotion->status==0){
            $status=1;
        }else{
            $status=0;
        }
        Promotion::where('id',$id)->update(['status'=> $status]);
        return redirect()->back()->with('success','Promotion status updated successfully');
    }

    public function upload_images(Request $request){
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
           
            $request->file('upload')->move(public_path('images'), $fileName);
       
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('images/'.$fileName); 
            $msg = 'Image uploaded successfully'; 
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
                  
            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }
    }

    public function PartnerRedeemRequestResponse(Request $request,$id){
        // dd($request->all());
        if($request->status==1){
            $text = 'granted';
        }else{
            $text = 'rejected';
        }
        $requests = Requests::find($id);
        Redeem::where('id',$requests->req_id)->update(['status'=>$request->status]);
        $deduction = Redeemdeduction::where('partner_id',$requests->from_id)->first();

        if(isset($deduction)){

            $inputData = [

                'total_reward' => $deduction->total_reward - $request->amount,

                'total_redeem' => $deduction->total_redeem + $request->amount

            ];

            // dd($inputData);

            Redeemdeduction::where('id',$deduction->id)->update($inputData);

        }else{

            $totalrewards = Reward::where('partner_id',$requests->from_id)->selectRaw('sum(rewards.point) as score')->get();

            $totalredeems = Redeem::where('partner_id',$requests->from_id)->selectRaw('sum(redeems.amount) as score')->get();

            $score1 = isset($totalrewards[0]->score)?$totalrewards[0]->score:0;

            $score2 = isset($totalredeems[0]->score)?$totalredeems[0]->score:0;

            $redeemId = Redeemdeduction::insertGetId([

                'total_reward' => ($score1 - $request->amount),

                'partner_id' => $requests->from_id,

                'total_redeem' => $score2 + $request->amount

            ]);

        }

        
        $notificationId = Notification::insertGetId([
            'req_from' => $requests->id,
            'from_id'=>Auth::user()->id,
            'to_id' => $requests->from_id,
            'type' => "Redeem_Request",
            'message' => "Redeem request for ".$requests->from->name. "  is  ".$text,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        if($notificationId){
            $requests->update(['notifid'=>$notificationId,'status'=>$request->status]);
        }
        return redirect()->route('Request_Call',['type'=> 'Redeem_Request','id'=>$requests->id,'modal'=>$notificationId]);
        // return redirect()->back()->with('success','Request responded successfully');
    }
    public function eventreport($id){
        $ev = Events::find($id);
        $list = EventRegister::where('event_id',$id)->has('user')->latest()->paginate(20);
        $services = Service::all();
        $technologies = Technology::all();
        $products = Product::all();
        return view('admin.events.report',compact('list','ev','services','technologies','products'));
    }
    public function unselectRegion(Request $request){
        dd($request->all());
    }
    public function logout(Request $request)

    {

        Auth::logout();

        Session::flush();

        return redirect('admin/login');

    }

}

