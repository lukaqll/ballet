<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentClassResource extends JsonResource
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
            'id_class' => $this->id_class,
            'id_student' => $this->id_student,
            'approved_at' => $this->approved_at,
            'approved_at_format' => date('d/m/Y', strtotime($this->approved_at)),
            'class' => $this->class,
            'unit_name' => $this->class->unit->name,
            'contract' => $this->contract
        ];
    }
}
