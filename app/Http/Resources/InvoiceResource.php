<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'value' => $this->value,
            'fee' => $this->fee,
            'total' => $this->fee + $this->value,
            'status' => $this->status,
            'reference' => $this->reference,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'paid_at' => $this->paid_at,
            'manual' => $this->manual,

            'created_at_formated' => date('d/m/Y', strtotime($this->created_at)),
            'expires_at_formated' => date('d/m/Y', strtotime($this->expires_at)),
            'paid_at_formated' => date('d/m/Y', strtotime($this->paid_at)),
            'status_text' => $this->getStatusText(),
            'is_expired' => $this->getIsExpired()
        ];
    }
}
