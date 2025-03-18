<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HolidayStoreDTO
{
    public int $user_id;
    public string $from_date;
    public string $to_date;
    public string $note;
    public bool $is_approved;

    public function __construct(array $data)
    {
        $this->user_id = $data['user_id'];
        $this->from_date = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $this->to_date = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');
        $this->note = $data['note'] ?? '';
        $this->is_approved = $data['is_approved'];
    }

    public static function fromRequest(array $validatedData): self
    {
        return new self([
            'user_id' => Auth::id(),
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'note' => $validatedData['note'] ?? '',
            'is_approved' => Gate::allows('account-index'),
        ]);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'note' => $this->note,
            'is_approved' => $this->is_approved,
        ];
    }
}
