<?php

namespace App\Http\Resources\Api;

use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
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
            'dates' => Carbon::parse($this->created_at)->isoFormat('YYYY-MM-DD'),
            'dates_human' => Carbon::parse($this->created_at)->isoFormat('dddd, DD MMMM YYYY'),
            'nominal_human' => $this->RupiahFormat($this->nominal),
            'nominal' => $this->nominal,
            'tenor' => $this->id ? CreditTenorResource::collection($this->tenors) : $this->tenor,
            'description' => $this->description,
            'admin_approved' => $this->admin_approved,
            'manager_approved' => $this->manager_approved,
            'is_accepted' => $this->is_accepted,
            'created_at' => $this->created_at
        ];
    }
}
