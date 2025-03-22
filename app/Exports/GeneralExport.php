<?php

namespace App\Exports;

use App\Models\CustomerGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GeneralExport implements FromCollection, WithHeadings
{
    protected array $customerGroupIds;
    protected string $model;
    protected array $fields;
    protected array $extraData; // بيانات إضافية من الطلب

    /**
     * Constructor to set model, fields, and additional data.
     */
    public function __construct(array $customerGroupIds,string $model, array $fields, array $extraData = [])
    {
        $this->model = $model;
        $this->fields = $fields;
        $this->extraData = $extraData;
        $this->customerGroupIds = $customerGroupIds;
    }

    /**
     * Fetch the data dynamically based on the model and fields.
     */
    public function collection(): \Illuminate\Support\Collection
    {
        if (!empty($this->customerGroupIds)) {
            $data =  $this->model::whereIn('id', $this->customerGroupIds)
                ->select($this->fields)
                ->get();
        }
        else{
            $data = $this->model::select($this->fields)->get();
        }


        // دمج البيانات الإضافية إن وجدت
        if (!empty($this->extraData)) {
            $extraCollection = collect([$this->extraData]);
            $data = $extraCollection->merge($data);
        }

        return $data;
    }

    /**
     * Define column headings dynamically.
     */
    public function headings(): array
    {
        return $this->fields;
    }
}
