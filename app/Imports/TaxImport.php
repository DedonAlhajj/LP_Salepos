<?php

namespace App\Imports;


use App\Jobs\ImportTaxJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class TaxImport implements ToCollection, WithHeadingRow
{
//name,rate
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['name'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'name'         => trim($row['name']),
                'rate'         => trim($row['rate']),
                'tenant_id'    => tenant('id'),
            ];


            // التحقق من صحة البيانات قبل إرسالها للـ Job
            try {
                $validatedData = Validator::make($data, [
                    'name' => [
                        'max:255',
                        Rule::unique('taxes')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'rate' => 'numeric|min:0|max:100',
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال Job بعد نجاح التحقق
                ImportTaxJob::dispatch($validatedData);

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}

