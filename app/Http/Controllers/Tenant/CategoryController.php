<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CategoryRequest;
use App\Services\Tenant\CategoryService;
use App\Services\Tenant\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $importService;

    public function __construct(CategoryService $categoryService, ImportService $importService)
    {
        $this->categoryService = $categoryService;
        $this->importService = $importService;
    }

    /**
     * Display the category creation view with all available categories.
     * Fetches category data from the service layer and passes it to the view.
     *
     * @return View The response view displaying the category creation form.
     */
    public function index(): View
    {
        // Retrieve all categories with associated data
        $categories = $this->categoryService->getAllCategoriesWithData();

        // Return the category creation view with the retrieved data
        return view('Tenant.category.create', compact('categories'));
    }

    /**
     * Store a newly created category.
     * Validates incoming request data, passes it to the service layer, and redirects.
     * Handles errors by logging and returning an appropriate message.
     *
     * @param CategoryRequest $request The validated request data for category creation.
     * @return RedirectResponse Redirects to category index with success or error message.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            // Validate request data and create a new category using the service
            $category = $this->categoryService->createCategory($request->validated());

            // Redirect to category index with success message
            return redirect('category')->with('message', 'Category inserted successfully');
        } catch (\Exception $e) {
            // Handle the error, log it, and return an appropriate message
            return redirect()->back()
                ->withErrors(['message' => __('Failed to create category. Please try again.')])
                ->withInput();
        }
    }

    /**
     * Retrieve category details for editing.
     * Fetches category data along with its parent category if applicable.
     *
     * @param int $id The unique identifier of the category to edit.
     * @return object The category object containing its details.
     */
    public function edit($id)
    {
        // Retrieve category data from the database
        $lims_category_data = DB::table('categories')->where('id', $id)->first();

        // Retrieve parent category data if available
        $lims_parent_data = DB::table('categories')->where('id', $lims_category_data->parent_id)->first();
        if ($lims_parent_data) {
            // Assign parent category name to the category object
            $lims_category_data->parent = $lims_parent_data->name;
        }

        // Return category data for editing
        return $lims_category_data;
    }

    /**
     * Update an existing category.
     * Validates incoming request, updates the category using the service layer, and redirects.
     * Logs any errors encountered during execution.
     *
     * @param CategoryRequest $request The validated request data for updating the category.
     * @param Category $category The category object to be updated.
     * @return RedirectResponse Redirects to category index with success or error message.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            // Validate request data and update the category using the service layer
            $this->categoryService->updateCategory($category, $request->validated());

            // Redirect to category index with success message
            return redirect('category')->with('message', 'Category updated successfully');
        } catch (\Exception $e) {
            // Log the error and return an appropriate response
            Log::error('Failed to update category', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while updating the category.'));
        }
    }


    public function deleteBySelection(Request $request)
    {
        try {
            $this->categoryService->deleteCategories($request->input('categoryIdArray'));
            return response()->json('Category deleted successfully!');
        } catch (\Exception $e) {
            return response()->json('Error while deleted the Category,try again.');

        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->deleteCategory($id);
            return redirect('category')->with('not_permitted', __('Category deleted successfully!'));
        } catch (\Exception $e) {
            return redirect('category')->with('not_permitted', __('Error while deleted the data,try again.'));
        }
    }


}
