<?php

namespace App\DTOs;

class PosSettingDTO
{
    public function __construct(
        public int $customer_id,
        public int $warehouse_id,
        public int $biller_id,
        public int $product_number,
        public string $stripe_public_key,
        public string $stripe_secret_key,
        public string $paypal_username,
        public string $paypal_password,
        public string $paypal_signature,
        public string $invoice_size,
        public string $thermal_invoice_size,
        public bool $keyboard_active,
        public bool $is_table,
        public bool $send_sms,
        public array $payment_options
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            customer_id: (int) $data['customer_id'],
            warehouse_id: (int) $data['warehouse_id'],
            biller_id: (int) $data['biller_id'],
            product_number: (int) $data['product_number'],
            stripe_public_key: trim($data['stripe_public_key'] ?? ''),
            stripe_secret_key: trim($data['stripe_secret_key'] ?? ''),
            paypal_username: trim($data['paypal_username'] ?? ''),
            paypal_password: trim($data['paypal_password'] ?? ''),
            paypal_signature: trim($data['paypal_signature'] ?? ''),
            invoice_size: trim($data['invoice_size'] ?? 'default'),
            thermal_invoice_size: trim($data['thermal_invoice_size'] ?? 'default'),
            keyboard_active: isset($data['keyboard_active']),
            is_table: isset($data['is_table']),
            send_sms: isset($data['send_sms']),
            payment_options: isset($data['options']) ? array_map('trim', $data['options']) : ["none"]
        );
    }
}
