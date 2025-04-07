<?php

namespace App\DTOs;

namespace App\DTOs;

use App\Models\Customer;
use App\Models\User;

class GiftCardDTO
{
    public function __construct(
        public ?string $cardNo,
        public ?int $customerId,
        public ?int $userId,
        public int $createdBy,
        public float $expense,
        public float $amount,
        public string $expired_date,
        public ?array $recipient // ✅ إضافة المستلم (email & name)
    ) {}

    public static function fromRequest($request): self
    {
        $dto = new self(
            cardNo: $request->input('card_no'),
            customerId: $request->input('user') ? null : $request->input('customer_id'),
            userId: $request->input('user') ? $request->input('user_id') : null,
            createdBy: auth()->id(),
            expense: 0,
            amount: (float) $request->input('amount'),
            expired_date: $request->input('expired_date'),
            recipient: null // ✅ سيتم تعيينه لاحقًا
        );

        // ✅ تعيين بيانات المستلم تلقائيًا
        $dto->recipient = self::getRecipient($dto);

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'card_no' => $this->cardNo,
            'customer_id' => $this->customerId,
            'user_id' => $this->userId,
            'created_by' => $this->createdBy,
            'expense' => $this->expense,
            'amount' => $this->amount,
            'expired_date' => $this->expired_date,
            'email' => $this->recipient['email'] ?? null, // ✅ تضمين الإيميل
            'name' => $this->recipient['name'] ?? 'Unknown', // ✅ تضمين الاسم
        ];
    }

    /**
     * إرجاع بيانات المستلم بناءً على الـ user_id أو customer_id
     */
    private static function getRecipient(GiftCardDTO $dto): ?array
    {
        if ($dto->userId) {
            $user = User::find($dto->userId);
            return $user ? ['email' => $user->email, 'name' => $user->name] : null;
        } elseif ($dto->customerId) {
            $customer = Customer::find($dto->customerId);
            return $customer && $customer->email ? ['email' => $customer->email, 'name' => $customer->name] : null;
        }
        return null;
    }
}
