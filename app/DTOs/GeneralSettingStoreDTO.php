<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class GeneralSettingStoreDTO
{
    public function __construct(
        public ?string $site_title,
        public ?bool   $is_rtl,
        public ?bool   $is_zatca,
        public ?string $company_name,
        public ?string $vat_registration_number,
        public ?string $currency,
        public ?string $currency_position,
        public ?int    $decimal,
        public ?string $staff_access,
        public ?bool   $without_stock,
        public ?bool   $is_packing_slip,
        public ?string $date_format,
        public ?string $developed_by,
        public ?string $invoice_format,
        public ?string $state,
        public ?string $expiry_type,
        public ?int    $expiry_value,
        public ?string $timezone,
        public ?UploadedFile $site_logo_path = null
    )
    {
    }

    public static function fromRequest(Request $request): self
    {
        $data = $request->all();
        // التحقق مما إذا كانت الصورة موجودة وتعيينها ككائن UploadedFile
        $image = isset($data['site_logo']) && $data['site_logo'] instanceof \Illuminate\Http\UploadedFile
            ? $data['site_logo']
            : null;  // إذا لم تكن الصورة موجودة، قم بتعيينها كـ null
        if(isset($data['is_rtl']))
            $data['is_rtl'] = true;
        else
            $data['is_rtl'] = false;

        if(isset($data['is_zatca'])) {
            $data['is_zatca'] = true;
        }
        else
            $data['is_zatca'] = false;
        return new self(
            site_title: $request->input('site_title'),
            is_rtl: $data['is_rtl']  ,
            is_zatca: $data['is_zatca'],
            company_name: $request->input('company_name'),
            vat_registration_number: $request->input('vat_registration_number'),
            currency: $request->input('currency'),
            currency_position: $request->input('currency_position'),
            decimal: (int)$request->input('decimal'),
            staff_access: $request->input('staff_access'),
            without_stock: $request->boolean('without_stock'),
            is_packing_slip: $request->boolean('is_packing_slip'),
            date_format: $request->input('date_format'),
            developed_by: $request->input('developed_by'),
            invoice_format: $request->input('invoice_format'),
            state: $request->input('state'),
            expiry_type: $request->input('expiry_type'),
            expiry_value: (int)$request->input('expiry_value'),
            timezone: $request->input('timezone'),
            site_logo_path: $image
        );

    }
}
