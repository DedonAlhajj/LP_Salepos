<?php

namespace App\Jobs;

use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportWarehouseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $sendMailAction;
    protected $tenantId;

    public function __construct(array $data, $tenantId)
    {
        $this->data = $data;
        $this->tenantId = $tenantId;
    }

    public function handle()
    {
        try {

            $biller = Warehouse::firstOrNew(['name' => $this->data['name']]);

            $biller->fill($this->data);
            $biller->save();
            Cache::forget('Warehouse');
            Cache::forget('Warehouse_Count');
        } catch (\Exception $e) {
            Log::error("❌ Failed import Warehouse: " . $e->getMessage());
        }
    }
}
