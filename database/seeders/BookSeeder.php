<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Book::truncate();

        $books = [
            [
                'title' => 'The Great Gatsby',
                'image' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/The_Great_Gatsby_cover.jpg',
                'writer' => 'F. Scott Fitzgerald',
                'publisher' => 'Charles Scribner\'s Sons',
                'publication_date' => '1925-04-10',
                'description' => 'The Great Gatsby is a 1925 novel written by F. Scott Fitzgerald.',
                'stock' => 10,
                'category_id' => 1,
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'image' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/To_Kill_a_Mockingbird_cover.jpg',
                'writer' => 'Harper Lee',
                'publisher' => 'J. B. Lippincott & Co.',
                'publication_date' => '1960-07-11',
                'description' => 'To Kill a Mockingbird is a novel by Harper Lee published in 1960.',
                'stock' => 5,
                'category_id' => 1,
            ],
            [
                'title' => 'Pride and Prejudice',
                'image' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Pride_and_Prejudice_cover.jpg',
                'writer' => 'Jane Austen',
                'publisher' => 'Penguin Classics',
                'publication_date' => '1813-01-28',
                'description' => 'Pride and Prejudice is a novel by Jane Austen published in 1813.',
                'stock' => 8,
                'category_id' => 1,
            ]
        ];

        collect($books)->each(function ($book) {
            Book::create($book);
        });
    }
}
