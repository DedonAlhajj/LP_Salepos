<?php

namespace App\Imports;


use App\Jobs\ImportBillerJob;
use App\Jobs\ImportCustomerGroupJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CustomerGroupImport implements ToCollection, WithHeadingRow
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
                'email'        => trim($row['percentage']),
                'tenant_id'    => tenant('id'),
            ];

            try {
                $validatedData = Validator::make($data, [
                    "percentage" => ['required', 'string', 'max:255'],
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('customer_groups')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')

                    ],
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال Job بعد نجاح التحقق
                ImportCustomerGroupJob::dispatch($validatedData);

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}

