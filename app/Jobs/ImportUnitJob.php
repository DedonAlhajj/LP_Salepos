<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportUnitJob implements ShouldQueue
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
            $Unit = Unit::firstOrNew(['unit_code' => $this->data['unit_code']]);

            $Unit->unit_code = $this->data['unit_code'];
            $Unit->unit_name = $this->data['unit_name'];
            if($this->data['base_unit']==null)
                $Unit->base_unit = null;
            else{
                $base_unit = Unit::where('unit_code', $this->data['base_unit'])->first();
                $Unit->base_unit = $base_unit->id;
            }
            if($this->data['operator'] == null)
                $this->data['operator'] = '*';
            else
                $Unit->operator = $this->data['operator'];

            if($this->data['operation_value'] == null)
                $this->data['operation_value'] = 1;
            else
                $Unit->operation_value = $this->data['operation_value'];

            $Unit->save();
            Cache::forget('Unit_all');
        } catch (\Exception $e) {
            Log::error("❌ Failed importing Unit: " . $e->getMessage());
        }
    }
}
