<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositService
{

    protected SendMailAction $sendMailAction;

    public function __construct(SendMailAction $sendMailAction)
    {
        $this->sendMailAction = $sendMailAction;
    }

    public function getDepositsByCustomerId($customerId)
    {
        $deposits = Deposit::with('user:id,name,email') // Eager Loading لتحسين الأداء
        ->where('customer_id', $customerId)
            ->get();

        // مصفوفات لتخزين القيم كما كان في الكود القديم
        $depositIds = [];
        $dates = [];
        $amounts = [];
        $notes = [];
        $names = [];
        $emails = [];

        foreach ($deposits as $deposit) {
            $depositIds[] = $deposit->id;
            $dates[] = $deposit->created_at->format('Y-m-d H:i:s');
            $amounts[] = $deposit->amount;
            $notes[] = $deposit->note;
            $names[] = $deposit->user->name ?? 'N/A';
            $emails[] = $deposit->user->email ?? 'N/A';
        }

        // التحقق من أن هناك بيانات قبل الإرجاع
        return !empty($depositIds) ? [$depositIds, $dates, $amounts, $notes, $names, $emails] : [];
    }

    public function addDeposit(array $data)
    {
        try{
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);
            $customer->increment('deposit', $data['amount']);

            $data['name'] = $customer->name;
            $data['email'] = $customer->email;
            $data['balance'] = $customer->deposit - $customer->expense;
            $data['currency'] = config('app.currency', 'USD');

            if (!$this->sendMailAction->execute($data, CustomerDeposit::class)) {
                $message = __('User created successfully. Please setup your mail settings to send mail.');
            } else {
                $message = __(' created successfully.');
            }

             Deposit::create([
                'customer_id' => $data['customer_id'],
                'amount'      => $data['amount'],
                'note'        => $data['note'] ?? null,
                'user_id'     => Auth::id(),
            ]);
            return $message;
        });
        } catch (Exception $e) {
            Log::error("Deposit creation failed: " . $e->getMessage());
            throw new \RuntimeException(__('Unable to process deposit at this moment. Please try again later.'));
        }
    }

    public function updateDeposit(array $data)
    {
        try{
        return DB::transaction(function () use ($data) {
            $deposit = Deposit::findOrFail($data['deposit_id']);
            $customer = Customer::findOrFail($deposit->customer_id);

            $amountDiff = $data['amount'] - $deposit->amount;
            $customer->increment('deposit', $amountDiff);

            return $deposit->update([
                'amount' => $data['amount'],
                'note'   => $data['note'] ?? $deposit->note,
            ]);
        });
        } catch (Exception $e) {
            Log::error("Deposit updation failed: " . $e->getMessage());
            throw new \RuntimeException(__('Unable to process deposit at this moment. Please try again later.'));
        }
    }

    public function deleteDeposit($depositId)
    {
        try{
        return DB::transaction(function () use ($depositId) {
            $deposit = Deposit::findOrFail($depositId);
            $customer = Customer::findOrFail($deposit->customer_id);

            $customer->decrement('deposit', $deposit->amount);
            return $deposit->delete();
        });
        } catch (Exception $e) {
            Log::error("Deposit deletion failed: " . $e->getMessage());
            throw new \RuntimeException(__('Unable to process deposit at this moment. Please try again later.'));
        }
    }
}
