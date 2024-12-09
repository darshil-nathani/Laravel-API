<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function signUp(request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'name' => 'required',
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => "validation error",
                'erroes' => $validateUser->errors()->all()
            ],401);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
        ]);

            return response()->json([
                'status' => true,
                'message' => "user create sucessfully",
                'user' => $user,
            ],200);
    }

    public function login(request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => "validation error",
                'errors' => $validateUser->errors()->all()
            ],401);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::User();
            return response()->json([
                'status' => true,
                'message' => "Login successful",
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'token_type' => 'bearer'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Email and password do not match",
            ], 401);
        }
    }

    public function logOut(request $request){
        $user = $request->User();
        $user->tokens()->delete();

        return response()->json([
            'message' => "Log out Sucessfull",
        ],200);
    }

    public function getUser(){
        $user = User::all();
        return response()->json([
            'status' => true,
            "user"=>$user,
        ],200);
    }
}
