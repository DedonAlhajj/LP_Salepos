<?php

namespace App\Imports;


use App\Jobs\ImportBillerJob;
use App\Jobs\ImportPurchaseJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PurchaseImport implements ToCollection, WithHeadingRow
{

    protected $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // التحقق من وجود البيانات الأساسية
            if (empty($row['product_code']) || empty($row['quantity'])) {
                continue;
            }

            $data = [
                'product_code'   => trim($row['product_code']),
                'quantity'       => trim($row['quantity']),
                'purchase_unit'  => trim($row['purchase_unit_code']),
                'cost'           => trim($row['cost']),
                'discount'       => trim($row['discount_per_unit']),
                'tax_name'       => trim($row['tax_name']),
                'tenant_id'      => tenant('id'),
            ];

            try {
                $validatedData = Validator::make($data, [
                    'product_code'   => 'required|string|max:255',
                    'quantity'       => 'required|integer|min:1',
                    'purchase_unit'  => 'required|string',
                    'cost'           => 'required|numeric|min:0',
                    'discount'       => 'nullable|numeric|min:0',
                    'tax_name'       => 'nullable|string',
                ])->validate();

                $data = array_merge($this->requestData,$validatedData);
                Log::info('Purchase Data:', $data);
                // إرسال Job بعد نجاح التحقق
                ImportPurchaseJob::dispatch($data);

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}


