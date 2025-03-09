<?php

namespace App\DTOs;

use App\Models\Account;
use App\Models\CashRegister;
use App\Models\Returns;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnSaleDTO
{
    public $reference_no;
    public $user_id;
    public $customer_id;
    public $warehouse_id;
    public $biller_id;
    public $currency_id;
    public $exchange_rate;
    public $cash_register_id;
    public $account_id;
    public $total_qty;
    public $total_discount;
    public $total_tax;
    public $total_price;
    public $order_tax;
    public $order_tax_rate;
    public $grand_total;
    public $return_note;
    public $staff_note;
    public $sale_id;
    public $change_sale_status;

    // الحقول الجديدة التي يجب تضمينها
    public $product_batch_id;
    public $actual_qty;
    public $qty;
    public $is_return;
    public $product_code;
    public $product_id;
    public $unit_price;
    public $product_variant_id;
    public $product_price;
    public $sale_unit;
    public $net_unit_price;
    public $discount;
    public $tax_rate;
    public $tax;
    public $subtotal;
    public $imei_number;

    // تحويل البيانات إلى مصفوفة
    public function toArray(): array
    {
        return [
            'reference_no' => $this->reference_no,
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'warehouse_id' => $this->warehouse_id,
            'biller_id' => $this->biller_id,
            'currency_id' => $this->currency_id,
            'exchange_rate' => $this->exchange_rate,
            'cash_register_id' => $this->cash_register_id,
            'account_id' => $this->account_id,
            'total_qty' => $this->total_qty,
            'total_discount' => $this->total_discount,
            'total_tax' => $this->total_tax,
            'total_price' => $this->total_price,
            'order_tax' => $this->order_tax,
            'order_tax_rate' => $this->order_tax_rate,
            'grand_total' => $this->grand_total,
            'return_note' => $this->return_note,
            'staff_note' => $this->staff_note,
            'sale_id' => $this->sale_id,
            'change_sale_status' => $this->change_sale_status,
            // الحقول الجديدة
            'product_batch_id' => $this->product_batch_id,
            'actual_qty' => $this->actual_qty,
            'qty' => $this->qty,
            'is_return' => $this->is_return,
            'product_code' => $this->product_code,
            'product_id' => $this->product_id,
            'unit_price' => $this->unit_price,
            'product_variant_id' => $this->product_variant_id,
            'product_price' => $this->product_price,
            'sale_unit' => $this->sale_unit,
            'net_unit_price' => $this->net_unit_price,
            'discount' => $this->discount,
            'tax_rate' => $this->tax_rate,
            'tax' => $this->tax,
            'subtotal' => $this->subtotal,
            'imei_number' => $this->imei_number,
        ];
    }

    public static function fromRequest(Sale $sale, Request $request): self
    {
        $dto = new self();
        $dto->reference_no = 'rr-' . now()->format("Ymd-His");
        $dto->user_id = Auth::id();
        $dto->customer_id = $sale->customer_id;
        $dto->warehouse_id = $sale->warehouse_id;
        $dto->biller_id = $sale->biller_id;
        $dto->currency_id = $sale->currency_id;
        $dto->exchange_rate = $sale->exchange_rate;
        $dto->cash_register_id = CashRegister::where([['user_id', Auth::id()], ['warehouse_id', $sale->warehouse_id], ['status', true]])->value('id');
        $dto->account_id = Account::where('is_default', true)->value('id');
        $dto->total_qty = $request->input('total_qty');
        $dto->total_discount = $request->input('total_discount');
        $dto->total_tax = $request->input('total_tax');
        $dto->total_price = $request->input('total_price');
        $dto->order_tax = $request->input('order_tax');
        $dto->order_tax_rate = $request->input('order_tax_rate');
        $dto->grand_total = $request->input('grand_total');
        $dto->return_note = $request->input('return_note');
        $dto->staff_note = $request->input('staff_note');
        $dto->sale_id = $request->input('sale_id');
        $dto->change_sale_status = $request->input('change_sale_status');

        // إضافة الحقول الجديدة
        $dto->product_batch_id = $request->input('product_batch_id');
        $dto->actual_qty = $request->input('actual_qty');
        $dto->qty = $request->input('qty');
        $dto->is_return = $request->input('is_return');
        $dto->product_code = $request->input('product_code');
        $dto->product_id = $request->input('product_id');
        $dto->unit_price = $request->input('unit_price');
        $dto->product_variant_id = $request->input('product_variant_id');
        $dto->product_price = $request->input('product_price');
        $dto->sale_unit = $request->input('sale_unit');
        $dto->net_unit_price = $request->input('net_unit_price');
        $dto->discount = $request->input('discount');
        $dto->tax_rate = $request->input('tax_rate');
        $dto->tax = $request->input('tax');
        $dto->subtotal = $request->input('subtotal');
        $dto->imei_number = $request->input('imei_number');

        return $dto;
    }
}


