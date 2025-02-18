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

}
