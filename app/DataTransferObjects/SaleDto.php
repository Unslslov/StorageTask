<?php

namespace App\DataTransferObjects;

class SaleDto
{
    public function __construct(
        public ?string $sale_id,
        public ?string $g_number,
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?string $barcode,
        public float $total_price,
        public float $discount_percent,
        public bool $is_supply,
        public bool $is_realization,
        public ?float $promo_code_discount,
        public ?string $warehouse_name,
        public ?string $country_name,
        public ?string $oblast_okrug_name,
        public ?string $region_name,
        public ?int $income_id,
        public ?int $odid,
        public float $spp,
        public float $for_pay,
        public float $finished_price,
        public float $price_with_disc,
        public int $nm_id,
        public ?string $subject,
        public ?string $category,
        public ?string $brand,
        public ?int $is_storno,
        public int $account_id,
    ) {
    }

    /**
     * Создаёт DTO из массива данных от API
     */
    public static function fromArray(array $data, int $accountId): self
    {
        return new self(
            sale_id: $data['sale_id'] ?? null,
            g_number: $data['g_number'] ?? null,
            date: $data['date'] ?? null,
            last_change_date: $data['last_change_date'] ?? null,
            supplier_article: $data['supplier_article'] ?? null,
            tech_size: $data['tech_size'] ?? null,
            barcode: isset($data['barcode']) ? (string)$data['barcode'] : null,
            total_price: isset($data['total_price']) ? round((float)$data['total_price'], 2) : 0.00,
            discount_percent: isset($data['discount_percent']) ? round((float)$data['discount_percent'], 2) : 0.00,
            is_supply: (bool)($data['is_supply'] ?? false),
            is_realization: (bool)($data['is_realization'] ?? false),
            promo_code_discount: isset($data['promo_code_discount']) ? round((float)$data['promo_code_discount'], 2) : null,
            warehouse_name: $data['warehouse_name'] ?? null,
            country_name: $data['country_name'] ?? null,
            oblast_okrug_name: $data['oblast_okrug_name'] ?? null,
            region_name: $data['region_name'] ?? null,
            income_id: $data['income_id'] ?? null,
            odid: $data['odid'] ?? null,
            spp: isset($data['spp']) ? round((float)$data['spp'], 2) : 0.00,
            for_pay: isset($data['for_pay']) ? round((float)$data['for_pay'], 2) : 0.00,
            finished_price: isset($data['finished_price']) ? round((float)$data['finished_price'], 2) : 0.00,
            price_with_disc: isset($data['price_with_disc']) ? round((float)$data['price_with_disc'], 2) : 0.00,
            nm_id: (int)$data['nm_id'], // Обязательное поле, без null
            subject: $data['subject'] ?? null,
            category: $data['category'] ?? null,
            brand: $data['brand'] ?? null,
            is_storno: $data['is_storno'] ?? null,
            account_id: $accountId
        );
    }

    /**
     * Преобразует DTO в массив, пригодный для вставки в БД
     */
    public function toArray(): array
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
            'account_id' => $this->account_id,
        ];
    }
}
