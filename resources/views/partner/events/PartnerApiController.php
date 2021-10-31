<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainService;
use App\User;
use App\Models\Resource;
use App\Models\Brand;
use App\Models\Region;
use App\Models\Technology;
use App\Models\BusinessSolution;
use App\Models\Service;
use App\Models\Product;
use App\Models\SalesConnect;
use App\Models\PresetQuestion;
use App\Models\ReplyRequest;
use App\Models\Description;
use App\Models\Feedback;
use App\Models\Journal;
use App\Models\ValueStory;
use App\Models\Events;
use App\Models\Requests;
use App\Models\EventRegister;
use App\Models\SubService;
use App\Models\History;
use App\Models\Notification;
use App\Models\PartnerReward;
use App\Models\Redeemdeduction;
use App\Models\Promotion;
use App\Models\Redeem;

use Illuminate\Support\Facades\Validator;

class PartnerApiController extends Controller
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

        $user = User::where('api_token',$token)->exists();

        if($user){

            return 1;

        }else{

            return 0;

        }  

    }

    public function mainserviceList(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $serviceData = MainService::where('status',0)->where('type',3)->get();
        if($serviceData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$serviceData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;
    }

    public function resourceList(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $resourceData = Resource::where('status',0)->whereRaw("find_in_set('3',type)")->get();
        if($resourceData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$resourceData;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No resources found';
        }
        return $response;
    }

    public function technologyList(Request $request){

        $technologyData = Technology::where('status',0)->get();

        if($technologyData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$technologyData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;

    }

    public function brands(Request $request){

        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $brandData = brand::where('status',0)->get();
        if($brandData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$brandData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;

    }

    public function regions(Request $request){

        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $regionData = Region::where('status',0)->get();
        if($regionData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$regionData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;

    }

    public function businessSolutions(Request $request){

        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $businessData = BusinessSolution::where('status',0)->get();
        if($businessData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$businessData;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No data found';
        }

        
        return $response;

    }

    public function serviceList(Request $request){
        
        $serviceData = Service::where('status',0)->get();


        if($serviceData->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $serviceData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;

    }

    public function selectPoc(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $pocData = User::where('status',0)->where('type',4)->where('poc_id',2)->get();
        if($pocData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$pocData;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No data found';
        }
        return $response;
    }

    public function products(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $productData = Product::where('status',0)->get();
        if($productData->count()>0){
            $response['status'] = 'Success';
            $response['data']   =$productData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;
    }

    public function connectnow(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
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
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }
        $fromid = $request->user_id;
        $queries = PresetQuestion::where('tech_id',$request->technology)->where('brand_id',$request->brand)->get();
        $response['data'] = [
                'tech_id' => $request->technology,
                'brand_id'=>$request->brand,
                'region_id'=>$request->region,
                'poc_user_id'=>$request->poc,
                'from_id'=>$request->user_id
            ];
        $response['status']     = 'Success';
        $response['data']       = $queries; 
        return $response;
    }

    public function sendReply(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $validator = Validator::make($request->all(),
            [
                'reply' => 'required'
            ],[
                'reply.required' => 'Please enter reply',
            ]);
        if ($validator->fails()) {
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }
        $connectId = SalesConnect::insertGetId([
            'tech_id' => $request->technology,
            'brand_id'=>$request->brand,
            'region_id'=>$request->region,
            'poc_user_id'=>$request->poc,
            'from_id'=>$request->user_id,
            'date_time'=>\Carbon\Carbon::now(),
            'status'=>2, //poc connect
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        $replyId = ReplyRequest::insertGetId([
            'req_id' => $request->request_id,
            'from_id'=>$request->user_id,
            'reply'=>$request->reply,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        if(isset($replyId)){
            $response['status']     = 'Success';
            $response['data']       = 'Recieved your request will get back soon';
            return $response;
        }else{
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }
    }

    public function scheduleMeeting(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $validator = Validator::make($request->all(),
        [
            'technology' => 'required',
            'product' => 'required',
            'date_time' => 'required'
        ],[
            'tech.required'=>'Please select technology',
            'product.required'=>'Please select product',
            'date_time.required' => 'Please enter date and time'
        ]);
        if ($validator->fails()) {
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }
        $connectId = SalesConnect::insertGetId([
            'tech_id' => $request->technology,
            'brand_id'=>$request->brand,
            'region_id'=>$request->region,
            'poc_user_id'=>$request->poc,
            'from_id'=>$request->user_id,
            'status'=>1, //schedule meeting
            'date_time'=>$request->date_time,
            'product_id'=>$request->product,
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

        ]);
        $requestId = Requests::insertGetId([
            'req_id' => $connectId,
            'from_id'=>$request->user_id,
            'type'=>"Sales_Connect", //Sales_Connect
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        ]);
        $admin = User::find(1);$user = User::find($request->user_id);
        if($requestId){
            $notificationId = Notification::insertGetId([
                'req_from' => $requestId,
                'from_id'=>$request->user_id,
                'to_id' => $admin->id,
                'type' => "Sales_Connect",
                'message' => "A meeting requested from ".$user->name,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
    
            ]);
            Requests::where('id',$requestId)->update(['notifid'=>$notificationId]);
        }
        
        if(isset($connectId)){
            $response['status']     = 'Success';
            $response['data']       = 'Meeting Scheduled';
            return $response;
        }else{
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }
    }

    public function feedback(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $validator = Validator::make($request->all(),
        [
            'feedback' => 'required'
        ],[
            'feedback.required' => 'Please add feedback'
        ]);
        if ($validator->fails()) {
            $response['status']  = 'Error';
            $response['data'] = $validator->messages()->first();
            return $response;
        }

        feedback::insert([
            'user_id'=> $request->user_id,
            'feedback' => $request->feedback
        ]);
        $response['status']     = 'Success';
        $response['data']       = 'Feedback added successfully';
        return $response;
    }

    public function terms(Request $request){
        $termsData = Description::where('page','Terms and Conditions')->get();
        if($termsData->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $termsData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;
    }

    public function help(Request $request){
        $helpData = Description::where('page','Help')->get();
        if($helpData->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $helpData;
        }else{
            $response['status'] = 'Error';
        }
        return $response;
    }

    public function journalList(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $journalData = Journal::where('status',0)->get();

        if($journalData->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $journalData;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No journals found';
        }
        return $response;

    }

    public function valuestories(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $storyData = ValueStory::where('status',0)->get();

        if($storyData->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $storyData;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No data found';
        }
        return $response;
    }

    public function eventsList(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $now_str = \Carbon\Carbon::now();
        $upcomingEvent  = Events::where('date_time', '>=', $now_str)->where('status',0)->whereRaw("find_in_set('3',access)")->get();
        $pastEvents     = Events::where('date_time', '<', $now_str)->where('status',0)->whereRaw("find_in_set('3',access)")->get();
        $eventData = Events::where('status',0)->whereRaw("find_in_set('3',access)")->get();
        if($eventData->count()>0){
            
            $response['status'] = 'Success';
            $response['pastEvents']   = $pastEvents;
            $response['upcomingEvent']   = $upcomingEvent;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No events available.';
        }
        return $response;
    }

    public function getRequests(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        
        $redeemRequest = Requests::where('from_id',$request->user_id)->where('type','Redeem_Request')->with('redeem')->without('from','notifi')->get();
        $promotionRequest = Requests::where('from_id',$request->user_id)->where('type','Promotion')->with('promotion')->without('from','notifi')->get();
        $businessRequest = Requests::where('from_id',$request->user_id)->where('type','Business_Solution')->with('business')->without('from','notifi')->get();
        $appointment = Requests::where('from_id',$request->user_id)->where('type','Sub_service')->with('subservice')->without('from','notifi')->get();
        $meetingRequest= SalesConnect::where('from_id',$request->user_id)->where('status',1)->without('from','region','user','reschedule','product','requestdata')->get();
        // $serviceRequest= SalesConnect::where('from_id',$request->user_id)->where('status',2)->without('from','region','user','reschedule','product')->get();
        $serviceRequest= SalesConnect::select('sales_connects.*','preset_questions.tech_id as Tech','preset_questions.brand_id as Brand','preset_questions.question as Question','reply_requests.from_id as Replyfromid','reply_requests.reply as reply')
        ->where('sales_connects.from_id',$request->user_id)->where('sales_connects.status',2)
        ->join('preset_questions','sales_connects.tech_id','=','preset_questions.tech_id')
        ->whereColumn('preset_questions.brand_id', '=', 'sales_connects.brand_id')
        ->join('reply_requests','preset_questions.id','=','reply_requests.req_id')
        // ->where('reply_requests.from_id', '=', $request->user_id)
        // ->groupBy('preset_questions.id')
        ->without('from','region','user','reschedule','product')->get();
        
        
        if($serviceRequest->count()>0 || $meetingRequest->count()>0 || $requestData->count()>0 ){
            
            $response['status'] = 'Success';
            $response['meeting_request']   = $meetingRequest;
            $response['service_request']   = $serviceRequest;
            $response['redeem_request']    = $redeemRequest;
            $response['promotion_request']    = $promotionRequest;
            $response['business_request']    = $businessRequest;
            $response['appointment_request']    = $appointment;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No request found';
        }
        return $response;
    }

    public function myEvents(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        // $pastEvents = EventRegister::with('pastEvents')->where('user_id',$request->user_id)->get();
        // $upcomingEvents = EventRegister::with('upcomingEvents')->where('user_id',$request->user_id)->get();
        $pastEvents = $upcomingEvents = [];
        $now_str = \Carbon\Carbon::now();
        $eventData= EventRegister::where('user_id',$request->user_id)->get();
        if($eventData->count()>0){
            foreach ($eventData as $key => $event) {
                if($event->event->date_time < $now_str){
                    $pastEvents[]       = $event;
                }else{
                    $upcomingEvents[]   = $event;
                }
            }
            $response['status'] = 'Success';
            $response['pastEvents']   = $pastEvents;
            $response['upcomingEvents']   = $upcomingEvents;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No events found';
        }
        return $response;
    }

    public function newRequest(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $admin = User::find(1);
        $user = User::find($request->user_id);
        $date_time = NULL;
        if($request->type=="Sub_service"){
            $sub = SubService::find($request->request_id);
            $date_time = \Carbon\Carbon::now();
            $message = "Appoinment for ".$sub->name." is requested from ".$user->name;
        }elseif($request->type=="Business_Solution"){
            $business = BusinessSolution::find($request->request_id);
            $date_time = $request->date_time;
            $message = "We received your meeting request. We will contact you shortly!";
        }else{
            $sales = SalesConnect::find($request->request_id);
            $date_time = \Carbon\Carbon::now();
            $message = "A meeting requested from ".$user->name;
        }
        $requestId = Requests::insertGetId([
            'req_id' => $request->request_id,
            'from_id'=>$request->user_id,
            'date_time'=> $date_time,
            'type'=>$request->type, //Sub_service,Business_Solution,Sales_Connect
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        ]);
        
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
            Requests::where('id',$requestId)->update(['notifid'=>$notificationId]);
            $response['status']     = 'Success';
            $response['data']       = 'Request Receviced';
            return $response;
        }else{
            $response['status']  = 'Error';
            $response['data'] = 'Some error occuired';
            return $response;
        }
    }

    public function myHistory(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        // $historyData = History::where('from',$request->user_id)->get();
        $meetingRequest= SalesConnect::where('from_id',$request->user_id)->where('status',1)->without('from','region','user','reschedule','product','requestdata')->get();
        // $serviceRequest= SalesConnect::where('from_id',$request->user_id)->where('status',2)->without('from','region','user','reschedule','product')->get();
        $serviceRequest= SalesConnect::select('sales_connects.*','preset_questions.tech_id as Tech','preset_questions.brand_id as Brand','preset_questions.question as Question','reply_requests.from_id as Replyfromid','reply_requests.reply as reply')
        ->where('sales_connects.from_id',$request->user_id)->where('sales_connects.status',2)
        ->join('preset_questions','sales_connects.tech_id','=','preset_questions.tech_id')
        ->whereColumn('preset_questions.brand_id', '=', 'sales_connects.brand_id')
        ->join('reply_requests','preset_questions.id','=','reply_requests.req_id')
        // ->where('reply_requests.from_id', '=', $request->user_id)
        // ->groupBy('preset_questions.question')
        ->without('from','region','user','reschedule','product')->get();
        if($meetingRequest->count()>0 || $serviceRequest->count()>0){
            
            $response['status'] = 'Success';
            $response['meeting_request']   = $meetingRequest;
            $response['service_request']   = $serviceRequest;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No history found';
        }
        return $response;
    }

    public function myNotifications(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }

        $notificationData = Notification::where('to_id',$request->user_id)->get();
        if($notificationData->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $notificationData;
        }else{
            $response['status'] = 'Error';
            $response['response'] = 'No notifications found';
        }
        return $response;

    }

    public function readNotification(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        Notification::where('id', $request->notification_id)->update(['status' => 1]);
        $response['status'] = 'Success';
        $response['data']   = 'Notification status changed';
        return $response;
    }

    public function registerEvent(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }

        $eventData  = new EventRegister();
        $eventData->user_id     = $request->user_id;
        $eventData->event_id    = $request->event_id;
        $eventData->save();

        $response['status'] = 'Success';
        $response['data']   = 'Event registered successfully';
        return $response;
    }

    public function myRewards(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $total      = Redeemdeduction::where('partner_id',$request->user_id)->first();
        $rewards    = PartnerReward::where('partner_id',$request->user_id)->without('partner')->get();
        if($rewards->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $rewards;
            $response['total']  = $total;
        }else{
            $response['status'] = 'Error';
            $response['response']   = 'No data found';
        }
        return $response;
    }

    public function redeemReward(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        
        $rewards = PartnerReward::where('partner_id',$request->user_id)->count();

        $check = Redeemdeduction::where('partner_id',$request->user_id)->first();

        if($request->amount>$check->total_reward){

            $response['status'] = 'Error';
            $response['response']   = 'There is not much points to redeem from the rewards';
            return $response;
        }
        if($rewards!=0){

            $redeemId = Redeem::insertGetId([

                'amount' => $request->amount,

                'partner_id' => $request->user_id,

                'description' => isset($request->description)?$request->description:"",

                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()

                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()

    

            ]);

            $requestId = Requests::insertGetId([
                'req_id' => $redeemId,
                'from_id'=> $request->user_id,
                'type'=>"Redeem_Request", //redeem table 
                'date_time' => \Carbon\Carbon::now(),
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
            ]);
            $admin = User::find(1);
            $user = User::find($request->user_id);
            if($requestId){
                $notificationId = Notification::insertGetId([
                    'req_from' => $requestId,
                    'from_id'=>$request->user_id,
                    'to_id' => $admin->id,
                    'type' => "Redeem_Request",
                    'message' => "Redeem requested from ".$user->name,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        
                ]);
                Requests::where('id',$requestId)->update(['notifid'=>$notificationId]);
            }
            $response['status'] = 'Success';
            $response['response']   = 'Reward redeem Successfully';
            return $response;
    

        }else{
            $response['status'] = 'Error';
            $response['response']   = 'There is no rewards for redeem,Please try again';
            return $response;
        }
    }

    Public function getPromotions(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }

        $list = Promotion::where('status',0)->get();
        
        if($list->count()>0){
            $response['status'] = 'Success';
            $response['data']   = $list;
        }else{
            $response['status'] = 'Error';
            $response['response']   = 'No promotions available';
        }
        return $response;
    }
    
    public function enrollPromotion(Request $request){
        $valid = $this->api_validation($request);

        if($valid != 1){

            $data['response'] = 'Invalid Token';

            $data['status'] = "Error";

            return response()->json($data);

        }
        $requestId = Requests::insertGetId([
            'req_id' => $request->promo_id,
            'from_id'=>$request->user_id,
            'type'=>"Promotion", //Sales_Connect
            'date_time' => \Carbon\Carbon::now(),
            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
            "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
        ]);
        $admin = User::find(1);$user = User::find($request->user_id);
        if($requestId){
            $notificationId = Notification::insertGetId([
                'req_from' => $requestId,
                'from_id'=>$request->user_id,
                'to_id' => $admin->id,
                'type' => "Promotion",
                'message' => "Promotion enroll request from ".$user->name,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
    
            ]);
        }
        $response['status'] = 'Success';
        $response['data']   = 'Enroll request submitted successfully';
        return $response;
    }
    
}
