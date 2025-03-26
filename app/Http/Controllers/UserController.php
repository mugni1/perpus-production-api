<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index(){
        $users =User::all();
        return UserResource::collection($users);
    }

    public function countUser(){
        $results = User::where('role_id', 2)->count();
        return response(['count'=>$results]);
    }
    public function user(){
        $user = User::where('role_id', 2)->simplePaginate(20);
        return UserResource::collection($user);
    }

    public function superUser(){
        $user = User::where('role_id', 1)->get();
        return UserResource::collection($user);
    }

    public function show($id){
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function store(Request $request){
        $request->validate([
            'full_name' => 'required|string|max:30',
            'username' => 'required|string|max:10|unique:users,username',
            'email'=> 'required|email|unique:users,email',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);

        $result = User::create($request->all());

        return new UserResource($result);
    }

    public function delete($id){
        $user = User::findOrFail($id);
        $user->delete();

        return response(['message'=> 'success delete user']);
    }
}