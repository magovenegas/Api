<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_name'     => $this -> title,
            'categoria'     => $this -> categoria,
            'created_at'    => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }
}
