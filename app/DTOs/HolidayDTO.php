<?php

namespace App\DTOs;

use App\Models\Holiday;
use Illuminate\Support\Collection;

class HolidayDTO
{
    public int $id;
    public ?string $note = null;
    public string $from_date;
    public string $to_date;
    public int $user_id;
    public bool $is_approved;
    public string $created_at;
    public ?object $user = null; // بدلاً من UserDTO

    public function __construct(Holiday $holiday)
    {
        $this->id = $holiday->id;
        $this->note = $holiday->note;
        $this->from_date = $holiday->from_date;
        $this->to_date = $holiday->to_date;
        $this->user_id = $holiday->user_id;
        $this->is_approved = $holiday->is_approved;
        $this->created_at = $holiday->created_at;

        // حفظ بيانات المستخدم ككائن عادي بدون DTO
        if ($holiday->relationLoaded('user') && $holiday->user) {
            $this->user = (object) [
                'id' => $holiday->user->id,
                'name' => $holiday->user->name,
            ];
        }
    }

    public static function collection(Collection $holidays): array
    {
        return $holidays->map(fn ($holiday) => new self($holiday))->toArray();
    }
}
