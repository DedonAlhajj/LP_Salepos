<?php

namespace App\Imports;


use App\Jobs\ImportBillerJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class BillerImport implements ToCollection, WithHeadingRow
{

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['companyname']) || empty($row['companyname'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'name'         => trim($row['name']),
                'image'        => isset($row['image']) ? trim($row['image']) : null,
                'email'        => trim($row['email']),
                'company_name' => trim($row['companyname']),
                'vat_number'   => trim($row['vatnumber']),
                'phone_number' => trim($row['phonenumber']),
                'address'      => trim($row['address']),
                'city'         => trim($row['city']),
                'state'        => trim($row['state']),
                'postal_code'  => trim($row['postalcode']),
                'country'      => trim($row['country']),
                'tenant_id'    => tenant('id'),
            ];

            Log::info("فشل استيراد البيانات: " . $data['tenant_id']);
            unset($data['image']);


            // التحقق من صحة البيانات قبل إرسالها للـ Job
            try {
                $validatedData = Validator::make($data, [
                    'name'         => 'required|string|max:255',
                    'company_name' => [
                        'required', 'string', 'max:255',
                        Rule::unique('billers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at'),
                    ],
                    'vat_number'   => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-]+$/',
                    'email' => [
                        'required', 'email', 'max:255',
                        Rule::unique('billers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at'),
                    ],
                    'phone_number' => 'required|string|max:20|regex:/^\+?[0-9\-]+$/',
                    'address'      => 'required|string|max:500',
                    'city'         => 'required|string|max:100',
                    'state'        => 'nullable|string|max:100',
                    'postal_code'  => 'nullable|string|max:20|regex:/^\d{4,10}$/',
                    'country'      => 'nullable|string|max:100',
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال Job بعد نجاح التحقق
                ImportBillerJob::dispatch($validatedData, tenant('id'));

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }
}

