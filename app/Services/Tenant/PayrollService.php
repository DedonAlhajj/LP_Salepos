<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\DTOs\PayrollDTO;
use App\Mail\PayrollDetails;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Payroll;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class PayrollService
{
    protected SendMailAction $sendMailAction;

    public function __construct(SendMailAction $sendMailAction)
    {
        $this->sendMailAction = $sendMailAction;
    }



    /**
     * Retrieve payroll attendance data for a given user.
     *
     * @return array The payroll data including employees, accounts, and payroll records.
     * @throws Exception If data retrieval fails.
     */
    public function getAttendanceData($user): array
    {
        $cacheKey = 'payroll_data_' . $user->id;
        try {
            return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
                // Fetch employees and accounts lists
                $employees = Employee::select(['id', 'name'])->get();
                $accounts = Account::select(['id', 'name'])->get();

                // Fetch the latest general settings
                $generalSetting = DB::table('general_settings')->latest()->first();

                // Retrieve payroll records with necessary relationships and apply access control
                $payroll = Payroll::with(['employee:id,name', 'user:id,name', 'account:id,name'])
                    ->when(!$user->hasRole(['Admin', 'Owner']) && $generalSetting->staff_access == 'own', function ($query) use ($user) {
                        return $query->where('user_id', $user->id);
                    })
                    ->orderByDesc('id')
                    ->get();

                return [
                    'lims_employee_list' => $employees,
                    'lims_account_list' => $accounts,
                    'lims_payroll_all' => $payroll, // Payroll data array
                ];
            });
        } catch (\Exception $e) {
            // Log any errors that occur while fetching payroll data
            Log::error("Error fetching payroll data: " . $e->getMessage());
            throw new Exception($e);
        }
    }

    /**
     * Store payroll record in the database.
     *
     * @param array $data Validated payroll data.
     * @throws Exception If payroll creation fails.
     */
    public function storePayroll(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                // Create Payroll Data Transfer Object (DTO)
                $payrollDTO = new PayrollDTO(
                    Carbon::parse($data['created_at'])->format('Y-m-d'),
                    $data['employee_id'],
                    $data['account_id'],
                    $data['amount'],
                    $data['paying_method'],
                    $data['note'] ?? null,
                );

                // Perform batch insert for efficiency
                Payroll::insert($payrollDTO->toArray());

                // Prepare email data
                $mailData = $this->prepareMailData($payrollDTO->toArray());

                // Send payroll notification email
                return $this->sendMailAction->sendMail($mailData, PayrollDetails::class);
            });

            // Invalidate payroll cache for the authenticated user
            Cache::forget('payroll_data_' . Auth::user()->id);

        } catch (Exception $e) {
            // Log the error and throw an exception for failure
            Log::error('Failed to create Payroll: ' . $e->getMessage());
            throw new Exception('Failed to create Payroll: ' . $e->getMessage());
        }
    }

    /**
     * Prepare email data for payroll notification.
     *
     * @param array $data Payroll data.
     * @return array Email content including reference number, amount, employee name, email, and currency.
     */
    #[ArrayShape(['reference_no' => "mixed", 'amount' => "mixed", 'name' => "mixed", 'email' => "mixed", 'currency' => "mixed"])]
    private function prepareMailData(array $data): array
    {
        $employee = Employee::findOrFail($data['employee_id']);
        return [
            'reference_no' => $data['reference_no'],
            'amount' => $data['amount'],
            'name' => $employee->name,
            'email' => $employee->email,
            'currency' => config('currency'),
        ];
    }

    /**
     * Update an existing payroll record.
     *
     * @param array $data Payroll data containing updated fields.
     * @throws Exception If the update operation fails.
     */
    public function updatePayroll(array $data)
    {
        try {
            // Ensure the 'created_at' field is formatted correctly
            if (isset($data['created_at'])) {
                $data['created_at'] = date("Y-m-d", strtotime(str_replace("/", "-", $data['created_at'])));
            } else {
                $data['created_at'] = date("Y-m-d");
            }

            // Find the payroll record by its ID
            $payroll = Payroll::find($data['payroll_id']);

            // Update the payroll record with new data
            $payroll->update($data);

            // Clear payroll cache for the authenticated user
            Cache::forget('payroll_data_' . Auth::user()->id);

        } catch (Exception $e) {
            // Log the error and throw an exception
            Log::error('Failed to update Payroll: ' . $e->getMessage());
            throw new Exception('Failed to update Payroll: ' . $e->getMessage());
        }
    }

    /**
     * Delete multiple payroll records based on department IDs.
     *
     * @param array $departmentId List of payroll IDs to be deleted.
     * @return bool Returns true if deletion is successful.
     * @throws ModelNotFoundException If payroll records are not found.
     * @throws Exception If any other error occurs during deletion.
     */
    public function deletePayrolls(array $departmentId): bool
    {
        try {
            // Delete multiple payroll records by their IDs
            Payroll::whereIn('id', $departmentId)->delete();

            // Clear payroll cache for the authenticated user
            Cache::forget('payroll_data_' . Auth::user()->id);

            return true;
        } catch (ModelNotFoundException $e) {
            // Log and rethrow the exception if payroll records are not found
            Log::error('Payroll not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log and rethrow the exception for general errors
            Log::error('Error deleting Payroll: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a single payroll record by its ID.
     *
     * @param int $id The ID of the payroll record to be deleted.
     * @return bool Returns true if deletion is successful.
     * @throws ModelNotFoundException If the payroll record is not found.
     * @throws Exception If any other error occurs during deletion.
     */
    public function deletePayroll(int $id): bool
    {
        try {
            // Find and delete the payroll record by ID
            Payroll::findOrFail($id)->delete();

            // Clear payroll cache for the authenticated user
            Cache::forget('payroll_data_' . Auth::user()->id);

            return true;
        } catch (ModelNotFoundException $e) {
            // Log and rethrow the exception if payroll record is not found
            Log::error('Payroll not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            // Log and rethrow the exception for general errors
            Log::error('Error deleting Payroll: ' . $e->getMessage());
            throw $e;
        }
    }



}

