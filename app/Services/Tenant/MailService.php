<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\Mail\TransferDetails;
use App\Models\Returns;
use App\Models\ProductReturn;
use App\Models\Customer;
use App\Models\MailSetting;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReturnDetails;
use Exception;

class MailService
{
    protected $sendMailAction;

    public function __construct(SendMailAction $sendMailAction)
    {
        $this->sendMailAction = $sendMailAction;
    }

    public function sendTransferMail(array $mailData): bool
    {
        try {
            if (!empty($mailData['to_email'])) {
                Mail::to($mailData['to_email'])->send(new TransferDetails($mailData));
            }

            if (!empty($mailData['from_email'])) {
                Mail::to($mailData['from_email'])->send(new TransferDetails($mailData));
            }

            return 1; // نجاح الإرسال
        } catch (\Exception $e) {
            Log::error('Failed to send transfer email: ' . $e->getMessage());
            return 0; // فشل الإرسال
        }
    }

    public function sendReturnEmail(int $returnId): string
    {
        try {

            $return = Returns::findOrFail($returnId); // get return with fail
            $productReturns = ProductReturn::where('return_id', $returnId)->get();
            $customer = Customer::findOrFail($return->customer_id);

            if (!$customer->email) {
                return 'Customer doesnt have email!';
            }

            $mailData = $this->prepareMailData($return, $productReturns, $customer);

            try {
                !$this->sendMailAction->execute($mailData, ReturnDetails::class);
                return 'Mail sent successfully';
            } catch (Exception $e) {
                return 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        } catch (Exception $e) {
            Log::error("Return Send Email: " . $e->getMessage());
            throw new \Exception("Return Send Email.");
        }
    }

    private function prepareMailData(Returns $return, $productReturns, Customer $customer): array
    {
        $mailData = [
            'email' => $customer->email,
            'reference_no' => $return->reference_no,
            'total_qty' => $return->total_qty,
            'total_price' => $return->total_price,
            'order_tax' => $return->order_tax,
            'order_tax_rate' => $return->order_tax_rate,
            'grand_total' => $return->grand_total,
            'products' => [],
            'unit' => [],
            'qty' => [],
            'total' => []
        ];

        foreach ($productReturns as $key => $productReturn) {
            $product = Product::findOrFail($productReturn->product_id);
            $variantData = $productReturn->variant_id ? Variant::findOrFail($productReturn->variant_id) : null;
            $mailData['products'][$key] = $variantData ? $product->name . ' [' . $variantData->name . ']' : $product->name;
            $mailData['unit'][$key] = $productReturn->sale_unit_id ? Unit::findOrFail($productReturn->sale_unit_id)->unit_code : '';
            $mailData['qty'][$key] = $productReturn->qty;
            $mailData['total'][$key] = $productReturn->qty;
        }

        return $mailData;
    }


}

