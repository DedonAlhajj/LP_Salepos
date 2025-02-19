<?php

namespace App\Listeners;

use App\Events\AdjustmentDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HandleAdjustmentDeletion implements ShouldQueue
{
    public function handle(AdjustmentDeleted $event)
    {
        Log::info('تم حذف تعديل المخزون', ['adjustment_id' => $event->adjustment->id]);
    }
}
