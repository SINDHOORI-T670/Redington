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

use App\Models\Region;

use App\Models\SalesConnect;

use App\Models\PresetQuestion;

use App\Models\Product;

use App\Models\MainService;

use App\Models\Events;

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

}

