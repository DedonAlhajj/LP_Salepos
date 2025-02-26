<?php

namespace App\Services\Tenant;



use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;

class WarehouseService
{

    public function getWarehouses(): Collection
    {
        return Warehouse::all();
    }

    public function getWarehousesById($user)
    {
        return Warehouse::when(!$user->hasRole(['Admin', 'Owner']), function ($query) use ($user) {
                return $query->where('id', $user->warehouse_id);
            })
            ->get();
    }
}
