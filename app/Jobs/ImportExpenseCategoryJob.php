<?php

namespace App\Jobs;

use App\Models\ExpenseCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportExpenseCategoryJob implements ShouldQueue
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
            $ExpenseCategory = ExpenseCategory::firstOrNew(['code' => $this->data['code']]);

            $ExpenseCategory->fill($this->data);
            $ExpenseCategory->save();

        } catch (\Exception $e) {
            Log::error("❌ error import Expense Category job: " . $e->getMessage());
        }
    }
}
