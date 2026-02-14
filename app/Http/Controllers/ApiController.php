<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmercategory;
use App\Models\Farmersubcategory;
use App\Models\User;
use App\Models\Farmerslider;
use Validator;
use Auth;

class ApiController extends Controller
{

	public function categories(Request $request)
	{
	    try {

	        $query = Farmercategory::query();

	        if ($request->has('search') && !empty($request->search)) {
	            $search = $request->search;
	            $query->where('category_name', 'LIKE', "%{$search}%");
	        }

	        $query->where('status', 'Active');


	        $query->with(['farmersubcategories' => function ($q) {
	            $q->where('status', 'Active');
	        }]);

	        if ($request->is_paginate == 1) {

	            $per_page = $request->per_page ?? 10;

	            $data = $query->latest()->paginate($per_page);

	        } else {

	            $data = $query->latest()->get();
	        }

	        return response()->json([
	            'status' => true,
	            'data'   => $data
	        ]);

	    } catch (Exception $e) {

	        return response()->json([
	            'status'  => false,
	            'code'    => $e->getCode(),
	            'message' => $e->getMessage()
	        ], 500);
	    }
	}


	public function userSignup(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:50',
                'phone' => 'required|string',
                'email' => 'nullable|email',
                'address' => 'nullable|string',
                'password' => 'required|string',
                'confirm_password' => 'required|string|same:password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = new User();

            if($request->has('email')){
                $countEmail = User::where('email',$request->email)->count();
                if($countEmail > 0){
                    return response()->json(['status'=>false, 'message'=>"Already the email has been taken", "data"=>new \stdClass()],400);
                }
            }

            if($request->has('phone')){
                $countPhone = User::where('phone',$request->phone)->count();
                if($countPhone > 0){
                    return response()->json(['status'=>false, 'message'=>"Already the phone has been taken", "data"=>new \stdClass()],400);
                }
            }

            $user = new User();
            $user->role = 'user';
            $user->full_name = $request->full_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->address = $request->address;
            $user->save();

            return response()->json(['status'=>true, 'message'=>"Successfully Signup", "data"=>$user],201);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userSignin(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $login = $request->input('login');
            $password = $request->input('password');

            $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            $user = User::where('email',$login)->orWhere('phone',$login)->first();
            
            if($user->status == 'Inactive'){
                return response()->json(['status'=>false, 'message'=>'Sorry you are not active user', 'token'=>"", 'user'=>new \stdClass()],403);
            }

            if (Auth::attempt([$fieldType => $login, 'password' => $password])) {
                $token = $user->createToken('MyApp')->plainTextToken;
                return response()->json(['status'=>true,'message'=>'Successfully Logged IN', 'token'=>$token, 'user'=>$user]);
            }

            return response()->json(['status'=>false,'message'=>"Invalid Email/Phone or Password", 'token'=>"", 'user'=>new \stdClass()],401);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userSignOut(Request $request)
    {
        try
        {
            auth()->user()->tokens()->delete();
            return response()->json(['status'=>true, 'message'=>'Successfully Logged Out']);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function sliders()
    {
        try
        {
            $sliders = Farmerslider::latest()->get();
            return response()->json(['status'=>count($sliders) > 0, 'data'=>$sliders]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

}
