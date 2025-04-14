<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function count()
    {
        $result = Transaction::count();
        return response(['count' => $result]);
    }

    public function index(Request $request)
    {
        $keyword = $request['keyword'];
        if (!$keyword) {
            $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
                ->orderByDesc("created_at")
                ->with([
                    'borrowings:id,user_id,book_id',
                    'borrowings.books:id,title',
                    'borrowings.users:id,username'
                ])
                ->simplePaginate(15);
            return response(['data' => $result]);
        }
        $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
            ->orderByDesc("created_at")
            ->with([
                'borrowings:id,user_id,book_id',
                'borrowings.books:id,title',
                'borrowings.users:id,username'
            ])
            ->where('borrowing_id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        // return TransactionResource::collection($result);
        return response(['data' => $result]);
    }

    public function show($id)
    {
        // $result = Transaction::where('id', $id)->first();
        $result = Transaction::findOrFail($id);
        return new TransactionResource($result);
        // return $result;
    }

    public function trasnBorrow(Request $request)
    {
        $keyword = $request['keyword'];
        if (!$keyword) {
            $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
                ->orderByDesc("created_at")
                ->where('transaction_type', 'peminjaman')
                ->with([
                    'borrowings:id,user_id,book_id',
                    'borrowings.books:id,title',
                    'borrowings.users:id,username'
                ])
                ->simplePaginate(15);
            return response(['data' => $result]);
        }
        $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
            ->orderByDesc("created_at")
            ->where('transaction_type', 'peminjaman')
            ->with([
                'borrowings:id,user_id,book_id',
                'borrowings.books:id,title',
                'borrowings.users:id,username'
            ])
            ->where('borrowing_id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        return response(['data' => $result]);
    }

    public function transReturn(Request $request)
    {
        $keyword = $request['keyword'];
        if (!$keyword) {
            $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
                ->orderByDesc("created_at")
                ->where('transaction_type', 'pengembalian')
                ->with([
                    'borrowings:id,user_id,book_id',
                    'borrowings.books:id,title',
                    'borrowings.users:id,username'
                ])
                ->simplePaginate(15);
            // return TransactionResource::collection($result);
            return response(['data' => $result]);
        }
        $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
            ->orderByDesc("created_at")
            ->where('transaction_type', 'pengembalian')
            ->with([
                'borrowings:id,user_id,book_id',
                'borrowings.books:id,title',
                'borrowings.users:id,username'
            ])
            ->where('borrowing_id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        // return TransactionResource::collection($result);
        return response(['data' => $result]);
    }

    public function transFine(Request $request)
    {
        $keyword = $request['keyword'];

        if (!$keyword) {
            $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
                ->orderByDesc("created_at")
                ->where('transaction_type', 'denda')
                ->with([
                    'borrowings:id,user_id,book_id',
                    'borrowings.books:id,title',
                    'borrowings.users:id,username'
                ])
                ->simplePaginate(15);
            // return TransactionResource::collection($result);
            return response(['data' => $result]);
        }
        $result = Transaction::select('id', 'borrowing_id', 'transaction_type', 'amount', 'created_at')
            ->orderByDesc("created_at")
            ->where('transaction_type', 'denda')
            ->with([
                'borrowings:id,user_id,book_id',
                'borrowings.books:id,title',
                'borrowings.users:id,username'
            ])
            ->where('borrowing_id', 'like', '%' . $keyword . '%')
            ->simplePaginate(15);
        // return TransactionResource::collection($result);
        return response(['data' => $result]);
    }

    public function transBorrowDate(Request $request)
    {
        $year = now()->year;
        if ($request['year']) {
            $year = $request['year'];
        }

        $result = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('transaction_type', 'peminjaman')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = ["peminjaman" => $result[$i] ?? 0];
        }

        return $chartData;
    }
    public function transReturnDate(Request $request)
    {
        $year = now()->year;
        if ($request['year']) {
            $year = $request['year'];
        }

        $result = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('transaction_type', 'pengembalian')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = ["pengembalian" => $result[$i] ?? 0];
        }

        return $chartData;
    }
    public function transFineDate(Request $request)
    {
        $year = now()->year;
        if ($request['year']) {
            $year = $request['year'];
        }

        $result = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('transaction_type', 'denda')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = ["denda" => $result[$i] ?? 0];
        }

        return $chartData;
    }
}
