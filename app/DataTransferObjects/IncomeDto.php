<?php

namespace App\DataTransferObjects;

class IncomeDto
{
    public function __construct(
        public ?int $income_id,
        public ?string $number,
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?string $barcode,
        public ?int $quantity,
        public float $total_price,
        public ?string $date_close,
        public ?string $warehouse_name,
        public ?int $nm_id,
        public ?int $account_id,
    ) {
    }

    public static function fromArray(array $data, int $accountId): self
    {
        return new self(
            income_id: $data['income_id'] ?? null,
            number: isset($data['number']) ? (string)$data['number'] : null,
            date: $data['date'] ?? null,
            last_change_date: $data['last_change_date'] ?? null,
            supplier_article: isset($data['supplier_article']) ? (string)$data['supplier_article'] : null,
            tech_size: isset($data['tech_size']) ? (string)$data['tech_size'] : null,
            barcode: isset($data['barcode']) ? (string)$data['barcode'] : null,
            quantity: $data['quantity'] ?? null,
            total_price: isset($data['total_price']) ? round((float)$data['total_price'], 2) : 0.00,
            date_close: $data['date_close'] ?? null,
            warehouse_name: $data['warehouse_name'] ?? null,
            nm_id: $data['nm_id'] ?? null,
            account_id: $accountId
        );
    }

    public function toArray(): array
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
            'account_id' => $this->account_id,
        ];
    }
}
