<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function count(){
        $result = Transaction::count();
        return response(['count'=>$result]);
    }

    public function index(){
        $result = Transaction::orderBy("id", "DESC")->all();
        return TransactionResource::collection($result);
    }

    public function show($id){
        // $result = Transaction::where('id', $id)->first();
        $result = Transaction::findOrFail($id);
        return new TransactionResource($result);
        // return $result;
    }

    public function trasnBorrow(){
        $result = Transaction::orderBy("id", "DESC")->where('transaction_type', 'peminjaman')->get();
        return TransactionResource::collection($result);
    }

    public function transReturn(){
        $result = Transaction::orderBy("id", "DESC")->where('transaction_type', 'pengembalian')->get();
        return TransactionResource::collection($result);
    }

    public function transFine(){
        $result = Transaction::orderBy("id", "DESC")->where('transaction_type', 'denda')->get();
        return TransactionResource::collection($result);
    }
}