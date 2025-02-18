<?php

namespace App\Services\Tenant;

use App\Models\CustomField;
use App\Models\Customer;
use App\Models\Product;

class CustomFieldService
{
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

