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

    public function getUnitData(int $baseUnitId, int $purchaseUnitId): array
    {
        $units = Unit::where('base_unit', $baseUnitId)->orWhere('id', $baseUnitId)->get();

        $unit_name = [];
        $unit_operator = [];
        $unit_operation_value = [];

        foreach ($units as $unit) {
            if ($purchaseUnitId == $unit->id) {
                array_unshift($unit_name, $unit->unit_name);
                array_unshift($unit_operator, $unit->operator);
                array_unshift($unit_operation_value, $unit->operation_value);
            } else {
                $unit_name[] = $unit->unit_name;
                $unit_operator[] = $unit->operator;
                $unit_operation_value[] = $unit->operation_value;
            }
        }

        return compact('unit_name', 'unit_operator', 'unit_operation_value');
    }

    public function calculateReceivedValue(string $unitName, float $receivedQty): float
    {
        $unit = Unit::where('unit_name', $unitName)->firstOrFail();

        return ($unit->operator == '*')
            ? $receivedQty * $unit->operation_value
            : $receivedQty / $unit->operation_value;
    }

    public function getUnitId(string $unitName): int
    {
        return Unit::where('unit_name', $unitName)->value('id');
    }

}

