<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function count(){
        $result = Category::count();
        return response(['count' => $result]);
    }
    public function index(){
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    public function show($id){
        $result = Category::findOrFail($id);
        return new CategoryResource($result);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:30'
        ]);
        $result = Category::create([
            'name' => $request['name']
        ]);
        return new CategoryResource($result);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:30'
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request['name'],
        ]);
        return response(['message'=>'success update']);
    }

    public function drop($id){
        $result = Category::findOrFail($id);
        $result->delete();

        return response(['message'=>'success delete']);
    }
}