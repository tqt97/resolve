<?php

namespace App\Http\Resources;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\PositionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            // 'positions' => PositionResource::collection($this->positions),
            'positions' => PositionResource::collection($this->whenLoaded('positions')),
            'departments' => DepartmentResource::collection(
                $this->positions->pluck('department')->unique('id')
            ),
            // 'departments' => DepartmentResource::collection($this->departments),

        ];
    }
}
