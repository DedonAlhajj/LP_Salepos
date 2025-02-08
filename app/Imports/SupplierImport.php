<?php

namespace App\Imports;

use App\Jobs\ImportSupplierJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToCollection, WithHeadingRow
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


            // التحقق من صحة البيانات
            try {
                $validatedData = Validator::make($data, [
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'name' => 'required|string|max:255',
                    'company_name' => [
                        'nullable', 'string', 'max:255',
                        Rule::unique('suppliers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'email' => [
                        'required', 'email', 'max:255',
                        Rule::unique('suppliers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'phone_number' => [
                        'nullable', 'string', 'max:15',
                        Rule::unique('customers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'vat_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-]+$/',
                    'address' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'state' => 'nullable|string|max:255',
                    'postal_code' => 'nullable|string|max:20',
                    'country' => 'nullable|string|max:255',
                    'tenant_id' => 'required|integer',
                ])->validate();

                // إرسال البيانات للـ Job
                ImportSupplierJob::dispatch($validatedData, tenant('id'));

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
        Cache::forget('suppliers_list');
    }
}
