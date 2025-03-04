<?php
namespace App\DTOs;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransferData
{
    public function __construct(
        public readonly int $from_warehouse_id,
        public readonly int $to_warehouse_id,
        public readonly int $status,
        public readonly array $product_id,
        public readonly array $purchase_unit,
        public readonly array $qty,
        public readonly array $net_unit_cost,
        public readonly array $tax_rate,
        public readonly array $tax,
        public readonly array $subtotal,
        public readonly ?array $imei_number,
        public readonly int $total_qty,
        public readonly ?float $total_discount,
        public readonly float $total_tax,
        public readonly float $total_cost,
        public readonly int $item,
        public readonly ?float $order_tax,
        public readonly float $grand_total,
        public readonly float $paid_amount,
        public readonly int $payment_status,
        public readonly float $shipping_cost,
        public readonly ?string $note,
        public readonly string $reference_no,
        public readonly int $user_id,
        public readonly Carbon $created_at,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            from_warehouse_id: (int) $data['from_warehouse_id'],
            to_warehouse_id: (int) $data['to_warehouse_id'],
            status: (int) $data['status'],
            product_id: $data['product_id'],
            purchase_unit: $data['purchase_unit'],
            qty: $data['qty'],
            net_unit_cost: $data['net_unit_cost'],
            tax_rate: $data['tax_rate'],
            tax: $data['tax'],
            subtotal: $data['subtotal'],
            imei_number: $data['imei_number'] ?? null,
            total_qty: (int) $data['total_qty'],
            total_discount: isset($data['total_discount']) ? (float) $data['total_discount'] : null,
            total_tax: (float) $data['total_tax'],
            total_cost: (float) $data['total_cost'],
            item: (int) $data['item'],
            order_tax: isset($data['order_tax']) ? (float) $data['order_tax'] : null,
            grand_total: (float) $data['grand_total'],
            paid_amount: (float) $data['paid_amount'],
            payment_status: (int) $data['payment_status'],
            shipping_cost: (float) $data['shipping_cost'],
            note: $data['note'] ?? null,
            reference_no: 'tr-' . now()->format("Ymd-His"),
            user_id: Auth::id(),
            created_at: isset($data['created_at']) ? Carbon::parse($data['created_at']) : now(),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
