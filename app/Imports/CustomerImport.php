<?php
namespace App\Imports;

use App\Jobs\ImportCustomerJob;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['companyname']) || empty($row['companyname'])) {
                continue;
            }

            // تنظيف البيانات
            $data = [
                'name'          => trim($row['name']),
                'company_name'  => trim($row['companyname']),
                'email'         => trim($row['email']),
                'phone_number'  => trim($row['phonenumber']),
                'address'       => trim($row['address']),
                'city'          => trim($row['city']),
                'state'         => trim($row['state']),
                'postal_code'   => trim($row['postalcode']),
                'country'       => trim($row['country']),
                'tenant_id'     => tenant('id'),
            ];

            // التحقق من مجموعة العميل (customer_group)
            $customerGroup = CustomerGroup::where('name', $row['customer_group'])->first();
            $data['customer_group_id'] = $customerGroup ? $customerGroup->id : null;

            // التحقق من صحة البيانات
            try {
                $validatedData = Validator::make($data, [
                    'customer_group_id' => 'nullable|exists:customer_groups,id',
                    'company_name' => [
                        'nullable', 'string', 'max:255',
                        Rule::unique('suppliers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'email' => [
                        'required', 'email', 'max:255',
                        Rule::unique('users')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'phone_number' => [
                        'nullable', 'string', 'max:15',
                        Rule::unique('customers')
                            ->where('tenant_id', tenant('id'))
                            ->whereNull('deleted_at')
                    ],
                    'address' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:255',
                    'state' => 'nullable|string|max:255',
                    'postal_code' => 'nullable|string|max:20',
                    'country' => 'nullable|string|max:255',
                    'name' => 'nullable|string|max:255',
                    'tenant_id'    => 'required|integer',
                ])->validate();

                // إرسال البيانات للـ Job
                ImportCustomerJob::dispatch($validatedData, tenant('id'));

            } catch (ValidationException $e) {
                Log::warning("⚠️ سجل غير صالح: " . json_encode($e->errors()));
            }
        }
    }


}
