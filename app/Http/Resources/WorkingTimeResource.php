<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkingTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'office_id' => $this->office_id,
            'description' => $this->description,
            'tolerance' => $this->tolerance,
            'isdefault' => $this->isdefault,
            'working_detail' => WorkingTimeDetailResouce::collection($this->working_detail)
        ];
    }
}
