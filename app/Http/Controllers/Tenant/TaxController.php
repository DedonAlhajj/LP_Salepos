<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TaxRequest;
use App\Imports\TaxImport;
use App\Services\Tenant\ImportService;
use App\Services\Tenant\TaxCalculatorService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Tax;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class TaxController extends Controller
{
    protected TaxCalculatorService $taxService;
    protected ImportService $importService;

    public function __construct(TaxCalculatorService $taxService,ImportService $importService)
    {
        $this->taxService = $taxService;
        $this->importService = $importService;
    }

    /**
     * Display the tax index page with tax data.
     *
     * This method retrieves the tax data for the authenticated user
     * and returns the view for the tax index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Authorize the user to access the 'tax-index' permission.
            $this->authorize('tax');
            // Get tax data for the logged-in user from the service
            $tax_all = $this->taxService->getTaxes();

            // Return the view with the tax data
            return view('Tenant.tax.create', compact('tax_all'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading tax data.')]);
        }
    }

    /**
     * Store new Tax data in the system.
     *
     * This method validates the incoming request data and stores the Tax record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param TaxRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(TaxRequest $request): JsonResponse|RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->taxService->createTax($request->validated());

            // Redirect back with a success message
            return $request->wantsJson()
                ? response()->json('Tax created successfully', 200)
                : redirect()->back()->with('message', 'Tax created successfully');

        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return $request->wantsJson()
                ? response()->json(['error' => 'Failed to create Tax, please try again.'], 500)
                : redirect()->back()->with('not_permitted', 'Failed to create Tax, please try again.');
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            // Return a Tax data in the response.
            return response()->json($this->taxService->edit($id));
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json('Failed to get Tax data!');
        } catch (\Exception $e) {
            return response()->json('Failed to get Tax data!');
        }
    }

    /**
     * Update new Tax data in the system.
     *
     * This method validates the incoming request data and updates the Tax record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param TaxRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function update(TaxRequest $request): JsonResponse|RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->taxService->updateTax($request->validated());

            // Redirect back with a success message
            return $request->wantsJson()
                ? response()->json('Tax updated successfully', 200)
                : redirect()->back()->with('message', 'Tax updated successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return $request->wantsJson()
                ? response()->json(['error' => 'Failed to updated Tax, please try again.'], 500)
                : redirect()->back()->with('not_permitted', 'Failed to updated Tax, please try again.');
        }
    }

    /**
     * Import Tax data from an uploaded file.
     *
     * @param Request $request The incoming HTTP request containing the file to import.
     * @return RedirectResponse Redirects back with a success or error message.
     *
     * This function utilizes the import service to process Tax data
     * from the uploaded file. In case of an error, it catches the exception and
     * returns an error message.
     */
    public function importTax(Request $request): RedirectResponse
    {
        try {
            $this->importService->import(TaxImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Delete multiple Tax by selection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected Tax IDs to the service for deletion.
            $this->taxService->deleteTax($request->input('taxIdArray'));

            // Return a success message in the response.
            return response()->json('Tax deleted successfully!');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($exception->getMessage());
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($e->getMessage());
        }
    }

    /**
     * Delete a single Tax record by date and Tax ID.
     *
     * This method deletes the Tax record for a specific Tax on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the Tax with the specified date and Tax ID
            $this->taxService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Tax deleted successfully');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }

}
