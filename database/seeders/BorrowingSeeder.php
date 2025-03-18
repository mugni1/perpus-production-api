<?php

namespace Database\Seeders;

use App\Models\Borrowing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Borrowing::truncate();
        Schema::enableForeignKeyConstraints();

        $borrowings =[
            ['user_id'=>1,'book_id'=>1,'borrow_date'=>"2025-03-03",'return_date'=>"2025-03-05",'status'=>"dipinjam",'daily_fine'=>5000],
        ];

        collect($borrowings)->map(function ($borrowing){
            Borrowing::create($borrowing);
        });
    }
}