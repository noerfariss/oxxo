<?php

namespace App\Http\Resources\Api;

use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditTenorResource extends JsonResource
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
            'nominal_human' => $this->RupiahFormat($this->nominal),
            'nominal' => $this->nominal,
            'status' => $this->status,
            'dates' => $this->dates,
            'dates_human' => Carbon::parse($this->dates)->isoFormat('DD MMMM YYYY'),
        ];
    }
}
