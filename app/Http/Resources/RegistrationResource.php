<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'phone' => $this->phone,
            'is_whatsapp' => $this->is_whatsapp,
            'status' => $this->status,
            'picture' =>  !empty($this->picture) ? '/storage/'.$this->picture : null,
            'signer_key' => $this->signer_key,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_format' => date('d/m/Y', strtotime($this->created_at)),

            'uf' => $this->uf,
            'city' => $this->city,
            'district' => $this->district,
            'street' => $this->street,
            'address_number' => $this->address_number,
            'address_complement' => $this->address_complement,

            'instagram' => $this->instagram,
            'rg' => $this->rg,
            'orgao_exp' => $this->orgao_exp,
            'uf_orgao_exp' => $this->uf_orgao_exp,
            'profession' => $this->profession,
            'birthdate' => $this->birthdate,
            'cep' => $this->cep,

            'student' => new StudentResource($this->pendentStudent),
            'register_files' => RegisterFileResource::collection($this->RegisterFiles),
            'know_by' => $this->know_by,
        ];
    }
}
