<?php

namespace App\Services\Tenant;



use App\Models\Product_Warehouse;
use App\Models\ProductTransfer;
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


    public function updateWarehouseStock(ProductTransfer $productTransfer, int $warehouseId, float $quantity, string $operation): void
    {
        $query = Product_Warehouse::query();

        if ($productTransfer->variant_id) {
            $query->FindProductWithVariant($productTransfer->product_id, $productTransfer->variant_id, $warehouseId);
        } else {
            $query->FindProductWithoutVariant($productTransfer->product_id, $warehouseId);
        }

        $productWarehouse = $query->firstOrFail();

        if ($operation === 'increase') {
            $productWarehouse->increment('qty', $quantity);
        } else {
            $productWarehouse->decrement('qty', $quantity);
        }
    }


}
