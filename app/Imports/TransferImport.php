<?php

namespace App\Imports;


use App\Jobs\ImportBillerJob;
use App\Jobs\ImportPurchaseJob;
use App\Jobs\ImportTransferJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class TransferImport implements ToCollection, WithHeadingRow
{
    protected $transferData;

    public function __construct(array $transferData)
    {
        $this->transferData = $transferData;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['product_code']) || empty($row['product_code'])) {
                continue;
            }

            $data = [
                'product_code'  => trim($row['product_code']),
                'quantity'      => (int) trim($row['quantity']),
                'purchase_unit' => trim($row['purchase_unit']),
                'unit_cost'     => (float) trim($row['product_cost']),
                'tax_name'      => isset($row['tax_name']) ? trim($row['tax_name']) : null,
            ];

            try {
                $validatedData = Validator::make($data, [
                    'product_code'  => 'required|string',
                    'quantity'      => 'required|integer',
                    'purchase_unit' => 'required|string',
                    'unit_cost'     => 'required|numeric',
                    'tax_name'      => 'nullable|string',
                ])->validate();

                ImportTransferJob::dispatch($validatedData, $this->transferData);

            } catch (ValidationException $e) {
                Log::warning("⚠️ بيانات غير صالحة: " . json_encode($e->errors()));
            }
        }
    }
}
