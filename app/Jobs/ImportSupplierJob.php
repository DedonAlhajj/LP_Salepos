<?php

namespace App\Jobs;

use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportSupplierJob implements ShouldQueue
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
            $biller = Supplier::firstOrNew(['company_name' => $this->data['company_name']]);

            $biller->fill($this->data);
            $biller->save();

            Log::info("تم استيراد العميل بنجاح: " . $this->data['name']);

        } catch (\Exception $e) {
            Log::error("❌ فشل استيراد العميل: " . $e->getMessage());
        }
    }
}
