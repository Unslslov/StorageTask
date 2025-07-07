<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'g_number' => $this->g_number,
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'total_price' => (float)$this->total_price,
            'discount_percent' => (float)$this->discount_percent,
            'warehouse' => $this->warehouse_name,
            'region' => $this->oblast,
            'product_id' => $this->nm_id,
            'category' => $this->category,
            'brand' => $this->brand,
            'status' => $this->is_cancel ? 'cancelled' : 'completed',
            'cancellation_date' => $this->cancel_dt,
        ];
    }
}
