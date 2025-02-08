<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportCustomerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $tenantId;

    public function __construct(array $data, $tenantId)
    {
        $this->data = $data;
        $this->tenantId = $tenantId;
    }

    public function handle()
    {
        try {
            $customer = Customer::firstOrNew(['name' => $this->data['name']]);
            $customer->customer_group_id = $this->data['customer_group_id'];
            $customer->name = $this->data['name'];
            $customer->company_name = $this->data['company_name'];
            $customer->email = $this->data['email'];
            $customer->phone_number = $this->data['phone_number'];
            $customer->address = $this->data['address'];
            $customer->city = $this->data['city'];
            $customer->state = $this->data['state'];
            $customer->postal_code = $this->data['postal_code'];
            $customer->country = $this->data['country'];
            $customer->tenant_id = $this->data['tenant_id'];
            $customer->save();

            Log::info("تم استيراد العميل بنجاح: " . $this->data['name']);

        } catch (\Exception $e) {
            Log::error("❌ فشل استيراد العميل: " . $e->getMessage());
        }
    }
}
