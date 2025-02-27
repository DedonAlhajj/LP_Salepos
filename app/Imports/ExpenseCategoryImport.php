<?php

namespace App\Imports;


use App\Jobs\ImportBillerJob;
use App\Jobs\ImportExpenseCategoryJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ExpenseCategoryImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['code']) || empty($row['code'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'code'        => trim($row['code']),
                'name'         => trim($row['name']),
                'tenant_id'    => tenant('id'),
            ];

            try {
                $validatedData = Validator::make($data, [
                    'code' => [
                        'max:255',
                        Rule::unique('expense_categories')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at'),
                    ],
                    'name' => 'required|string|max:255',
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال Job بعد نجاح التحقق
                ImportExpenseCategoryJob::dispatch($validatedData, tenant('id'));

            } catch (ValidationException $e) {
                Log::warning("⚠️ An valid Record: " . json_encode($e->errors()));
            }
        }
    }
}

