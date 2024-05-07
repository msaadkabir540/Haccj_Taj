<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\TrasabilityCategory;
use App\Models\TrasabilityProdType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrasabilityCatergoryController extends Controller
{

    public function addTrasabilityData(Request $request){
            // dd($request->all());
        try
        {
            DB::beginTransaction();
            $addTrasability = new TrasabilityCategory();
            $addTrasability->created_by  = $request->employeecode;
            $addTrasability->trasability_name  = $request->trasability_name;
            $addTrasability->trasability_type = $request->trasability_type;
            $addTrasability->image_name = $request->file('image') ? $request->file('image')->getClientOriginalName() : "";
            $addTrasability->image = $request->file('image') ? $request->file('image')->move('uploads/', $addTrasability->image_name) : "";
            $addTrasability->created_at = Carbon::now()->toDateTimeString();
            // $addTrasability->updated_at = Carbon::now()->toDateTimeString();
            $addTrasability->save();

            if(!$addTrasability) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    
    }

    public function getAllTrasabilityData(Request $request, $employeecode){

        $trasabilityData = TrasabilityCategory::whereDate('created_at', Carbon::today())->where('created_by', $employeecode)->get();

        $trasabilityProdData = TrasabilityProdType::get();

        // if (count($trasabilityData)) {
            return response()->json(['status' => true, 'message' => 'All Trasability Data', 'trasabilityData' => $trasabilityData, 'trasabilityProdData' => $trasabilityProdData ]);
        // }
        // else {
        //     return response()->json(['status' => true, 'message' => 'No Trasability Found', 'data' => "N/A" ]);
        // }

    }




    public function addTrasabilityProdType(Request $request){
        
        try
        {
            DB::beginTransaction();
            $addProdType = new TrasabilityProdType();
            $addProdType->product_type  = $request->product_type;
            $addProdType->employeecode  = $request->employeecode;
            $addProdType->created_at = Carbon::now()->toDateTimeString();
            // $addProdType->updated_at = Carbon::now()->toDateTimeString();;
            $addProdType->save();
            

            if(!$addProdType) {
                DB::rollback();
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Trasability Product Type Added']);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e]);
        }
    

    }


    public function getAllTrasabilityProd(Request $request){

        $trasabilityProdData = TrasabilityProdType::get();
        if (count($trasabilityProdData)) {
            return response()->json(['status' => true, 'message' => 'All Trasability Product Type Data', 'data' => $trasabilityProdData ]);
        }
        else {
            return response()->json(['status' => true, 'message' => 'No Trasability Product Type Found', 'data' => "N/A" ]);
        }

    }




}
