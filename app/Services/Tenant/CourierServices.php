<?php

namespace App\Services\Tenant;

use App\Models\Courier;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CourierServices
{


    public function getCourier()
    {
        try {
            return Cache::remember("Courier_all", 60, function () {
                return Courier::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Courier): " . $e->getMessage());
            throw new Exception("An error occurred while fetching the modification data (Courier)..");
        }
    }
}
