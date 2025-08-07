<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'price' => $this->price,
            'status' => $this->status,
            'minimum_quantity' => $this->minimum_quantity,
            'supplier' => $this->supplier,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
