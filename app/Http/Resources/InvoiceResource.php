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
            'added' => $this->added,
            'total' => $this->fee + $this->value + $this->added,
            'status' => $this->status,
            'reference' => $this->reference,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'paid_at' => $this->paid_at,
            'closed_at' => $this->closed_at,
            'manual' => $this->manual,
            'method' => !empty($this->paymentMethod) ? $this->paymentMethod->name : null,
            'receipt' => !empty($this->receipt) ? "/storage/".$this->receipt : null,
            'invoice_adds' => $this->invoiceAdds,

            'created_at_formated' => date('d/m/Y', strtotime($this->created_at)),
            'expires_at_formated' => date('d/m/Y', strtotime($this->expires_at)),
            'paid_at_formated' => !empty($this->paid_at) ? date('d/m/Y', strtotime($this->paid_at)) : null,
            'closed_at_formated' => !empty($this->closed_at) ? date('d/m/Y', strtotime($this->closed_at)) : null,
            'status_text' => $this->getStatusText(),
            'is_expired' => $this->getIsExpired()
        ];
    }
}
