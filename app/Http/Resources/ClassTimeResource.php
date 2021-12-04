<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassTimeResource extends JsonResource
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
            'weeday' => $this->weeday,
            'weeday_text' => $this->getWeekdayText(),
            'start_at' => date('H:i', strtotime($this->start_at)),
            'end_at' => date('H:i', strtotime($this->end_at)),
        ];
    }
}
