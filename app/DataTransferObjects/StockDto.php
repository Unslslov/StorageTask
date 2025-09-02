<?php

namespace App\DataTransferObjects;

class StockDto
{
    public function __construct(
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?string $barcode,
        public ?int $quantity,
        public ?bool $is_supply,
        public ?bool $is_realization,
        public ?int $quantity_full,
        public ?string $warehouse_name,
        public ?int $in_way_to_client,
        public ?int $in_way_from_client,
        public ?int $nm_id,
        public ?string $subject,
        public ?string $category,
        public ?string $brand,
        public ?string $sc_code,
        public ?float $price,
        public ?float $discount,
        public int $account_id,
    ) {
    }

    /**
     * Создаёт DTO из массива данных от API
     */
    public static function fromArray(array $data, int $accountId): self
    {
        return new self(
            date: $data['date'] ?? null,
            last_change_date: $data['last_change_date'] ?? null,
            supplier_article: isset($data['supplier_article']) ? (string)$data['supplier_article'] : null,
            tech_size: isset($data['tech_size']) ? (string)$data['tech_size'] : null,
            barcode: isset($data['barcode']) ? (string)$data['barcode'] : null,
            quantity: $data['quantity'] ?? null,
            is_supply: filter_var($data['is_supply'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            is_realization: filter_var($data['is_realization'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            quantity_full: $data['quantity_full'] ?? null,
            warehouse_name: $data['warehouse_name'] ?? null,
            in_way_to_client: $data['in_way_to_client'] ?? null,
            in_way_from_client: $data['in_way_from_client'] ?? null,
            nm_id: $data['nm_id'] ?? null,
            subject: isset($data['subject']) ? (string)$data['subject'] : null,
            category: isset($data['category']) ? (string)$data['category'] : null,
            brand: isset($data['brand']) ? (string)$data['brand'] : null,
            sc_code: isset($data['sc_code']) ? (string)$data['sc_code'] : null,
            price: isset($data['price']) ? round((float)$data['price'], 2) : null,
            discount: isset($data['discount']) ? round((float)$data['discount'], 2) : null,
            account_id: $accountId
        );
    }

    /**
     * Преобразует DTO в массив, пригодный для вставки в БД
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
            'is_supply' => $this->is_supply,
            'is_realization' => $this->is_realization,
            'quantity_full' => $this->quantity_full,
            'warehouse_name' => $this->warehouse_name,
            'in_way_to_client' => $this->in_way_to_client,
            'in_way_from_client' => $this->in_way_from_client,
            'nm_id' => $this->nm_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'brand' => $this->brand,
            'sc_code' => $this->sc_code,
            'price' => $this->price,
            'discount' => $this->discount,
            'account_id' => $this->account_id,
        ];
    }
}
