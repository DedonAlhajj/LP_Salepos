<?php

namespace App\Jobs;

use App\Actions\SendMailAction;
use App\Mail\BillerCreate;
use App\Models\Biller;
use App\Models\CustomerGroup;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportCustomerGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            $CustomerGroup = CustomerGroup::firstOrNew(['name' => $this->data['name']]);

            $CustomerGroup->fill($this->data);
            $CustomerGroup->save();


        } catch (\Exception $e) {
            Log::error("❌ Field import CustomerGroup : " . $e->getMessage());
        }
    }
}
