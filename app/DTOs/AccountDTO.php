<?php

namespace App\DTOs;

class AccountDTO
{
    public string $account_no;
    public ?int $accountId;
    public string $name;
    public ?float $initial_balance;
    public ?string $note;

    public function __construct(array $data)
    {
        $this->account_no = $data['account_no'];
        $this->accountId = $data['account_id'] ?? 0;
        $this->name = $data['name'];
        $this->initial_balance = isset($data['initial_balance']) ? (float) $data['initial_balance'] : 0.0;
        $this->note = $data['note'] ?? null;
    }
}
