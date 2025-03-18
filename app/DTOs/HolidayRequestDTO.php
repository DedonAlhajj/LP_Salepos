<?php

namespace App\DTOs;

class HolidayRequestDTO
{
    public int $year;
    public int $month;
    public int $user_id;

    public function __construct(int $year, int $month, int $user_id)
    {
        $this->year = $year;
        $this->month = $month;
        $this->user_id = $user_id;
    }
}

