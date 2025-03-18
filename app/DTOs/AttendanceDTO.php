<?php

namespace App\DTOs;

use JetBrains\PhpStorm\ArrayShape;

class AttendanceDTO
{
    public function __construct(
        public string $date,
        public string $employee_name,
        public string $checkin_checkout,
        public string $status,
        public string $user_name,
        public int $employee_id
    ) {}

    #[ArrayShape(['date' => "string", 'employee_name' => "string", 'checkin_checkout' => "string", 'status' => "string", 'user_name' => "string", 'employee_id' => "int"])] public function toArray(): array
    {
        return [
            'date' => $this->date,
            'employee_name' => $this->employee_name,
            'checkin_checkout' => $this->checkin_checkout,
            'status' => $this->status,
            'user_name' => $this->user_name,
            'employee_id' => $this->employee_id,
        ];
    }
}
