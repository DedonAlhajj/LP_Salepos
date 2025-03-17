<?php

namespace App\Services\Tenant;

use App\DTOs\EmployeeDTO;
use App\DTOs\EmployeeEditDTO;
use App\Models\Biller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeServices
{
    protected DepartmentService $departmentService;
    protected UserService $userService;
    protected MediaService $mediaService;

    public function __construct(
        DepartmentService $departmentService,
        UserService $userService,
        MediaService $mediaService)
    {
        $this->departmentService = $departmentService;
        $this->userService = $userService;
        $this->mediaService = $mediaService;
    }

    /**
     * Get all active employees (without soft deletes).
     *
     * @return Collection|\Illuminate\Support\Collection|array
     */
    public function getActiveEmployees(): array|Collection|\Illuminate\Support\Collection
    {
        // Retrieve all active employees excluding those marked as deleted.
        return Employee::withoutTrashed()->get();
    }

    /**
     * Get the total number of employees.
     *
     * @return int
     */
    private function numberOfEmployee(): int
    {
        // Query the database to count the total number of employees.
        return DB::table('employees')->select(DB::raw('count(*) as total'))->value('total');
    }

    /**
     * Fetch employee data with caching.
     *
     * @return array
     */
    public function index(): array
    {
        // Cache the employee data for 10 minutes to improve performance.
        return Cache::remember('employee_data', now()->addMinutes(10), function () {
            return [
                // Get active employees.
                'employees' => $this->getActiveEmployees(),
                // Get active departments.
                'departments' => $this->departmentService->getActiveDepartment(),
                // Get the total number of employees.
                'numberOfEmployee' => $this->numberOfEmployee(),
            ];
        });
    }

    /**
     * Get data required for creating a new employee.
     *
     * @return array
     */
    public function create()
    {
        // Return data needed for creating a new employee, such as roles, billers, warehouses, users, and departments.
        return [
            // Active roles.
            'roles' => Role::active()->get(['id', 'name']),
            // Biller data.
            'billers' => Biller::get(['id', 'name', 'phone_number']),
            // Warehouse data.
            'warehouses' => Warehouse::get(['id', 'name']),
            // Total number of users.
            'users' => User::count(),
            // Active departments.
            'departments' => $this->departmentService->getActiveDepartment(),
            // List of active employees.
            'numberOfEmployee' => $this->getActiveEmployees(),
        ];
    }

    /**
     * Create a new employee along with their user record if needed.
     *
     * @param EmployeeDTO $dto
     * @throws Exception
     */
    public function createEmployee(EmployeeDTO $dto)
    {
        try {
            // Use DB transaction to ensure atomicity of the employee creation process.
            $employee = DB::transaction(function () use ($dto) {
                $userId = null;

                // If the employee requires a user to be created, call createUser method.
                if ($dto->create_user) {
                    $user = $this->createUser($dto);
                    $userId = $user?->id;
                }

                // Create the employee record.
                // إذا كانت الصورة موجودة، إضافة الصورة إلى الموظف
//                if ($dto->image) {
//                    $this->mediaService->addDocument($employee,$dto->toArray()->image,"employees");
//                }

                // Return the created employee.
                return Employee::create([
                    'name' => $dto->employee_name,
                    'email' => $dto->email,
                    'phone_number' => $dto->phone,
                    'address' => $dto->address,
                    'city' => $dto->city,
                    'country' => $dto->country,
                    'department_id' => $dto->department_id,
                    'staff_id' => $dto->staff_id,
                    'user_id' => $userId, // Associate user if created.
                ]);
            });

            // Clear the cached employee data after a successful creation.
            Cache::forget('employee_data');

        } catch (Exception $e) {
            // Log and rethrow the exception if employee creation fails.
            Log::error('Failed to create employee: ' . $e->getMessage());
            throw new Exception('Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Create a user for the employee.
     *
     * @param EmployeeDTO $dto
     * @return User
     * @throws Exception
     */
    private function createUser(EmployeeDTO $dto): User
    {
        // Ensure that both email and password are provided to create a user.
        if (!$dto->email || !$dto->password) {
            throw new Exception("Email and password are required to create a user.");
        }

        // Create the user record using the user service and DTO data.
        $user = $this->userService->createUserRecord([
            ...$dto->toArray(), // Convert the DTO to an array.
            'is_active' => $dto->is_active ?? true, // Set user as active if not specified.
            'role' => $dto->role_id, // Assign the role to the user.
        ]);

        // Return the created user.
        return $user;
    }

    /**
     * Update an existing employee's details.
     *
     * @param EmployeeEditDTO $dto Data transfer object containing updated employee details.
     * @param int $employeeId The ID of the employee to be updated.
     * @return bool Returns true if the update was successful.
     * @throws Exception If the update fails.
     */
    public function updateEmployee(EmployeeEditDTO $dto, $employeeId): bool
    {
        DB::beginTransaction();

        try {
            // Find the employee by ID or fail
            $employee = Employee::findOrFail($dto->employee_id);

            // Update the employee's data
            $employee->update($dto->toArray());

            // If a new image is provided, replace the existing one
            //if ($dto->image) {
            //    $this->mediaService->uploadDocumentWithClear($employee, $dto->image, 'employees');
            //}

            // Commit the transaction and clear cache
            DB::commit();
            Cache::forget('employee_data');

            return true;

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            Log::error('Error updating employee: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete multiple employees based on an array of IDs.
     *
     * @param array $departmentId List of employee IDs to be deleted.
     * @return bool Returns true if deletion was successful.
     * @throws ModelNotFoundException If the employees are not found.
     * @throws Exception If an error occurs during deletion.
     */
    public function deleteEmployees(array $departmentId): bool
    {
        try {
            // Retrieve all employees with the given IDs
            $employees = Employee::whereIn('id', $departmentId)->get();

            foreach ($employees as $employee) {
                // If the employee has an associated user, delete the user
                if ($employee->user_id) {
                    User::find($employee->user_id)?->delete();
                }
                // Delete employee-related media
                $this->mediaService->deleteDocument($employee, 'employees');
            }

            // Delete employees from the database
            Employee::whereIn('id', $departmentId)->delete();

            // Clear cache after deletion
            Cache::forget('employee_data');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Department not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            Log::error('Error deleting Department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a single employee by ID.
     *
     * @param int $id The ID of the employee to be deleted.
     * @return bool Returns true if deletion was successful.
     * @throws ModelNotFoundException If the employee is not found.
     * @throws Exception If an error occurs during deletion.
     */
    public function deleteEmployee(int $id): bool
    {
        try {
            // Find the employee by ID or fail
            $employee = Employee::findOrFail($id);

            // If the employee has an associated user, delete the user
            if ($employee->user_id) {
                User::find($employee->user_id)?->delete();
            }

            // Delete employee-related media
            //$this->mediaService->deleteDocument($employee, 'employees');

            // Delete the employee record
            $employee->delete();

            // Clear cache after deletion
            Cache::forget('employee_data');

            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Employee not found: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            Log::error('Error deleting Employee: ' . $e->getMessage());
            throw $e;
        }
    }


}
