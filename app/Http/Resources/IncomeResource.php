<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
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
            'income_id' => $this->income_id,
            'number' => $this->number,
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'date_close' => $this->date_close,
            'warehouse_name' => $this->warehouse_name,
            'nm_id' => $this->nm_id,
        ];
    }
}
