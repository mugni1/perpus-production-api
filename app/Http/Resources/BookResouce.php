<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            "title" => $this->title,
            "image" => $this->image,
            "writer" => $this->writer,
            "publisher" => $this->publisher,
            "publication_date" => $this->publication_date,
            "description" => $this->description,
            "stock" => $this->stock,
            "category"=> new CategoryResource($this->category),
            'created_at' => $this->created_at->translatedFormat('l, d-F-Y H:i:s'),
            'updated_at' => $this->updated_at->translatedFormat('l, d-F-Y H:i:s'),
        ];
    }
}
