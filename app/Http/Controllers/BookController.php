<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResouce;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request['keyword'];
        $books = Book::simplePaginate(1);
        if ($keyword) {
            $books = Book::where('title', 'LIKE', '%' . $keyword . '%')->simplePaginate(2);
        }
        return BookResouce::collection($books);
    }

    public function count()
    {
        $results = Book::count();
        return response(['count' => $results]);
    }

    public function detail($id)
    {
        $results = Book::findOrFail($id);
        return new BookResouce($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100|string',
            'image' => 'required|mimes:png,jpg,jpeg,jfif,avif,webp',
            'writer' => 'required|max:50|string',
            'publisher' => 'required|max:50|string',
            'publication_date' => 'date|required',
            'description' => 'required',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id|integer',
        ]);

        $image_name = $request->file('image')->getClientOriginalName();
        $time_now = now()->translatedFormat('His');
        $new_name_image = strtolower(str_replace(" ", "_", $time_now . $image_name));

        $results = Book::create([
            'title' => $request['title'],
            'image' => $new_name_image,
            'writer' => $request['writer'],
            'publisher' => $request['publisher'],
            'publication_date' => $request['publication_date'],
            'description' => $request['description'],
            'stock' => $request['stock'],
            'category_id' => $request['category_id'],
        ]);

        if ($results) {
            // simpan ke folder storage/image
            $request->file('image')->storeAs('images', $new_name_image);
        }

        return new BookResouce($results);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:100|string',
            'image' => 'mimes:png,jpg,jpeg,jfif,avif,webp',
            'writer' => 'required|max:50|string',
            'publisher' => 'required|max:50|string',
            'publication_date' => 'date|required',
            'description' => 'required',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id|integer',
        ]);

        $book = Book::findOrFail($id);
        $book_image = $book['image'];

        if ($request->file('image')) {
            $PathImage = 'images/' . $book_image;
            if (Storage::disk('public')->exists($PathImage)) {
                Storage::disk('public')->delete($PathImage);
            }
            $image_name = $request->file('image')->getClientOriginalName();
            $time_now = now()->translatedFormat('His');
            $book_image = strtolower(str_replace(" ", "_", $time_now . $image_name));
            // simpan ke folder storage/image
            $request->file('image')->storeAs('images', $book_image);
        }

        $book->update([
            'title' => $request['title'],
            'image' => $book_image,
            'writer' => $request['writer'],
            'publisher' => $request['publisher'],
            'publication_date' => $request['publication_date'],
            'description' => $request['description'],
            'stock' => $request['stock'],
            'category_id' => $request['category_id'],
        ]);

        return response(['message' => "seuccss update the book"]);
    }

    public function drop($id)
    {
        $book = Book::findOrFail($id);
        $bookTitle = $book['title'];
        $filePath = 'images/' . $book['image']; // ambil gambar dari database
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $book->delete();
        return response()->json(['message' => 'Success delete books ' . $bookTitle]);
    }
}
