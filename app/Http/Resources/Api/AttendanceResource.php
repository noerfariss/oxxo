<?php

namespace App\Http\Resources\Api;

use App\Traits\ApiPhoto;
use App\Traits\AttendanceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    use ApiPhoto, AttendanceTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'dates' => Carbon::parse($this->dates)->isoFormat('YYYY-MM-DD'),
            'dates_human' => Carbon::parse($this->dates)->isoFormat('dddd, DD MMMM YYYY'),
            'description' => $this->description,
            'type' => $this->AttendanceType($this->type),
            'photo' => $this->showPhoto($this->photo),
        ];
    }
}
