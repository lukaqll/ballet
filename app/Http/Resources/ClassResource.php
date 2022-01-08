<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'id_unit' => $this->id_unit,
            'name' => $this->name,
            'value' => $this->value,
            'unit_name' => $this->unit->name,
            'times' => ClassTimeResource::collection($this->times),
            'students_count' => count($this->students)
        ];
    }
}
