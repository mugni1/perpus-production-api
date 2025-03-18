<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function profile(){
        return new UserResource(Auth::user());
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response(['message' => 'Wrong email, please check again'], 403);
        }
        if(!Hash::check($request['password'], $user['password'])){
            return response(['message' => 'Incorrect password'], 403);
        }

        // generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // masukan token ke $user
        // $user['token'] = $token;

        // return
        return response([
            'message' => 'Login Success',
            'token' => $token,
            'data' => new UserResource($user),
        ]);
    }

    public function login_logout_all_device(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response(['message' => 'Wrong email, please check again'], 403);
        }
        if(!Hash::check($request['password'], $user['password'])){
            return response(['message' => 'Incorrect password'], 403);
        }

        // logout dari semua perangkat
        $user->tokens()->delete();

        // generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // masukan token ke $user
        // $user['token'] = $token;

        // return
        return response([
            'message' => 'Login Success',
            'token' => $token,
            'data' => new UserResource($user),
        ]);
    }

    public function logout(){
        $user = Auth::user();
        // hapus token saat ini
        $user->currentAccessToken()->delete();
        return response(['message'=>'Success logout']);
    }

    public function logout_all_device(){
        $user = Auth::user();
        // logout all devices
        $user->tokens()->delete();
        return response(['message'=>'Success logout from all device']);
    }
}