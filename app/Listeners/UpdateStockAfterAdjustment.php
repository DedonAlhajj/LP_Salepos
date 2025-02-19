<?php

namespace App\Listeners;

use App\Events\AdjustmentUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateStockAfterAdjustment implements ShouldQueue
{
    public function handle(AdjustmentUpdated $event)
    {
        // ✅ يمكنك تنفيذ أي عمليات أخرى بعد تحديث المخزون هنا
        Log::info('تم تحديث المخزون بعد التعديل', ['adjustment_id' => $event->adjustment->id]);
    }
}
