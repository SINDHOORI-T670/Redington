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
use App\Models\Reward;
use App\Models\Redeemdeduction;
use App\Models\PartnerReward;
use App\Models\Resource;
use App\Models\SubResource;
use App\Models\UserSpec;
use App\Models\MainStory;
use App\Models\Redeem;
use App\Models\Requests;
class PartnerController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
        // $this->middleware('CheckUser');
        $this->middleware(function ($request, $next) {
            $settings = Setting::select('key', 'value')->get();
            $company = $settings->mapWithKeys(function ($item) {
                    return [$item['key'] => $item['value']];
            });
            $not_count = Notification::where('to_id',Auth::User()->id)->where('message','!=',null)->where('status',0)->with('from')->has('from')->count();
            $new_notifs = Notification::where('to_id',Auth::User()->id)->where('message','!=',null)->where('status',0)->with('from')->has('from')->latest()->get();
            // dd($new_notifs);
            view()->share(['company' => $company,'not_count' => $not_count,'new_notifs' => $new_notifs]);
            return $next($request);
        });
    }

    public function index(){
        return view('partner.home');
    }

    public function editprofile(){
        // dd("test");
        $customer = User::find(Auth::User()->id);
        $technologies= Technology::where('status',0)->get();
        $products = Product::where('status',0)->get();
        $userspecs=UserSpec::where('user_id',Auth::User()->id)->first();
        // dd($userspecs);
        return view('partner.profile',compact('customer','technologies','products','userspecs'));
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
            'password' => isset($request->password)?Hash::make($request->password):$user->password,
        ];
        // dd($data);
        User::where('id',Auth::User()->id)->update($data);
        $check = UserSpec::where('user_id',Auth::User()->id)->count();
        // dd($check);
        if($check==0){
                $feature = UserSpec::insertGetId([

                    'user_id' => Auth::User()->id,
                    
                    'product_id' => isset($request->products)?implode(",",$request->products):null,

                    'technology_id' => isset($request->technologies)?implode(",",$request->technologies):null,

                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()

                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()



                ]);
        }else{
            $feature =[
                'product_id' => isset($request->products)?implode(",",$request->products):"",

                'technology_id' => isset($request->technologies)?implode(",",$request->technologies):"",

            ];
            // dd($feature);
            UserSpec::where('user_id',Auth::User()->id)->update($feature);
        }
        // dd($customer);
        return redirect()->back()->with('message', 'Profile Updated Successfully');
        
    }
    
    public function ListRewards(){
        $total = Redeemdeduction::where('partner_id',Auth::User()->id)->first();
        $rewards = PartnerReward::where('partner_id',Auth::User()->id)->latest()->paginate(20);
        return view('partner.rewards.list',compact('rewards','total'));
    }

    public function journals(){
        $list = Journal::where('status',0)->latest()->paginate(20);
        return view('partner.journals.list',compact('list'));
    }
    public function subjournals($id){
        $journal = Journal::find($id);
        $list = ValueJournal::where('journal_id',$id)->where('status',0)->latest()->paginate(20);
        return view('partner.journals.sublist',compact('list','journal'));
    }
    public function mainStories(){
        $list = MainStory::where('status',0)->latest()->paginate(20);
        return view('partner.stories.mainlist',compact('list'));
    }
    public function ValuestoriesList($id){
        $list = ValueStory::where('story_id',$id)->where('status',0)->latest()->paginate(20);
        return view('partner.stories.list',compact('list'));
    }
    public function resources(){
        $list = Resource::where('status',0)->latest()->paginate(20);
        return view('partner.resource.list',compact('list'));
    }
    public function subresources($id){
        $list = SubResource::where('status',0)->where('resource_id',$id)->latest()->paginate(20);
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
        return view('partner.resource.sublist',compact('list','resource','icons'));
    }
    
    public function listsalesconnects(){
        $list = SalesConnect::where('from_id',Auth::User()->id)->latest()->paginate(20);
        $techs = Technology::latest()->get();
        $products = Product::latest()->get();
        
        return view('partner.salesconnect.list',compact('list','techs','products'));
    }

    public function latestevents(){
        $now_str = \Carbon\Carbon::now();
        // dd($now_str);
        $events = Events::where('date_time', '>=', $now_str)->where('status',0)->latest()->paginate(20);
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
        return view('partner.events.new',compact('events','icons'));
    }

    public function pastevents(){
        
        $now_str = \Carbon\Carbon::now();
        // dd($now_str);
        $events = Events::where('date_time', '<', $now_str)->where('status',0)->latest()->paginate(20);
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
        return view('partner.events.past',compact('events','icons'));
    }

    public function RegisteredEvents(){
        $events = Events::select('events.*')
        ->join('event_registers','events.id','=','event_registers.event_id')
        ->where('event_registers.user_id',Auth::User()->id)
        ->latest()->paginate(20);
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
        return view('partner.events.registered',compact('events','icons'));
    }
    public function ListServices(){
        $list = MainService::where('status',0)->latest()->paginate(20);
        return view('partner.services.list',compact('list'));
    }
    public function ListSubServices($id){
        // SubService::truncate();
        $list = SubService::where('main_id',$id)->where('status',0)->latest()->paginate(20);
        $main =$id;
        return view('partner.services.sub',compact('list','main'));
    }
    public function RedeemHistory($id){
        $user = User::find($id);

        $total = Redeemdeduction::where('partner_id',$id)->first();
        $redeems = Redeem::where('partner_id',$id)->latest()->paginate(3);

        return view('partner.rewards.redeemhistory',compact('redeems','user','total'));

    }
    public function RequestFormForRedeem(){
        $user = User::find(Auth::User()->id);

        return view('partner.rewards.redeem',compact('user'));
    }

    public function SendRedeemRequest(Request $request){
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

                'status' => 0, //requested

                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()

                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

            ]);
            $requestId = Requests::insertGetId([
                'req_id' => $redeemId,
                'from_id'=>Auth::User()->id,
                'type'=>"Redeem_Request", //redeem table 
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
            ]);
            $admin = User::find(1);$user = User::find(Auth::User()->id);
            if($requestId){
                $notificationId = Notification::insertGetId([
                    'req_from' => $requestId,
                    'from_id'=>Auth::User()->id,
                    'to_id' => $admin->id,
                    'type' => "Redeem_Request",
                    'message' => "Redeem requested from ".$user->name,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        
                ]);
                Requests::where('id',$requestId)->update(['notifid'=>$notificationId]);
            }
            DB::commit();

            return redirect('partner/redeem/history/'.$request->id)->with('success', 'Redeem request send successfully');

    

        }else{

            $messages = ['check'=> 'There is no rewards for redeem,Please try again'];

            return Redirect::back()->withErrors($messages)->withInput();

        }

    }

    public function readNotification($id,$type){
        if($type=='All'){
            Notification::where('to_id',Auth::User()->id)->update(['status'=>1]);
            return redirect()->back();
        }else{
            Notification::where('id',$id)->update(['status'=>1]);
            $item = Notification::find($id);
            $modalid = $item->req_from;
            return redirect()->route('Redeem-History',['id'=>Auth::User()->id]);
        }
        
        
    }
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('login');
    }
}
