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
    public function borrowCount()
    {
        $count = Borrowing::where('status', 'dipinjam')->count();
        return response(['count' => $count]);
    }
    public function returnCount()
    {
        $count = Borrowing::where('status', 'dikembalikan')->count();
        return response(['count' => $count]);
    }
    public function lateCount()
    {
        $count = Borrowing::where('status', 'terlambat')->count();
        return response(['count' => $count]);
    }

    public function index()
    {
        $borrowings = Borrowing::all();
        return BorrowingResource::collection($borrowings);
    }

    public function show($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        return new BorrowingResource($borrowing);
    }

    public function borrowList(Request $request)
    {
        $keyword = $request['keyword'];

        if (!$keyword) {
            $borrowings = Borrowing::with(['books:id,title', 'users:id,username'])
                ->select('id', 'user_id', 'book_id', 'borrow_date', 'return_date', 'status', 'daily_fine')
                ->orderBy('created_at', 'DESC')
                ->where('status', 'dipinjam')
                ->simplePaginate(15);
            return response(['data' => $borrowings]);
        }
        $borrowings = Borrowing::with(['books:id,title', 'users:id,username'])
            ->select('id', 'user_id', 'book_id', 'borrow_date', 'return_date', 'status', 'daily_fine')
            ->orderBy('created_at', 'DESC')
            ->where('status', 'dipinjam')
            ->where('id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        return response(['data' => $borrowings]);

        // return BorrowResource::collection($borrowings);
    }

    public function returnList(Request $request)
    {
        $keyword = $request['keyword'];

        if (!$keyword) {
            $borrowings = Borrowing::with(['books:id,title', 'users:id,username'])
                ->select('id', 'user_id', 'book_id', 'borrow_date', 'return_date', 'actual_return_date', 'status', 'daily_fine')
                ->orderBy('updated_at', 'DESC')
                ->where('status', 'dikembalikan')
                ->simplePaginate(15);
            return response(['data' => $borrowings]);
        }
        $borrowings = Borrowing::with(['books:id,title', 'users:id,username'])
            ->select('id', 'user_id', 'book_id', 'borrow_date', 'return_date', 'actual_return_date', 'status', 'daily_fine')
            ->orderBy('updated_at', 'DESC')
            ->where('status', 'dikembalikan')
            ->where('id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        return response(['data' => $borrowings]);
        // return BorrowingResource::collection($borrowings);
    }

    public function lateList(Request $request)
    {
        $keyword = $request['keyword'];

        if (!$keyword) {
            $borrowings = Borrowing::with(['books:id,title', 'users:id,username'])
                ->select('id', 'user_id', 'book_id', 'borrow_date', 'return_date', 'actual_return_date', 'status')
                ->orderBy('updated_at', 'DESC')
                ->where('status', 'terlambat')
                ->simplePaginate(15);
            return response(['data' => $borrowings]);
        }
        $borrowings = Borrowing::with(['books:id,title', 'users:id,username'])
            ->select('id', 'user_id', 'book_id', 'borrow_date', 'return_date', 'actual_return_date', 'status')
            ->orderBy('updated_at', 'DESC')
            ->where('status', 'terlambat')
            ->where('id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        return response(['data' => $borrowings]);
        // return BorrowingResource::collection($borrowings);
    }

    public function borrowBook(Request $request)
    {
        $request->validate([
            "user_id" => 'required|exists:users,id',
            "book_id" => 'required|exists:books,id',
            "return_date" => 'required|date|after:+2 days', //minimal 3 hari
            "daily_fine" => 'required|integer'
        ], [
            'return_date.after' => 'Tanggal pengembalian buku tidak benar atau minimal harus lebih 2 hari dari hari ini. Minimal 3 hari'
        ]);

        // data body
        $requestData = $request->only([
            "user_id",
            "book_id",
            "return_date",
            "daily_fine"
        ]);
        $borrow_date = now();
        $status = "dipinjam";

        $books = Book::findOrFail($requestData['book_id']); // ambil data buku

        // cek buku apakah ada dan jika ada
        if ($books['stock'] != 0) {
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
        return response(['message' => 'Stok Buku kosong'], 400);
    }

    public function deleteBorrowBook($borrowID)
    {
        $borrowing = Borrowing::findOrFail($borrowID);
        if ($borrowing['status'] == "dipinjam") {
            $book = Book::findOrFail($borrowing['book_id']);
            $historyTransaction = Transaction::where('borrowing_id', $borrowID)->first();
            $historyTransaction->delete();
            $borrowing->delete();
            $book->update([
                'stock' => $book->stock + 1
            ]);
            return response(['message' => 'Sukses Menghapus peminjaman']);
        }
        return response(['message' => 'Gagal Menghapus peminjaman'], 500);
    }

    public function returnBook($borrowID)
    {
        $date_now = now();
        $borrowing = Borrowing::findOrFail($borrowID);
        $book = Book::findOrFail($borrowing['book_id']);

        // Pastikan return_date dalam format tanggal Carbon
        $return_date = Carbon::parse($borrowing->return_date); //parse dulu agar bisa di cek
        $delayDays = floor($return_date->diffInHours($date_now) / 24); //bulatkan dalam satu hari penuh

        // jika terdeteksi terlambat
        if ($delayDays > 0) {
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
            return response(['message' => 'Berhasil Mengembalikan buku, Harus membayar denda Rp' . $totalFine]);
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
        return response(['message' => 'Berhasil Mengembalikan buku tepat waktu, Mantap!']);
    }

    //////////// BUKU YG DI PINJAM USER //////////////////
    public function borrowUser()
    {
        $user = Auth::user()->id;
        $borrowing = Borrowing::with('books:id,image,title,writer')->orderBy('created_at', 'DESC')->select('id', 'book_id', 'borrow_date', 'return_date', 'status', 'daily_fine')->where('user_id', $user)->where('status', 'dipinjam')->get();
        return response(['data' => $borrowing]);
    }
    ////////// BUKU YG DI KEMBALIKAN USER ///////////////
    public function returnUser()
    {
        $user = Auth::user()->id;
        $borrowing = Borrowing::with(['books:id,image,title,writer'])
            ->orderBy('updated_at', 'DESC')
            ->select('id', 'book_id', 'borrow_date', 'return_date', 'actual_return_date', 'status', 'daily_fine')
            ->where('user_id', $user)
            ->whereIn('status', ['dikembalikan', 'terlambat'])
            ->get();
        return response(['data' => $borrowing]);
    }
}
