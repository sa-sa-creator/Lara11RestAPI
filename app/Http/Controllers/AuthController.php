<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    public function register(Request $request){
        //validation
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:50',
            'email' => 'required|string|unique:users|email',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }
        try {
            //create
           $user = User::create([
                'name' =>$request->name,
                'email' =>$request->email,
                'password' =>bcrypt($request->password)
            ]);
            //create user token
            $token = $user->createToken('register_token')->plainTextToken;
            return response()->json([
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ],200);
        } catch (Exception $e) {
            return response()->json([
                'massage' => $e->getMessage()
            ],500);
        }
    }
    public function login(Request $request){
        //validation
        $credentails = $request->only('email','password');
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }
        //find user
        $user = User::where('email',$request->email)->first();

        if(!Auth::attempt($credentails)){
            if(!$user || !Hash::check($request['password'],$user->password)){
                return response()->json([
                    'massage' => 'Invalid credentials'
                ],401);
            }
        }
        //create user token
        $token = $user->createToken('login_token')->plainTextToken;
        return response()->json([
            'massage' => 'Login success',
            'data_user'=> $request->user(),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ],200);
    }
    public function logout(){
        //delete user token
        Auth::user()->tokens()->delete();
        return response()->json([
            'massage' => 'Logout success',
        ],200);

    }
}
