<?php

namespace App\Models;

use App\Models\Borrowing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = ['borrowing_id','transaction_type','amount'];

    /**
     * Get the borrowings that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function borrowings()
    {
        return $this->belongsTo(Borrowing::class, 'borrowing_id', 'id');
    }
}