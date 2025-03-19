<?php

namespace App\Services\Tenant;

use App\Models\CustomField;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CustomFieldService
{

    /**
     * إنشاء حقل مخصص
     *
     * @param array $data
     * @return CustomField
     */
    public function createCustomField(array $data): CustomField
    {
        try {
            // ضبط القيم الافتراضية
            $data['default_value'] = $data['default_value_1'] ?? $data['default_value_2'] ?? null;

            // تحويل القيم البوليانية بشكل ذكي
            $booleanFields = ['is_table', 'is_invoice', 'is_required', 'is_admin', 'is_disable'];
            foreach ($booleanFields as $field) {
                $data[$field] = isset($data[$field]);
            }

            // إنشاء الحقل المخصص
            return CustomField::create($data);
        } catch (\Exception $e) {
            Log::error('Error creating custom field: ' . $e->getMessage(), ['data' => $data]);
            throw new \RuntimeException('Failed to create custom field. Please try again.');
        }
    }

    public function deleteCustomField(int $id): bool
    {
        try {
            $custom_field_data = CustomField::with('values')->findOrFail($id);

            if (!$custom_field_data->values) {
                $custom_field_data->delete();
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting custom field: ' . $e->getMessage());
            throw new \RuntimeException('Failed to delete custom field. Please try again.');
        }
    }

    public function updateBasicCustomField(array $data, int $id): bool
    {
        try {
            $CustomField = CustomField::findOrFail($id);
            // ضبط القيم الافتراضية
            $data['default_value'] = $data['default_value_1'] ?? $data['default_value_2'] ?? null;

            // تحويل القيم البوليانية بشكل ذكي
            $booleanFields = ['is_table', 'is_invoice', 'is_required', 'is_admin', 'is_disable'];
            foreach ($booleanFields as $field) {
                $data[$field] = isset($data[$field]);
            }

            // إنشاء الحقل المخصص
            $CustomField->update($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating custom field: ' . $e->getMessage(), ['data' => $data]);
            throw new \RuntimeException('Failed to updating custom field. Please try again.');
        }
    }

    public function storeCustomFields(Customer $customer, array $customFields): void
    {

        if (empty($customFields)) {
            return;
        }

        // جلب الحقول المطلوبة فقط بدلاً من تحميل جميع `CustomField`
        $fields = CustomField::where('entity_type', 'customer')
            ->whereIn('name', array_map(fn($key) => str_replace('_', ' ', $key), array_keys($customFields)))
            ->get()
            ->keyBy(fn($field) => str_replace(' ', '_', strtolower($field->name))); // إنشاء keyBy للوصول السريع

        if ($fields->isEmpty()) {
            return;
        }

        // تجهيز البيانات باستخدام `map()`
        $customFieldValues = collect($customFields)
            ->filter(fn($value, $key) => isset($fields[$key])) // تصفية الحقول غير الموجودة
            ->map(fn($value, $key) => [
                'custom_field_id' => $fields[$key]->id,
                'value' => is_array($value) ? implode(',', $value) : $value,
                'tenant_id' => $customer->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values()
            ->all();

        // إدخال البيانات دفعة واحدة باستخدام العلاقة بين `Customer` و `CustomFieldValue`
        $customer->customFields()->createMany($customFieldValues);
    }

    public function updateCustomFields(Customer $customer, array $customFields): void
    {
        if (empty($customFields)) {
            return;
        }

        // جلب الحقول المطلوبة فقط
        $fields = CustomField::where('entity_type', 'customer')
            ->whereIn('name', array_map(fn($key) => str_replace('_', ' ', $key), array_keys($customFields)))
            ->get()
            ->keyBy(fn($field) => str_replace(' ', '_', strtolower($field->name)));

        if ($fields->isEmpty()) {
            return;
        }

        // تجهيز البيانات دفعة واحدة
        $customFieldValues = collect($customFields)
            ->filter(fn($value, $key) => isset($fields[$key]))
            ->map(fn($value, $key) => [
                'custom_field_id' => $fields[$key]->id,
                'value' => is_array($value) ? implode(',', $value) : $value,
                'tenant_id' => $customer->tenant_id,
                'updated_at' => now(),
            ])
            ->values()
            ->all();

        // تحديث القيم دفعة واحدة
        $customer->customFields()->delete();
        $customer->customFields()->createMany($customFieldValues);
    }

    public function findCustomFieldById($id)
    {
        return CustomField::findOrFail($id);
    }

    public function getAllCustomFields()
    {
        return  CustomField::orderBy('id', 'desc')->get();
    }

    public function getCustomFields($entityType)
    {
        return CustomField::where('entity_type', $entityType)->get();
    }

    public function getCustomFieldsWithTable($entityType): array
    {
        return CustomField::where([
            ['entity_type', $entityType],
            ['is_table', true]
        ])->pluck('name')
            ->map(fn($field) => str_replace(" ", "_", strtolower($field)))
            ->toArray();
    }


    public function getFieldNames($customFields)
    {
        return $customFields->pluck('name')->map(fn($name) => str_replace(" ", "_", strtolower($name)))->toArray();
    }

    public function getCustomerCustomFields($customer, $fieldNames)
    {
        $customFieldValues = $customer->customFields->pluck('value', 'custom_field_id');

        // جلب أسماء الحقول المخصصة وربطها مع القيم المخزنة
        $customFields = CustomField::whereIn('id', $customFieldValues->keys())->pluck('id', 'name');

        // إنشاء مصفوفة الحقول مع القيم الخاصة بها
        return collect($fieldNames)->mapWithKeys(function ($field) use ($customFields, $customFieldValues) {
            $fieldId = $customFields[$field] ?? null;
            return [$field => $fieldId ? ($customFieldValues[$fieldId] ?? '-') : '-'];
        })->toArray();
    }

    public function getProductCustomFields(Product $product): array
    {
        $customFields = [];
        foreach ($product->customFields as $customFieldValue) {
            $customFields[$customFieldValue->customField->name] = $customFieldValue->value;
        }
        return $customFields;
    }

}

