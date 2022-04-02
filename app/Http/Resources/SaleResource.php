<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'id_unit' => $this->id_unit,
            'description' => $this->description,
            'color' => $this->color,
            'size' => $this->size,
            'payment_method' => $this->payment_method,
            'paid_at' => date('Y-m-d', strtotime($this->paid_at)),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'price' => $this->price,
            'paid_price' => $this->paid_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'formated_price' => number_format($this->price, 2, ',', '.'),
            'formated_paid_price' => number_format($this->paid_price, 2, ',', '.'),

            'student' => $this->student,
            'unit' => $this->unit,
        ];
    }
}
