<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\BookResouce;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => new UserResource($this->users),
            "book" => new BookResouce($this->books),
            "borrow_date"=>$this->borrow_date,
            "return_date"=>$this->return_date,
            "actual_return_date"=>$this->actual_return_date,
            "status"=>$this->status,
            "daily_fine"=>$this->daily_fine,
        ];
    }
}
