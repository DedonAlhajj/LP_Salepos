<?php

namespace App\Services\Tenant;

use App\Models\Unit;

class UnitService
{

    /**
     * الحصول على وحدات البيع بناءً على الـ ID الأساسي
     */
    public function getSaleUnits(int $id)
    {
        return Unit::where("base_unit", $id)
            ->orWhere('id', $id)
            ->pluck('unit_name', 'id');
    }

    public function getUnit($unitId)
    {
        return Unit::findOrFail($unitId);
    }

}

