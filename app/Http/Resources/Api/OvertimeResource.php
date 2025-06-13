<?php

namespace App\Http\Resources\Api;

use App\Traits\GlobalTrait;
use App\Traits\OvertimeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OvertimeResource extends JsonResource
{
    use GlobalTrait, OvertimeTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'description' => $this->description,
            'dates_human' => Carbon::parse($this->dates)->timezone($this->zonawaktu())->isoFormat('dddd, DD MMMM YYYY'),
            'dates' => $this->dates,
            'start' => Carbon::parse($this->start)->timezone($this->zonawaktu())->isoFormat('HH:mm'),
            'end' => $this->end ? Carbon::parse($this->end)->timezone($this->zonawaktu())->isoFormat('HH:mm') : 'Selesai',
            'overtimestart' => $this->overtimestart,
            'overtimeend' => $this->overtimeend,
            'status' => $this->OvertimeStatus($this->overtimestart, $this->overtimeend),
            'checklog' => ChecklogResource::collection($this->checklog)
        ];
    }
}
