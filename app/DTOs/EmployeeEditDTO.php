<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class EmployeeEditDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone_number,
        public string $address,
        public string $city,
        public string $country,
        public ?int $department_id,
        public int $employee_id,
        public ?string $staff_id,
        public ?UploadedFile $image,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'department_id' => $this->department_id,
            'staff_id' => $this->staff_id,
        ];
    }

    public static function fromRequest(array $data): self
    {
        // التحقق مما إذا كانت الصورة موجودة وتعيينها ككائن UploadedFile
        $image = isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile
            ? $data['image']
            : null;  // إذا لم تكن الصورة موجودة، قم بتعيينها كـ null

        return new self(
            name: $data['name'],
            email: $data['email'],
            phone_number: $data['phone_number'],
            address: $data['address'],
            city: $data['city'],
            country: $data['country'],
            department_id: $data['department_id'],
            employee_id: $data['employee_id'],
            staff_id: $data['staff_id'],
            image: $image,  // تمرير كائن الصورة إذا كانت موجودة
        );
    }


}

