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
            default:
                $data['response'] = 'Invalid OP';
                $data['status'] = "Error";
        }
        return response()->json($data);
    }

    public function getProfile(Request $request){
        $userData   = User::with('userSpec')->where('id',$request->user_id)->first();
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
}
