<?php

namespace App\Imports;


use App\Jobs\ImportBillerJob;
use App\Jobs\ImportWarehouseJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class WarehouseImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['name']) || empty($row['name'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'name'         => trim($row['name']),
                'phone'        => trim($row['phone']),
                'email'        => trim($row['email']),
                'address'      => trim($row['address']),
                'tenant_id'    => tenant('id'),
            ];

            // التحقق من صحة البيانات قبل إرسالها للـ Job
            try {
                $validatedData = Validator::make($data, [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('warehouses')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')

                    ],
                    "phone" => 'nullable|string|max:15',
                    "email" => 'required|email|max:255',
                    "address" => 'nullable|string|max:255' ,
                    'tenant_id'    => 'required|integer',
                ])->validate();


                // إرسال Job بعد نجاح التحقق
                ImportWarehouseJob::dispatch($validatedData, tenant('id'));

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}

