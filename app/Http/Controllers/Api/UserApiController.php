<?php



namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\User;

use App\Models\Service;

use App\Models\Technology;

use App\Models\Resource;

use App\Models\SubResource;

use App\Models\Journal;

use App\Models\ValueJournal;

use App\Models\ValueStory;

use App\Models\Brand;
use App\Models\BusinessSolution;
use App\Models\Region;

use App\Models\SalesConnect;

use App\Models\PresetQuestion;

use App\Models\Product;

use App\Models\MainService;

use App\Models\Events;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use App\Models\Notification;

use App\Models\Requests;

use App\Models\EventRegister;
use App\Models\History;
use App\Models\SubService;

class UserApiController extends Controller

{

    public function api_validation($request){

        $tokenget = $request->header('Authorization');

        //return $tokenget;

        if(strpos($tokenget,' ') > -1)

            list($b,$t) = @explode(' ',$tokenget);

        else

            $b = $tokenget;



        if($b != '' && $b != "Bearer"){

            $data['response'] = "Token error!!";

            $data['status'] = "Error";

            //return response()->json($data);

            return 0;

        }



        $token = str_replace("Bearer ", "", $tokenget);

        $valid = $this->validateInternalToken($token,$request->user_id);

        return  $valid;

    }



    public function validateInternalToken($token,$userId){

        // $actualToken = config('constants.TOKEN');

        $user = User::where('id',$userId)->where('api_token',$token)->exists();

        if($user){

            return 1;

        }else{

            return 0;

        }  

    }



    public function operation_validation($request){

        if(!isset($request['op'])){

            return "Error";

        }

        else{

            return $request['op'];

        }



    }



    public function apiOperations(Request $request){

        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }



        $op = $this->operation_validation($request);

        if($op == "Error"){

            $data['response'] = 'No OP';

            $data['status'] = "Error";

            return response()->json($data);

        }



        switch ($op) {

            case 'get-profile':

                $data=$this->getProfile($request);

                break;
            case 'services':

                $data=$this->serviceList($request);

                break;
            case 'technologies':

                $data=$this->technologyList($request);

                break;
            case 'resources':

                $data=$this->resourceList($request);

                break;
            case 'subresources':

                $data=$this->subresourceList($request);

                break;
            case 'value-journals':

                $data=$this->journalList($request);

                break;
            case 'subjournals':

                $data=$this->subJournals($request);

                break;
            case 'value-stories':

                $data=$this->valuestories($request);

                break;
            case 'brands':

                $data=$this->brands($request);

                break;
            case 'regions':

                $data=$this->regions($request);

                break;
            case 'salesconnects':

                $data=$this->salesconnectList($request);

                break;
            case 'presetQuestions':

                $data=$this->presetQuestions($request);

                break;
            case 'products':

                $data=$this->products($request);

                break;

            case 'main-services': 
                $data=$this->mainserviceList($request);
                break;
            case 'newevents': 
                $data=$this->neweventsList($request);
                break;
            case 'pastevents': 
                $data=$this->pasteventsList($request);
                break;
    
            default:

                $data['response'] = 'Invalid OP';

                $data['status'] = "Error";

        }

        return response()->json($data);

    }



    public function getProfile(Request $request,$user_id){
        $userData   = User::with('userSpec')->where('id',$user_id)->first();
        return $userData;
    }



    public function serviceList(Request $request){

        $serviceData = Service::where('status',0)->get();

        return $serviceData;

    }



    public function technologyList(Request $request){

        $technologyData = Technology::where('status',0)->get();

        return $technologyData;

    }



    public function resourceList(Request $request){

        $resourceData = Resource::where('status',0)->whereJsonContains('type', 2)->get();
        $response['data']=$resourceData;
        return $response;

    }



    public function subresourceList($id){

        $subresourceData = SubResource::where('resource_id',$id)->where('status',0)->get();

        return $subresourceData;

    }



    public function journalList(){

        $journalData = Journal::where('status',0)->get();

        return $journalData;

    }



    public function subJournals($id){

        $subJournalData = ValueJournal::where('journal_id',$id)->where('status',0)->get();

        return $subJournalData;

    }

    

    public function valuestories(){

        $storyData = ValueStory::where('status',0)->get();

        return $storyData;

    }



    public function brands(){

        $brandData = Brand::where('status',0)->get();

        return $brandData;

    }



    public function regions(){

        $regionData = Region::where('status',0)->get();

        return $regionData;

    }



    public function salesconnectList(){

        $salesData = SalesConnect::where('status',0)->get();

        return $salesData;

    }

    public function presetQuestions(){
        $QuestionData = PresetQuestion::where('status',0)->get();
        return $QuestionData;
    }

    public function products(){
        $productData = Product::where('status',0)->get();
        return $productData;
    }

    public function mainserviceList(Request $request){
        $serviceData = MainService::where('status',0)->get();
        $response['data']= $serviceData;
        return $response;
    }

    public function neweventsList(Request $request){
        $now_str = \Carbon\Carbon::now();
        $eventData = Events::where('date_time', '>=', $now_str)->where('status',0)->get();
        return $eventData;
    }
    public function pasteventsList(Request $request){
        $now_str = \Carbon\Carbon::now();
        $eventData = Events::where('date_time', '<', $now_str)->where('status',0)->get();
        return $eventData;
    }
    public function connectnow(Request $request){
        $validator = Validator::make($request->all(),
            [
                'technology' => 'required',
                'brand' => 'required',
                'region'=>'required',
                'poc'=>'required'
            ],[
                'technology.required' => 'Please select technology',
                'brand.required' => 'Please select brand',
                'region.required'=>'Please select region',
                'poc.required'=>'Please select POC'
            ]);
        if ($validator->fails()) {
            $response['status']  = 'error';
            $response['message'] = $validator->messages()->first();
        }
        $fromid = Auth::User()->id;
        $queries = PresetQuestion::where('tech_id',$request->technology)->where('brand_id',$request->brand)->get();
        $response['data'] = [
                'tech_id' => $request->technology,
                'brand_id'=>$request->brand,
                'region_id'=>$request->region,
                'poc_user_id'=>$request->poc,
                'from_id'=>Auth::User()->id
            ]; 
        $response['questions'] = $queries; 
        return $response;
    }
    public function sendReply(Request $request,$id){
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
        $connectId = SalesConnect::insertGetId([
            'tech_id' => $request->technology,
            'brand_id'=>$request->brand,
            'region_id'=>$request->region,
            'poc_user_id'=>$request->poc,
            'from_id'=>Auth::User()->id,
            'status'=>2, //poc connect
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        $replyId = ReplyRequest::insertGetId([
            'req_id' => $id,
            'from_id'=>Auth::User()->id,
            'reply'=>$request->reply,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        if(isset($replyId)){
            return response()->json(['status' => 'success','message'=>'Reply Sent'], 200);
        }else{
            $response['status']  = 'error';
            $response['message'] = $validator->messages()->first();
        }
    }

    public function scheduleMeeting(Request $request,$id){
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
        $connectId = SalesConnect::insertGetId([
            'tech_id' => $request->technology,
            'brand_id'=>$request->brand,
            'region_id'=>$request->region,
            'poc_user_id'=>$request->poc,
            'from_id'=>Auth::User()->id,
            'status'=>1, //schedule meeting
            'date_time'=>$request->date.' '.$request->time,
            'product_id'=>$request->product,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        
        if(isset($connectId)){
             return response()->json(['status' => 'success','message'=>'Meeting Scheduled'], 200);
        }else{
            $response['status']  = 'error';
            $response['message'] = $validator->messages()->first();
        }
    }

    public function getRequests($userid){
        $requestData = Requests::where('from_id',$userid)->with('subservice','business')->get();
        $response['data']= $requestData;
        return $response;
    }

    public function getschedules($userid){
        $salesData = SalesConnect::where('from_id',$userid)->get();
        $response['data']= $salesData;
        return $response;
    }

    public function myevents($userid){
        $eventData = EventRegister::where('user_id',$userid)->get();
        $response['data']= $eventData;
        return $response;
    }

    public function myhistory($userid){
        $historyData = History::where('from',$userid)->get();
        $response['data']= $historyData;
        return $response;
    }

    public function newrequest($userid,$id,Request $request){
        $requestId = Requests::insertGetId([
            'req_id' => $id,
            'from_id'=>$userid,
            'type'=>$request->type, //Sub_service,Business_Solution,Sales_Connect
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        ]);
        $admin = User::find(1);
        $user = User::find($request->user_id);
        if($request->type=="Sub_service"){
            $sub = SubService::find($id);
            $message = "Appoinment for ".$sub->name." is requested from ".$user->name;
        }elseif($request->type=="Business_Solution"){
            $business = BusinessSolution::find($id);
            $message = "Meeting schedule for ".$business->name." is requested from ".$user->name;
        }else{
            $sales = SalesConnect::find($id);
            $message = "A meeting requested from ".$user->name;
        }
        if(isset($requestId)){
            $notificationId = Notification::insertGetId([
                'req_from' => $requestId,
                'from_id'=>$request->user_id, //logged in user
                'to_id' => $admin->id,
                'type' => $request->type,
                'message' => $message,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
    
            ]);
            Request::where('id',$requestId)->update(['notifid'=>$notificationId]);
            $response['status']     = 'Success';
            $response['data']       = 'Request Send';
            return $response;
        }else{
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }
    }

    public function Salesconnect(Request $request){
        $meetingRequestfromcustomer= SalesConnect::where('poc_user_id',$request->user_id)->where('status',1)->without('region','user','reschedule','product','requestdata')->has('from')
        ->whereHas('from', function($q) {
            $q->where('type', 2);
        })
        ->latest()->get();
        $meetingRequestfrompartner= SalesConnect::where('poc_user_id',$request->user_id)->where('status',1)->without('region','user','reschedule','product','requestdata')->has('from')
        ->whereHas('from', function($q) {
            $q->where('type', 3);
        })
        ->latest()->get();

        $serviceRequestfromcustomer= SalesConnect::select('sales_connects.*','preset_questions.id as questionId','preset_questions.question as Question','reply_requests.id as replyId','reply_requests.req_id as reply_questionId','reply_requests.from_id as replyFrom','reply_requests.reply as Reply')
        ->where('sales_connects.poc_user_id',$request->user_id)->where('sales_connects.status',2)->without('region','user','reschedule','product','requestdata')->has('from')
        ->whereHas('from', function($q) {
            $q->where('type', 2);
        })
        ->join('preset_questions','sales_connects.tech_id','=','preset_questions.tech_id')
        ->whereColumn('preset_questions.brand_id', '=', 'sales_connects.brand_id')
        ->join('reply_requests','preset_questions.id','=','reply_requests.req_id')
        ->latest()->get();
        $serviceRequestfrompartner= SalesConnect::select('sales_connects.*','preset_questions.id as questionId','preset_questions.question as Question','reply_requests.id as replyId','reply_requests.req_id as reply_questionId','reply_requests.from_id as replyFrom','reply_requests.reply as Reply')
        ->where('sales_connects.poc_user_id',$request->user_id)->where('sales_connects.status',2)->without('region','user','reschedule','product','requestdata')->has('from')
        ->whereHas('from', function($q) {
            $q->where('type', 3);
        })
        ->join('preset_questions','sales_connects.tech_id','=','preset_questions.tech_id')
        ->whereColumn('preset_questions.brand_id', '=', 'sales_connects.brand_id')
        ->join('reply_requests','preset_questions.id','=','reply_requests.req_id')
        ->latest()->get();

        $response['meetingRequestfromcustomer'] = $meetingRequestfromcustomer;
        $response['meetingRequestfrompartner']  = $meetingRequestfrompartner;
        $response['serviceRequestfromcustomer'] = $serviceRequestfromcustomer;
        $response['serviceRequestfrompartner']  = $serviceRequestfrompartner;
    }

}

