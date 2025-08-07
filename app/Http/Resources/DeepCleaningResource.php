<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeepCleaningResource extends JsonResource
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
            'chalet_id' => $this->chalet_id,
            'cleaner_id' => $this->cleaner_id,
            'cleaning_date' => $this->cleaning_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'cleaning_type' => $this->cleaning_type,
            'notes' => $this->notes,
            'status' => $this->status,
            'chalet' => new ChaletResource($this->whenLoaded('chalet')),
            'cleaner' => new CleanerResource($this->whenLoaded('cleaner')),
            'images' => $this->whenLoaded('images'),
            'videos' => $this->whenLoaded('videos'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
