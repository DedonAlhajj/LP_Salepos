<?php

namespace App\Imports;


use App\Jobs\ImportUnitJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class UnitImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            if (empty($row['code'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'unit_code'         => trim($row['code']),
                'unit_name'         => trim($row['name']),
                'base_unit'         => trim($row['base_unit']),
                'operator'          => trim($row['operator']),
                'operation_value'   => trim($row['operation_value']),
                'tenant_id'         => tenant('id'),
            ];

            // التحقق من صحة البيانات قبل إرسالها للـ Job
            try {
                $validatedData = Validator::make($data, [
                    'unit_code' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('units')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')

                    ],
                    'unit_name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('units')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')

                    ],
                    'operator' => ['nullable', 'string', 'max:255'],
                    'base_unit' => ['nullable'],
                    'operation_value' => ['nullable',  'numeric', 'min:0'],
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال Job بعد نجاح التحقق
                ImportUnitJob::dispatch($validatedData);

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}

