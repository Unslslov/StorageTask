<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderDto
{
    public function __construct(
        public ?string $g_number,
        public ?Carbon $date,
        public ?Carbon $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?string $barcode,
        public float $total_price,
        public float $discount_percent,
        public ?string $warehouse_name,
        public ?string $oblast,
        public int $income_id,
        public int $odid,
        public ?int $nm_id,
        public ?string $subject,
        public ?string $category,
        public ?string $brand,
        public bool $is_cancel,
        public ?Carbon $cancel_dt,
        public int $account_id,
    ) {
    }

    /**
     * Создаёт DTO из массива данных API
     */
    public static function fromArray(array $data, int $accountId): self
    {
        return new self(
            g_number: $data['g_number'] ?? null,
            date: isset($data['date']) ? Carbon::parse($data['date']) : null,
            last_change_date: isset($data['last_change_date']) ? Carbon::parse($data['last_change_date']) : null,
            supplier_article: isset($data['supplier_article']) ? Str::substr($data['supplier_article'], 0, 64) : null,
            tech_size: isset($data['tech_size']) ? Str::substr($data['tech_size'], 0, 64) : null,
            barcode: isset($data['barcode']) ? (string)$data['barcode'] : null,
            total_price: isset($data['total_price']) ? round((float)$data['total_price'], 2) : 0.00,
            discount_percent: isset($data['discount_percent']) ? round((float)$data['discount_percent'], 2) : 0.00,
            warehouse_name: isset($data['warehouse_name']) ? Str::substr($data['warehouse_name'], 0, 64) : null,
            oblast: $data['oblast'] ?? null,
            income_id: isset($data['income_id']) ? (int)$data['income_id'] : 0,
            odid: isset($data['odid']) ? (int)$data['odid'] : 0,
            nm_id: $data['nm_id'] ?? null,
            subject: isset($data['subject']) ? Str::substr($data['subject'], 0, 64) : null,
            category: isset($data['category']) ? Str::substr($data['category'], 0, 64) : null,
            brand: isset($data['brand']) ? Str::substr($data['brand'], 0, 64) : null,
            is_cancel: (bool)($data['is_cancel'] ?? false),
            cancel_dt: isset($data['cancel_dt']) ? Carbon::parse($data['cancel_dt']) : null,
            account_id: $accountId
        );
    }

    /**
     * Преобразует DTO в массив, подходящий для Eloquent
     */
    public function toArray(): array
    {
        return [
            'g_number' => $this->g_number,
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'total_price' => $this->total_price,
            'discount_percent' => $this->discount_percent,
            'warehouse_name' => $this->warehouse_name,
            'oblast' => $this->oblast,
            'income_id' => $this->income_id,
            'odid' => $this->odid,
            'nm_id' => $this->nm_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'brand' => $this->brand,
            'is_cancel' => $this->is_cancel,
            'cancel_dt' => $this->cancel_dt,
            'account_id' => $this->account_id,
        ];
    }
}
