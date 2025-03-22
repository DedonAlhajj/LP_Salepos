<?php

namespace App\Services\Tenant;


use App\Models\Table;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TableService
{

    public function getActiveTable(): Collection
    {
        try {
            return Cache::remember("Table_all", 60, function () {
                return Table::withoutTrashed()->get();
            });
        } catch (Exception $e) {
            Log::error("Error fetching modifications (Table): " . $e->getMessage());
            throw new \Exception("An error occurred while fetching the modification data (Table)..");
        }
    }

    public function storeTable(array $data)
    {
        try {
            Table::create($data);

            Cache::forget('Table_all');

        } catch (\Exception $e) {
            Log::error('An error occurred while saving data table.: ' . $e->getMessage());
            throw new Exception('An error occurred while saving data table.');
        }
    }

    public function updateTable(array $request)
    {

        try {

            Table::findOrFail($request['table_id'])->update($request);

            Cache::forget('Table_all');

        } catch (ModelNotFoundException $e) {
            Log::error('Table not found: ' . $e->getMessage());
            throw new Exception('Table not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('An error occurred while updating data.: ' . $e->getMessage());
            throw new Exception('An error occurred while updating data table.');
        }
    }

    public function destroy(int $id)
    {
        try {
            Table::findOrFail($id)->delete();

            Cache::forget('Table_all');
        } catch (ModelNotFoundException $e) {
            Log::error('Table not found: ' . $e->getMessage());
            throw new Exception('Table not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting table: ' . $e->getMessage());
            throw new Exception('An error occurred while deleting data table.');
        }
    }

}
