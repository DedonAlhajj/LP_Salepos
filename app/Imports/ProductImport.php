<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Jobs\ImportProductJob;
use Illuminate\Support\Facades\Log;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['name']) || empty($row['name'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'name'             => trim($row['name']),
                'image'            => isset($row['image']) ? trim($row['image']) : null,
                'code'             => trim($row['code']),
                'type'             => trim(strtolower($row['type'])),
                'brand'            => isset($row['brand']) ? trim($row['brand']) : null,
                'category'         => trim($row['category']),
                'unit_code'        => trim($row['unit_code']),
                'cost'             => str_replace(",", "", $row['cost']),
                'price'            => str_replace(",", "", $row['price']),
                'product_details'  => isset($row['product_details']) ? trim($row['product_details']) : null,
                'variant_value'    => isset($row['variant_value']) ? trim($row['variant_value']) : null,
                'variant_name'     => isset($row['variant_name']) ? trim($row['variant_name']) : null,
                'item_code'        => isset($row['item_code']) ? trim($row['item_code']) : null,
                'additional_cost'  => isset($row['additional_cost']) ? trim($row['additional_cost']) : null,
                'additional_price' => isset($row['additional_price']) ? trim($row['additional_price']) : null,
                'tenant_id'        => tenant('id'),
            ];

            // التحقق من صحة البيانات قبل إدخالها للطابور
            try {
                $validatedData = Validator::make($data, [
                    'name'         => 'required|string|max:255',
                    'code'         => 'required|string|max:50|unique:products,code,NULL,id,tenant_id,' . tenant('id'),
                    'type'         => 'required|string|in:standard,combo,digital',
                    'brand'        => 'nullable|string|max:255',
                    'category'     => 'required|string|max:255',
                    'unit_code'    => 'required|string|max:50',
                    'cost'         => 'required|numeric|min:0',
                    'price'        => 'required|numeric|min:0',
                    'product_details' => 'nullable|string',
                    'variant_value' => 'nullable|string',
                    'variant_name'  => 'nullable|string',
                    'item_code'     => 'nullable|string',
                    'additional_cost' => 'nullable|string',
                    'additional_price' => 'nullable|string',
                    'tenant_id'     => 'required|integer',
                ])->validate();

                // إرسال البيانات للـ Job بعد نجاح التحقق
                ImportProductJob::dispatch($validatedData, tenant('id'));

            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}
