<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    public function countUser()
    {
        $results = User::where('role_id', 2)->count();
        return response(['count' => $results]);
    }

    public function user()
    {
        $user = User::where('role_id', 2)->simplePaginate(1);
        return UserResource::collection($user);
    }

    public function superUser(Request $request)
    {
        $userLoginID = Auth::user()->id;
        $keyword = $request->query("keyword");

        if (!$keyword) {
            $user = User::whereNot('id', $userLoginID)->where('role_id', 1)->simplePaginate(1);
            return UserResource::collection($user);
        }
        $user = User::whereNot('id', $userLoginID)
            ->where('role_id', 1)
            ->where('full_name', 'like', '%' . $keyword . '%')
            ->simplePaginate(1);
        return UserResource::collection($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:30',
            'username' => 'required|string|max:10|unique:users,username,NULL,id,deleted_at,NULL',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);
        $result = User::create($request->all());
        return new UserResource($result);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response(['message' => 'success delete user']);
    }
}
