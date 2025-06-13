<?php

namespace App\Http\Resources\Api\Member;

use App\Http\Resources\Api\DivisionResource;
use App\Http\Resources\Api\OfficeResource;
use App\Http\Resources\Api\PositionResource;
use App\Trait\GlobalTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'nik' => $this->nik,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'gender_human' => genderResource($this->gender),
            'salary' => $this->salary,
            'salary_human' => $this->RupiahFormat($this->salary),
            'office' => new OfficeResource($this->office),
            'division' => new DivisionResource($this->division),
            'position' => new PositionResource($this->position),
            'token' => $this->whenNotNull($this->token),
        ];
    }
}
