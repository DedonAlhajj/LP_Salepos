<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CategoryRequest;
use App\Services\Tenant\CategoryService;
use App\Services\Tenant\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use App\Traits\CacheForget;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $importService;

    public function __construct(CategoryService $categoryService,ImportService $importService)
    {
        $this->categoryService = $categoryService;
        $this->importService = $importService;
    }
    public function index()
    {
        $categories = $this->categoryService->getAllCategoriesWithData();
        return view('Tenant.category.create', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());
            return redirect('category')->with('message', 'Category inserted successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message' => __('Failed to create category. Please try again.')])
                ->withInput();
        }

    }

    public function edit($id)
    {
        $lims_category_data = DB::table('categories')->where('id', $id)->first();
        $lims_parent_data = DB::table('categories')->where('id', $lims_category_data->parent_id)->first();
        if($lims_parent_data){
            $lims_category_data->parent = $lims_parent_data->name;
        }
        return $lims_category_data;
    }

    public function update(CategoryRequest $request, Category $category)
    {

        try {

            $this->categoryService->updateCategory($category, $request->validated());
            return redirect('category')->with('message', 'Category updated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to update customer', ['error' => $e->getMessage()]);
            return redirect()->back()->with('message', __('An error occurred while updating the customer.'));
        }
    }

    public function import(Request $request)
    {
        //get file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');
        $filename =  $upload->getClientOriginalName();
        $filePath=$upload->getRealPath();
        //open and read
        $file=fopen($filePath, 'r');
        $header= fgetcsv($file);
        $escapedHeader=[];
        //validate
        foreach ($header as $key => $value) {
            $lheader=strtolower($value);
            $escapedItem=preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }
        //looping through othe columns
        while($columns=fgetcsv($file))
        {
            if($columns[0]=="")
                continue;
            foreach ($columns as $key => $value) {
                $value=preg_replace('/\D/','',$value);
            }
            $data= array_combine($escapedHeader, $columns);
            $category = Category::firstOrNew(['name' => $data['name'], 'is_active' => true ]);
            if($data['parentcategory']){
                $parent_category = Category::firstOrNew(['name' => $data['parentcategory'], 'is_active' => true ]);
                $parent_id = $parent_category->id;
            }
            else
                $parent_id = null;

            $category->parent_id = $parent_id;
            $category->is_active = true;
            $category->save();
        }
        $this->cacheForget('category_list');
        return redirect('category')->with('message', 'Category imported successfully');
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

    /*public function deleteBySelection1(Request $request)
    {
        $category_id = $request['categoryIdArray'];
        foreach ($category_id as $id) {
            $lims_product_data = Product::where('category_id', $id)->get();
            foreach ($lims_product_data as $product_data) {
                $product_data->is_active = false;
                $product_data->save();
            }
            $lims_category_data = Category::findOrFail($id);
            $lims_category_data->is_active = false;
            $lims_category_data->save();

            $this->fileDelete('images/category/', $lims_category_data->image);
            $this->fileDelete('images/category/icons', $lims_category_data->icon);
        }
        $this->cacheForget('category_list');
        return 'Category deleted successfully!';
    }

    public function destroy1($id)
    {
        $lims_category_data = Category::findOrFail($id);
        $lims_category_data->is_active = false;
        $lims_product_data = Product::where('category_id', $id)->get();
        foreach ($lims_product_data as $product_data) {
            $product_data->is_active = false;
            $product_data->save();
        }

        $this->fileDelete('images/category/', $lims_category_data->image);
        $this->fileDelete('images/category/icons', $lims_category_data->icon);

        $lims_category_data->save();
        $this->cacheForget('category_list');
        return redirect('category')->with('not_permitted', 'Category deleted successfully');
    }*/
}
