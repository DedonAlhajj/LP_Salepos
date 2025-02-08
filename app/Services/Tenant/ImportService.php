<?php

namespace App\Services\Tenant;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportService
{
    public function import($importClass, $file)
    {
        try {
            Excel::import(new $importClass, $file);
        } catch (\Exception $e) {
            Log::error("❌ importing data failed " . $e->getMessage());
            throw new \Exception("⚠️ Error while imported the data,try again.");
        }
    }

    //                     Commands
    // php artisan make:import SupplierImport
    // php artisan make:job ImportSupplierJob

}

