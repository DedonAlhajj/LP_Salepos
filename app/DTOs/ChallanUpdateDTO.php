<?php

namespace App\DTOs;

class ChallanUpdateDTO
{
    public int $challanId;
    public array $cashList;
    public array $chequeList;
    public array $onlinePaymentList;
    public array $statusList;
    public array $deliveryChargeList;
    public string $closingDate;
    public int $closedById;

    public function __construct(array $data)
    {
        $this->challanId = $data['challan_id'];
        $this->cashList = $data['cash_list'] ?? [];
        $this->chequeList = $data['cheque_list'] ?? [];
        $this->onlinePaymentList = $data['online_payment_list'] ?? [];
        $this->statusList = $this->generateStatusList($data);
        $this->deliveryChargeList = $data['delivery_charge_list'] ?? [];
        $this->closingDate = now()->toDateString();
        $this->closedById = auth()->id();
    }

    private function generateStatusList(array $data): array
    {
        return array_map(fn($cash, $cheque, $online) =>
        (!$cash && !$cheque && !$online) ? 'Failed' : 'Delivered',
            $data['cash_list'] ?? [],
            $data['cheque_list'] ?? [],
            $data['online_payment_list'] ?? []
        );
    }
}

