<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return UserResource::collection($users);
    }
    public function getAll()
    {
        $user = User::select(['id', 'username'])->where('role_id', 2)->get();
        return response(['data' => $user]);
    }

    public function countUser()
    {
        $results = User::where('role_id', 2)->count();
        return response(['count' => $results]);
    }

    public function user(Request $request)
    {
        $keyword = $request->query("keyword");
        if (!$keyword) {
            $user = User::where('role_id', 2)->simplePaginate(20);
            return UserResource::collection($user);
        }
        $user = User::where('role_id', 2)
            ->where('full_name', 'like', '%' . $keyword . '%')
            ->simplePaginate(20);
        return UserResource::collection($user);
    }

    public function superUser(Request $request)
    {
        $userLoginID = Auth::user()->id;
        $keyword = $request->query("keyword");

        if (!$keyword) {
            $user = User::whereNot('id', $userLoginID)->where('role_id', 1)->simplePaginate(20);
            return UserResource::collection($user);
        }
        $user = User::whereNot('id', $userLoginID)
            ->where('role_id', 1)
            ->where('full_name', 'like', '%' . $keyword . '%')
            ->simplePaginate(20);
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
        if ($result) {
            return response(['data' => "Berhasil menambah user"], 201);
        }
        return response(['data' => "Gagal menambah user"], 403);
    }
    public function update($id, Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:30',
            'username' => 'required|string|max:10|unique:users,username,' . $id . ',id,deleted_at,NULL',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'password' => 'nullable',
        ]);

        $user = User::findOrFail($id);

        if (!$request['password']) {
            $user->update([
                'full_name' => $request['full_name'],
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => $user['password'],
                'role_id' => $user['role_id'],
            ]);
            return response(['data' => "Berhasil mengedit user tanpa mengubah pasword"]);
        }
        $user->update([
            'full_name' => $request['full_name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role_id' => $user['role_id'],
        ]);
        return response(['data' => "Berhasil mengedit user dan mengubah password"]);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response(['message' => 'success delete user']);
    }
}
