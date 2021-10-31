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
            view()->share(['company' => $company]);
            return $next($request);
        });
    }

    public function index(){
        return view('partner.home');
    }

    public function editprofile(){
        // dd("test");
        $customer = User::find(Auth::User()->id);
        return view('partner.profile',compact('customer'));
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
    public function ValuestoriesList(){
        $list = ValueStory::where('status',0)->latest()->paginate(20);
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
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('login');
    }
}
