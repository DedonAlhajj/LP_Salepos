<?php

namespace App\Services\Tenant;

use App\Exports\GeneralExport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportService
{
    /**
     * Import data using the given class and file.
     *
     * @param string $importClass The class responsible for handling the import logic.
     * @param mixed $file The file to import, usually uploaded by the user.
     * @throws \Exception Throws an exception if the import fails.
     *
     * This function uses the Excel library to import data. If an error occurs during the import,
     * it logs the error message and throws a custom exception for the caller to handle.
     */
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



    /**
     * Export data to an Excel file based on the specified parameters.
     *
     * @param array $request The data array to be exported.
     * @param string $model The name of the model to be used for data retrieval.
     * @param array $fields The fields to include in the export.
     * @param array $extraData Additional data to include in the export (optional).
     * @return BinaryFileResponse Returns a response containing the downloadable file.
     * @throws \Exception Throws an exception if the export fails or an invalid model is provided.
     *
     * This function validates the model, generates a dynamic file name using the model name and the current date,
     * and utilizes the Excel library to create and download the export file. Any errors are logged and an exception is thrown.
     */
    public function export(array $request,string $model,array $fields,array $extraData = []): BinaryFileResponse
    {
        try {
            $modelClass = "App\\Models\\" . $model;

            if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
                throw new \Exception("⚠️ Invalid model provided.");
            }

            $filename = strtolower($model) . '_' . now()->format('d-m-Y') . '.xlsx';

            return Excel::download(new GeneralExport($request,$modelClass, $fields, $extraData ?? []), $filename);

        } catch (\Throwable $e) {
            Log::error('Export failed: ' . $e->getMessage());
            throw new \Exception("⚠️ Failed to export data. Please try again.");
        }
    }



}

