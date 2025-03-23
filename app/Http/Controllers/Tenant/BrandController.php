<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\BrandRequest;
use App\Imports\BrandImport;
use App\Services\Tenant\BrandService;
use App\Services\Tenant\ImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
   protected BrandService $brandService;
   protected ImportService $importService;

   public function __construct(BrandService $brandService,ImportService $importService)
   {
       $this->brandService = $brandService;
       $this->importService = $importService;
   }

    /**
     * Display the Brand index page with Brand data.
     *
     * This method retrieves the Brand data for the authenticated user
     * and returns the view for the Brand index page.
     * If there is an error fetching the data, an error message is displayed.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Get Brand data for the logged-in user from the service
            $brand_all = $this->brandService->getBrandsWithoutTrashed();

            // Return the view with the Brand data
            return view('Tenant.brand.create', compact('brand_all'));
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['not_permitted' => __('An error occurred while loading Brand data.')]);
        }
    }

    /**
     * Store new Brand data in the system.
     *
     * This method validates the incoming request data and stores the Brand record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param BrandRequest $request
     * @return RedirectResponse
     */
    public function store(BrandRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->brandService->createBrand($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Brand created successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to create Brand, please try again.');
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        try {
            // Return a Brand data in the response.
            return response()->json($this->brandService->edit($id));
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json('Failed to get brand data!');
        } catch (\Exception $e) {
            return response()->json('Failed to get brand data!');
        }
    }

    /**
     * Update new Brand data in the system.
     *
     * This method validates the incoming request data and updates the Brand record.
     * If the process is successful, a success message is displayed.
     * If there is an error during the process, an error message is shown.
     *
     * @param BrandRequest $request
     * @return RedirectResponse
     */
    public function update(BrandRequest $request): RedirectResponse
    {
        try {
            // Pass the validated request data to the service for storage
            $this->brandService->updateBrand($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Brand updated successfully');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->with('not_permitted', 'Failed to update Brand, please try again.');
        }
    }

    /**
     * Import Brand data from an uploaded file.
     *
     * @param Request $request The incoming HTTP request containing the file to import.
     * @return RedirectResponse Redirects back with a success or error message.
     *
     * This function utilizes the import service to process Brand data
     * from the uploaded file. In case of an error, it catches the exception and
     * returns an error message.
     */
    public function importBrand(Request $request): RedirectResponse
    {
        try {
            $this->importService->import(BrandImport::class, $request->file('file'));
            return redirect()->back()->with('message', __('Data imported successfully, data will be processed in the background.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('not_permitted', $e->getMessage());
        }
    }

    /**
     * Delete multiple Brand by selection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Pass the selected Brand IDs to the service for deletion.
            $this->brandService->deleteBrand($request->input('brandIdArray'));

            // Return a success message in the response.
            return response()->json('Brand deleted successfully!');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($exception->getMessage());
        } catch (\Exception $e) {
            // Handle any exceptions and provide feedback for failed deletion.
            return response()->json($e->getMessage());
        }
    }

    /**
     * Delete a single Brand record by date and Brand ID.
     *
     * This method deletes the Brand record for a specific Brand on a specific date.
     * If successful, a success message is displayed. If an error occurs, an error message is shown.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service to delete the Brand with the specified date and Brand ID
            $this->brandService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Brand deleted successfully');
        }catch (ModelNotFoundException $exception){
            // Handle any exceptions and provide feedback for failed deletion.
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (\Exception $e) {
            // Handle any exceptions and redirect back with a failure message
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }


}
