<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HolidayEditDTO
{
    public int    $id;
    public string $from_date;
    public string $to_date;
    public string $note;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->from_date = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $this->to_date = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');
        $this->note = $data['note'] ?? '';
    }

    public static function fromRequest(array $validatedData): self
    {
        return new self([
            'id'  => $validatedData['id'],
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'note' => $validatedData['note'] ?? '',
        ]);
    }

    public function toArray(): array
    {
        return [
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'note' => $this->note,
        ];
    }
}
