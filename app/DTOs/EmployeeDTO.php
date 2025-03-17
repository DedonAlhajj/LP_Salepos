<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class EmployeeDTO
{
    public function __construct(
        public string $employee_name,
        public string $name,
        public string $email,
        public ?string $password,
        public string $phone,
        public string $address,
        public string $city,
        public string $country,
        public ?int $department_id,
        public ?int $warehouse_id,
        public ?int $biller_id,
        public ?bool $is_active,
        public ?string $role_id,
        public ?string $staff_id,
        public ?bool $create_user,
        public ?UploadedFile $image,
    ) {}

    public function toArray(): array
    {
        return [
            'employee_name' => $this->employee_name,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone_number' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'department_id' => $this->department_id,
            'warehouse_id' => $this->warehouse_id,
            'biller_id' => $this->biller_id,
            'is_active' => $this->is_active,
            'role_id' => $this->role_id,
            'staff_id' => $this->staff_id,
            'create_user' => $this->create_user,
        ];
    }

    public static function fromRequest(array $data): self
    {
        // التحقق مما إذا كانت الصورة موجودة وتعيينها ككائن UploadedFile
        $image = isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile
            ? $data['image']
            : null;  // إذا لم تكن الصورة موجودة، قم بتعيينها كـ null

        return new self(
            employee_name: $data['employee_name'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            phone: $data['phone_number'],
            address: $data['address'],
            city: $data['city'],
            country: $data['country'],
            department_id: $data['department_id'],
            warehouse_id: $data['warehouse_id'],
            biller_id: $data['biller_id'],
            is_active: true,  // يمكن تعديل هذا بناءً على المدخلات أو المنطق لديك
            role_id: $data['role_id'],
            staff_id: $data['staff_id'],
            create_user: $data['user'],
            image: $image,  // تمرير كائن الصورة إذا كانت موجودة
        );
    }


}

