<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\ProductsCategory;
use App\Models\ProductsProdName;
use App\Models\ProductsProdType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductsCatergoryController extends Controller
{

    public function addProductsData(Request $request){
            // dd($request->all());
        try
        {
            DB::beginTransaction();
            $addProducts = new ProductsCategory();
            $addProducts->created_by  = $request->employeecode;
            $addProducts->product_name  = $request->product_name;
            $addProducts->product_type = $request->product_type;
            $addProducts->image_name = $request->file('image') ? $request->file('image')->getClientOriginalName() : "";
            $addProducts->image = $request->file('image') ? $request->file('image')->move('uploads/', $addProducts->image_name) : "";
            $addProducts->created_at = Carbon::now()->toDateTimeString();
            // $addProducts->updated_at = Carbon::now()->toDateTimeString();
            $addProducts->save();

            if(!$addProducts) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Products Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }

    public function getAllProductsData(Request $request) {
        // Retrieve the employee record based on the provided employeecode
        $employee = Employees::where('employeecode', $request->employeecode)->first();
    
        // Initialize the productData query
        $productsData = ProductsCategory::query();
    
        // Check if the employee record exists and if the employee is an admin
        if ($employee && $employee->isadmin == 1) {
            // If the employee is an admin, fetch all product data
            $productsData->select('id', 'product_name', 'product_type', 'image', 'image_name', 'created_at', 'created_by');
        } else {
            // If the employee is not an admin, fetch only the employee's product data
            $productsData->where('created_by', $request->employeecode)
                            ->select('id', 'product_name', 'product_type', 'image', 'image_name', 'created_at', 'created_by');
        }
    
        // Apply date filters if provided
        if ($request->date) {
            $date = date('Y-m-d', strtotime($request->date));
            if ($request->edate) {
                $edate = date('Y-m-d', strtotime($request->edate));
                $productsData->whereRaw("DATE(created_at) BETWEEN '$date' AND '$edate'");
            } else {
                $productsData->whereRaw("DATE(created_at) = '$date'");
            }
        } else {
            if (!$request->date && !$request->employee) {
                $productsData->whereDate('created_at', today());
            }
        }
    
        // Apply employeecode filter if provided
        if ($request->employee) {
            $productsData->where('created_by', $request->employee);
        }
    
        // Continue building your query as needed
    
        // Finally, retrieve the data and return the response
        $productsData = $productsData->orderBy('created_at', 'DESC')->get();
    
        $productsProdData = ProductsProdType::get();
        $productsProdNameData = ProductsProdName::get();

        // if (count($trasabilityData)) {
            return response()->json(['status' => true, 'message' => 'All Products Data', 'productsData' => $productsData , 'productsProdData' => $productsProdData , 'productsProdNameData' => $productsProdNameData ]);
        // }
        // else {
        //     return response()->json(['status' => true, 'message' => 'No Trasability Found', 'data' => "N/A" ]);
        // }
        

    }
    
    public function updateProductData(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();

            $updateProduct = ProductsCategory::find($id);

            if (!$updateProduct) {
                return response()->json(['status' => false, 'message' => 'Product data not found']);
            }

            // Update the attributes based on the request data
            $updateProduct->updated_by = $request->employeecode;
            $updateProduct->product_name = $request->product_name;
            $updateProduct->product_type = $request->product_type;
            $updateProduct->updated_at = Carbon::now()->toDateTimeString();
            $updateProduct->save();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Product data updated']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteProduct($id)
    {
        try {
            DB::beginTransaction();

            $Product = ProductsCategory::find($id);

            if (!$Product) {
                return response()->json(['status' => false, 'message' => 'Product data not found']);
            }

            $Product->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Product data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }


    public function addProductsProdType(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addProdType = new ProductsProdType();
            $addProdType->product_type  = $request->product_type;
            $addProdType->employeecode  = $request->employeecode;
            $addProdType->created_at = Carbon::now()->toDateTimeString();
            // $addProdType->updated_at = Carbon::now()->toDateTimeString();;
            $addProdType->save();
            

            if(!$addProdType) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Productivity Product Type Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    

    }

    public function getAllProductsProdType(Request $request){

        $ProductsProdData = ProductsProdType::get();
        if (count($ProductsProdData)) {
            return response()->json(['status' => true, 'message' => 'All Productivity Product Type Data', 'data' => $ProductsProdData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Productivity Product Type Found', 'data' => "N/A" ]);
        }

    }

    public function deleteProductsProdType($id)
    {
        try {
            DB::beginTransaction();

            $ProductsProd = ProductsProdType::find($id);

            if (!$ProductsProd) {
                return response()->json(['status' => false, 'message' => 'Productivity Product data not found']);
            }

            $ProductsProd->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Productivity Product data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }





    public function addProductProdName(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addProdName = new ProductsProdName();
            $addProdName->product_name  = $request->product_name;
            $addProdName->employeecode  = $request->employeecode;
            $addProdName->created_at = Carbon::now()->toDateTimeString();
            $addProdName->save();
            

            if(!$addProdName) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Productivity Product Name Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    

    }

    public function getAllProductProdName(Request $request){

        $productsProdData = ProductsProdName::get();
        if (count($productsProdData)) {
            return response()->json(['status' => true, 'message' => 'All Productivity Product Name Data', 'data' => $productsProdData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Productivity Product Name Found', 'data' => "N/A" ]);
        }

    }

    public function deleteProductProdName($id)
    {
        try {
            DB::beginTransaction();

            $productivityProd = ProductsProdName::find($id);

            if (!$productivityProd) {
                return response()->json(['status' => false, 'message' => 'Productivity Product Name data not found']);
            }

            $productivityProd->delete();

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Productivity Product Name data deleted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }


}
