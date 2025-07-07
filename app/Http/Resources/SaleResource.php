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
            'sale_id' => $this->sale_id,
            'g_number' => $this->g_number,
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'total_price' => $this->total_price,
            'discount_percent' => $this->discount_percent,
            'is_supply' => $this->is_supply,
            'is_realization' => $this->is_realization,
            'promo_code_discount' => $this->promo_code_discount,
            'warehouse_name' => $this->warehouse_name,
            'country_name' => $this->country_name,
            'oblast_okrug_name' => $this->oblast_okrug_name,
            'region_name' => $this->region_name,
            'income_id' => $this->income_id,
            'odid' => $this->odid,
            'spp' => $this->spp,
            'for_pay' => $this->for_pay,
            'finished_price' => $this->finished_price,
            'price_with_disc' => $this->price_with_disc,
            'nm_id' => $this->nm_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'brand' => $this->brand,
            'is_storno' => $this->is_storno,
        ];
    }
}
