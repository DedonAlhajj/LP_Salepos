<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CurrencyRequest;
use App\Services\Tenant\CurrencyService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Display the currency index page with currency data.
     *
     * This method retrieves the currency data for the authenticated user
     * and returns the view for the currency index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Authorize the user to access the 'currency-index' permission.
            $this->authorize('currency');
            // Get currency data for the logged-in user from the service
            $currency_all = $this->currencyService->getCurrencies();

            // Return the view with the currency data
            return view('Tenant.currency.index', compact('currency_all'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading currency data.')]);
        }
    }

    /**
     * Store new Currency data in the system.
     *
     * This method validates the incoming request data and stores the Currency record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param CurrencyRequest $request
     * @return RedirectResponse
     */
    public function store(CurrencyRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->currencyService->createCurrency($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Currency created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to create Currency, please try again.');
        }
    }

    /**
     * Update new Currency data in the system.
     *
     * This method validates the incoming request data and updates the Currency record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param CurrencyRequest $request
     * @return RedirectResponse
     */
    public function update(CurrencyRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->currencyService->updateCurrency($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Currency updated successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to update Currency, please try again.');
        }
    }

    /**
     * Delete a single Currency record by date and Currency ID.
     *
     * This method deletes the Currency record for a specific Currency on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the Currency with the specified date and Currency ID
            $this->currencyService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Currency deleted successfully');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }
}
