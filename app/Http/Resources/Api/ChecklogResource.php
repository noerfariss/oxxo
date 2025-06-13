<?php

namespace App\Http\Resources\Api;

use App\Traits\CheckLogTrait;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklogResource extends JsonResource
{
    use CheckLogTrait, GlobalTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'type_label' => $this->checkLogType($this->type),
            'dates_human' => Carbon::parse($this->dates)->isoFormat('dddd, DD MMMM YYYY'),
            'dates' => $this->dates,
            'time' => Carbon::parse($this->time)->timezone($this->zonawaktu())->isoFormat('HH:mm'),
            'time_late_string' => $this->time_late_string ? $this->time_late_string : '',
            'reason' => $this->reason ? $this->reason : '',
            'address' => $this->address,
            'photo' => $this->photo ? url('/storage' . '/' . $this->photo) : '',
        ];
    }
}
