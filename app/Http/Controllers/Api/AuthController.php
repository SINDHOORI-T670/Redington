<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\UserSpec;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required'
        ]);
        if($validator->fails()){
            $response['status']  = 'error';
            $response['message'] = $validator->messages()->first();
        }else{
            $credentials = $request->only('phone', 'password');
            if(Auth::attempt($credentials)){
                $response['status']  = 'success';
                $response['message'] = 'Logged in successfuly';
                $response['token']   = Auth::user()->api_token;
                $response['type'] = Auth::user()->type;
                $response['user_id'] = Auth::user()->id;
            }else{
                $response['status']  = 'error';
                $response['message'] = 'Invalid credentials';
            }
        }
        return response()->json($response);
    }

    public function register(Request $request){
        $validator = $this->createValidation($request);
        if ($validator->fails()) {
            $response['status']  = 'error';
            $response['message'] = $validator->messages()->first();
        }else{
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
                'phone' => $request->phoneNumber,
                'type'=>$request->type,
                'status'=>0,
                'verify_status'=>1,
                'image'=>$fileName,
                'password' => Hash::make($request->password),
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
            $user   = User::find($userId);
            $usertype = (
                ($request->type == 2) ? "Customer" :
                (($request->type == 3) ? "Partner" :
                (($request->type == 4) ? "Employee" : "User"))
                );
                $response['status']  = 'success';
                $response['message'] = $usertype.' created successfuly';
                $response['token']   = $user->api_token;
        }
        return response()->json($response);
    }

    public function createValidation($request)
    {
        if($request->type==4){
            $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'phoneNumber' => 'required|unique:users,phone',
                'email' => 'required|email|max:255|regex:/(.*)@redington\.com/i|unique:users,email',
                'poc' => 'required'
            ],[
                'name.required' => 'Please enter name',
                'phoneNumber.required' => 'Please enter phone number',
                'email.required' => 'Please enter email',
                'email.regex' => 'Domain not valid for registration(example@redington.com).',
                'poc.required' => 'Please select type'
            ]);
        }elseif($request->type==3){
            $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'phoneNumber' => 'required|unique:users,phone',
                'email' => 'required|email|max:255|unique:users,email'
            ],[
                'name.required' => 'Please enter name',
                'phoneNumber.required' => 'Please enter phone number',
                'email.required' => 'Please enter email'
            ]);
        }else{
            $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'phoneNumber' => 'required|unique:users,phone',
                'email' => 'required|email|max:255|unique:users,email'
            ],[
                'name.required' => 'Please enter name',
                'phoneNumber.required' => 'Please enter phone number',
                'email.required' => 'Please enter email address'
            ]);
        }
        return $validator;
    }

}
