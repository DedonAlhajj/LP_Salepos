<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TableRequest;
use App\Services\Tenant\TableService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TableController extends Controller
{
    protected TableService $tableService;

    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }


    /**
     * Display the table index page with table data.
     *
     * This method retrieves the table data for the authenticated user
     * and returns the view for the table index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Get table data for the logged-in user from the service
            $tableData = $this->tableService->getActiveTable();

            // Return the view with the table data
            return view('Tenant.table.index', compact('tableData'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading table data.')]);
        }
    }

    /**
     * Store new table data in the system.
     *
     * This method validates the incoming request data and stores the table record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param TableRequest $request
     * @return RedirectResponse
     */
    public function store(TableRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->tableService->storetable($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Table created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('error', 'Failed to create table, please try again.');
        }
    }

    /**
     * Update new table data in the system.
     *
     * This method validates the incoming request data and updates the table record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param TableRequest $request
     * @return RedirectResponse
     */
    public function update(TableRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->tableService->updateTable($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Table updated successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('error', 'Failed to update table, please try again.');
        }
    }

    /**
     * Delete a single table record by date and table ID.
     *
     * This method deletes the table record for a specific table on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param string $date
     * @param int $table_id
     * @return RedirectResponse
     */
    public function destroy(int $table_id): RedirectResponse
    {
        try {
            // Call the service to delete the table with the specified date and table ID
            $this->tableService->destroy($table_id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Table deleted successfully');
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => 'Failed to delete table. ' . $e->getMessage()]);
        }
    }

}
