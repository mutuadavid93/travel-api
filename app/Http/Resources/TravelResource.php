<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// HINT: instruct larastan to understand what $this object is

/**
 * @mixin \App\Models\Travel
 * @property int $number_of_nights
 */

class TravelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Payload
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "description" => $this->description,
            "number_of_days" => $this->number_of_days,

            // NOTE: Invoke Eloquent Accessor implicitly
            "number_of_nights" => $this->number_of_nights,
        ];
    }
}
