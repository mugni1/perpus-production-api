<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\ThisForSuperUser;


// AUTH
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login-logout', [AuthController::class, 'login_logout_all_device']);

// Books
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'detail']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// ROLES
Route::get('/roles', [RoleController::class, 'index'])->middleware(['auth:sanctum']);
Route::middleware('auth:sanctum')->group(function () {
    // AUTH
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/logout-all', [AuthController::class, 'logout_all_device']);

    // USERS
    Route::get('/user-count', [UserController::class, 'countUser'])->middleware(ThisForSuperUser::class);
    Route::get('/users/{id}', [UserController::class, 'show'])->middleware(ThisForSuperUser::class); // cek user detail
    Route::get('/users', [UserController::class, 'index'])->middleware(ThisForSuperUser::class); // ambil semua user dan superUser
    Route::get('/users-user', [UserController::class, 'user'])->middleware(ThisForSuperUser::class); // ambil semua data user
    Route::get('/users-superUser', [UserController::class, 'superUser'])->middleware(ThisForSuperUser::class); // ambil semua data Admin
    Route::post('/users', [UserController::class, 'store'])->middleware(ThisForSuperUser::class); // buat user / superUser baru
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware(ThisForSuperUser::class); // buat user / superUser baru
    Route::delete('/users/{id}', [UserController::class, 'delete'])->middleware(ThisForSuperUser::class); // hapus user / superUser
    Route::get('/users-username', [UserController::class, 'getAll'])->middleware(ThisForSuperUser::class);

    // BOOKS
    Route::get('books-count', [BookController::class, 'count'])->middleware(ThisForSuperUser::class);
    Route::post('/books', [BookController::class, 'store'])->middleware(ThisForSuperUser::class);
    Route::put('/books/{id}', [BookController::class, 'update'])->middleware(ThisForSuperUser::class);
    Route::delete('/books/{id}', [BookController::class, 'drop'])->middleware(ThisForSuperUser::class);
    Route::get("/books-title-stock", [BookController::class, 'getAll'])->middleware(ThisForSuperUser::class); // untuk membuat order

    // CATEGORIES
    Route::get('/categories-count', [CategoryController::class, 'count'])->middleware(ThisForSuperUser::class);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware(ThisForSuperUser::class);
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware(ThisForSuperUser::class);
    Route::delete('/categories/{id}', [CategoryController::class, 'drop'])->middleware(ThisForSuperUser::class);

    // BORROWING
    // count
    Route::get("/borrowings-borrow-count", [BorrowingController::class, 'borrowCount'])->middleware(ThisForSuperUser::class);
    Route::get("/borrowings-return-count", [BorrowingController::class, 'returnCount'])->middleware(ThisForSuperUser::class);
    Route::get("/borrowings-late-count", [BorrowingController::class, 'lateCount'])->middleware(ThisForSuperUser::class);
    // for user
    Route::get("/borrowings-borrow-user", [BorrowingController::class, 'borrowUser']); // list semua Buku yang di pinjam si anggota tertentu yang telah login dengan akunnya
    Route::get("/borrowings-return-user", [BorrowingController::class, 'returnUser']); // list semua Buku yang di kembalikan si anggota tertentu yang telah login dengan akunnya

    Route::get("/borrowings", [BorrowingController::class, 'index'])->middleware(ThisForSuperUser::class); // list semua buku yang di pinjam semua user
    Route::get("/borrowings/{id}", [BorrowingController::class, 'show']); // show detail

    Route::get("/borrowings-borrow", [BorrowingController::class, 'borrowList'])->middleware((ThisForSuperUser::class)); // tampikan list buku yg di pinjam saja
    Route::get("/borrowings-return", [BorrowingController::class, 'returnList'])->middleware((ThisForSuperUser::class)); // tampikan list buku yg di kembalikan saja
    Route::get("/borrowings-late", [BorrowingController::class, 'lateList'])->middleware((ThisForSuperUser::class)); // tampikan list buku yg terlambat saja
    // BUAT PEMINJAMAN DAN PEMGEMBALIAN
    Route::post("/borrowings", [BorrowingController::class, 'borrowBook'])->middleware(ThisForSuperUser::class); // catat pinjaman buku
    Route::patch("/borrowings/{id}", [BorrowingController::class, 'returnBook'])->middleware(ThisForSuperUser::class); // catat pengembalian buku
    Route::delete("/borrowings/{id}", [BorrowingController::class, 'deleteBorrowBook'])->middleware(ThisForSuperUser::class);

    // TRANSACTION
    Route::get('/transactions-count', [TransactionController::class, 'count'])->middleware(ThisForSuperUser::class); // JUMLAH SEMUA TRANSAKSI
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->middleware(ThisForSuperUser::class); // DETAIL
    Route::get('/transactions', [TransactionController::class, 'index'])->middleware(ThisForSuperUser::class); // RIWAYAT SEMUA TRANSAKSI
    Route::get('/transactions-borrow', [TransactionController::class, 'trasnBorrow'])->middleware(ThisForSuperUser::class); // RIWAYAT PINJAM
    Route::get('/transactions-return', [TransactionController::class, 'transReturn'])->middleware(ThisForSuperUser::class); // RIWAYAT PENGEMBALIAN
    Route::get('/transactions-fine', [TransactionController::class, 'transFine'])->middleware(ThisForSuperUser::class); // RIWAYAT DENDA
    // TRANSAKSI DARI BULAN KE BULAN ||| UNTUK CHART
    Route::get('/transactions-borrow-data', [TransactionController::class, 'transBorrowDate'])->middleware(ThisForSuperUser::class);
    Route::get('/transactions-return-data', [TransactionController::class, 'transReturnDate'])->middleware(ThisForSuperUser::class);
    Route::get('/transactions-fine-data', [TransactionController::class, 'transFineDate'])->middleware(ThisForSuperUser::class);
});
