<?php

namespace App\DTOs;


class AttendanceStoreDTO
{
    public function __construct(
        public string  $date,
        public int     $employee_id,
        public int     $user_id,
        public string  $checkin,
        public ?string $checkout,
        public int     $status,
        public ?string $note,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'employee_id' => $this->employee_id,
            'user_id' => $this->user_id,
            'checkin' => $this->checkin,
            'checkout' => $this->checkout,
            'status' => $this->status,
            'note'  => $this->note,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
