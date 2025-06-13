<?php

namespace App\Http\Resources\Api;

use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    use GlobalTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'dates_human' => Carbon::parse($this->dates)->timezone($this->zonawaktu())->isoFormat('dddd, DD MMMM YYYY'),
            'dates' => $this->dates,
            'nominal' => $this->nominal,
            'nominal_human' => $this->RupiahFormat($this->nominal),
            'admin_approved' => $this->admin_approved,
            'manager_approved'  => $this->manager_approved,
            'is_accepted' => $this->is_accepted,
        ];
    }
}
