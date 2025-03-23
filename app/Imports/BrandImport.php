<?php

namespace App\Imports;


use App\Jobs\ImportBrandJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class BrandImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['title']) || empty($row['title'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'title'         => trim($row['title']),
                'image'        => isset($row['image']) ? trim($row['image']) : null,
                'tenant_id'    => tenant('id'),
            ];

            unset($data["image"]);


            // التحقق من صحة البيانات قبل إرسالها للـ Job
            try {
                $validatedData = Validator::make($data, [
                    'title'         => 'required|string|max:255',
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال Job بعد نجاح التحقق
                ImportBrandJob::dispatch($validatedData);

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}

