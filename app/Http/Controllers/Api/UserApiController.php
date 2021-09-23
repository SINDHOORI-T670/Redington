<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $serviceData = Service::latest()->paginate(20);
        return $serviceData;
    }

    public function technologyList(Request $request){
        $technologyData = Technology::latest()->paginate(20);
        return $technologyData;
    }

    public function resourceList(Request $request){
        $resourceData = Resource::latest()->paginate(20);
        return $resourceData;
    }

    public function subresourceList($id){
        $subresourceData = SubResource::where('resource_id',$id)->latest()->paginate(20);
        return $subresourceData;
    }

    public function journalList(){
        $journalData = Journal::latest()->paginate(20);
        return $journalData;
    }

    public function subJournals($id){
        $subJournalData = ValueJournal::where('journal_id',$id)->latest()->paginate(20);
        return $subJournalData;
    }
    
    public function valuestories(){
        $storyData = ValueStory::latest()->paginate(20);
        return $storyData;
    }

    public function brands(){
        $brandData = Brand::latest()->paginate(20);
        return $brandData;
    }

    public function regions(){
        $regionData = Region::latest()->paginate(20);
        return $regionData;
    }

    public function salesconnectList(){
        $salesData = SalesConnect::latest()->paginate(20);
        return $salesData;
    }

    public function presetQuestions(){
        $QuestionData = PresetQuestion::latest()->paginate(20);
        return $QuestionData;
    }

    public function products(){
        $productData = Product::latest()->paginate(20);
        return $productData;
    }

}
