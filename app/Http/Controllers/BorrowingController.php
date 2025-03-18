<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\BorrowingResource;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    public function borrowCount(){
        $count = Borrowing::where('status', 'dipinjam')->count();
        return response(['count' => $count]);
    }
    public function returnCount(){
        $count = Borrowing::where('status', 'dikembalikan')->count();
        return response(['count' => $count]);
    }
    public function lateCount(){
        $count = Borrowing::where('status', 'terlambat')->count();
        return response(['count' => $count]);
    }

    public function index(){
        $borrowings = Borrowing::all();
        // return jika stock buku kosong
        return BorrowingResource::collection($borrowings);
    }

    public function borrowList(){
        $borrowings = Borrowing::orderBy('id', 'DESC')->where('status','dipinjam')->get();
        return BorrowingResource::collection($borrowings);
    }

    public function returnList(){
        $borrowings = Borrowing::orderBy('id', 'DESC')->where('status','dikembalikan')->get();
        return BorrowingResource::collection($borrowings);
    }

    public function lateList(){
        $borrowings = Borrowing::orderBy('id', 'DESC')->where('status','terlambat')->get();
        return BorrowingResource::collection($borrowings);
    }

    public function borrowBook(Request $request) {
        $request->validate([
            "user_id" => 'required|exists:users,id',
            "book_id" => 'required|exists:books,id',
            "return_date" => 'required|date|after:today',
            "daily_fine" => 'required|integer'
        ],[
            'return_date.after' => 'Tanggal pengembalian buku tidak benar atau minimal harus lebih 1 hari dari hari ini.'
        ]);

        $requestData = $request->only([
            "user_id",
            "book_id",
            "return_date",
            "daily_fine"
        ]);

        $borrow_date = now();
        $status = "dipinjam";

        $books = Book::findOrFail($requestData['book_id']);

        // cek buku apakah ada
        if($books['stock'] != 0){
            $result = Borrowing::create([
                'user_id' => $requestData['user_id'],
                'book_id' => $requestData['book_id'],
                'borrow_date' => $borrow_date,
                'return_date' => $requestData['return_date'],
                'actual_return_date' => null,
                'status' => $status,
                'daily_fine' => $requestData['daily_fine']
            ]);

            $books->update([
                'stock' => $books->stock - 1
            ]);

            Transaction::create([
                'borrowing_id' => $result['id'],
                'transaction_type' => 'peminjaman',
                'amount' => 0
            ]);
            return new BorrowingResource($result);
        }
        // return jika stock buku kosong
        return response(['message'=>'stock book is zero'], 400);
    }

    public function returnBook($borrowID){
        $date_now = now();
        $borrowing = Borrowing::findOrFail($borrowID);
        $book = Book::findOrFail($borrowing['book_id']);

        // Pastikan return_date dalam format tanggal Carbon
        $return_date = Carbon::parse($borrowing->return_date); //parse dulu agar bisa di cek
        $delayDays =ceil($return_date->diffInHours($date_now) / 24); //bulatkan dalam satu hari penuh

        // jika terdeteksi terlambat
        if($delayDays > 0){
            $book->update([
                'stock' => $book['stock'] + 1,
            ]);
            $borrowing->update([
                'actual_return_date' => $date_now,
                'status' => 'terlambat',
            ]);
            //hitung denda
            $totalFine = $borrowing['daily_fine'] * $delayDays;
            //simpan hasil nya di transaction
            Transaction::create([
                'borrowing_id' => $borrowing['id'],
                'transaction_type' => 'denda',
                'amount' => $totalFine,
            ]);
            return response(['message'=>'Success return the book and pay the fine']);
        }
        $book->update([
            'stock' => $book['stock'] + 1,
        ]);
        $borrowing->update([
            'actual_return_date' => $date_now,
            'status' => 'dikembalikan',
        ]);
        Transaction::create([
            'borrowing_id' => $borrowing['id'],
            'transaction_type' => 'pengembalian',
            'amount' => 0,
        ]);
        return response(['message'=>'Success return the book, Nice']);
    }

     //////////// BUKU YG DI PINJAM USER //////////////////
    public function borrowUser(){
        $user = Auth::user()->id;
        $borrowing = Borrowing::orderBy('id', 'DESC')->where('user_id',$user)->where('status', 'dipinjam')->get();
        return BorrowingResource::collection($borrowing);
    }

    public function returnUser(){
        $user = Auth::user()->id;
        $borrowing = Borrowing::orderBy('id', 'DESC')->where('user_id', $user)->whereIn('status', ['dikembalikan', 'terlambat'])->get();
        return BorrowingResource::collection($borrowing);
    }

}