<?php

namespace App\Events;

use App\Models\Adjustment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdjustmentDeleted
{
    use Dispatchable, SerializesModels;

    public $adjustment;

    public function __construct(Adjustment $adjustment)
    {
        $this->adjustment = $adjustment;
    }
}
