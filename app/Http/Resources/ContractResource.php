<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'id_student' => $this->id_student,
            'key' => $this->key,
            'path' => $this->path,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'created_at_format' => date('d/m/Y', strtotime($this->created_at)),
            'status_text' => $this->getStatusText(),
            'student' => $this->student,
            'user' => $this->student->user,
        ];
    }
}
