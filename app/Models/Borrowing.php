<?php

namespace App\Models;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrowing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'return_date',
        'actual_return_date',
        'status',
        'daily_fine',
    ];

    /**
     * Get the user that owns the Borrowing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the user that owns the Borrowing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function books()
    {
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }
}