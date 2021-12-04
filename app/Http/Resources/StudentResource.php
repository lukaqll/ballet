<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'id_user' => $this->id_user,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'birthdate' => $this->birthdate,
            'picture' => !empty($this->picture) ? '/storage/'.$this->picture : null,
            'birthdate_formated' => date('d/m/Y', strtotime($this->birthdate)),
            'classes' => ClassResource::collection($this->classes),
        ];
    }
}
