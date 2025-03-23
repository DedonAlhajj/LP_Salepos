<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\Tax;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportTaxJob implements ShouldQueue
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
            $brand = Tax::firstOrNew(['name' => $this->data['name']]);
            $brand->fill($this->data);
            $brand->save();
            Cache::forget('Tax_all');

        } catch (\Exception $e) {
            Log::error("❌ Failed importing Tax: " . $e->getMessage());
        }
    }
}
