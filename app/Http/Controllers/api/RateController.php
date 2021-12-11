<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\rating;

class RateController extends Controller
{
    //
    public function getRate()
    {
        //get Rates
        $getRates=rating::all();

        return response()->json($getRates, 200);

    }

    //
    public function SaveRate(Request $request)
    {

        //validate inputs 
        $validate = Validator::make(request()->all(), [
            'CompNameI'=>'required',
            'CompDescI'=>'required',
            'RateValI'=>"required",
        ]);

        if ($validate->fails()) {
            return response()->json(['code'=>400,'message'=>'Validation Error','status'=>false,'item'=>null],400);
        }

        //comp_name	value	desc	user_id

        //Save Rate
        $SaveRate=new rating();
        $SaveRate->comp_name=$request->input('CompNameI');
        $SaveRate->value=$request->input('RateValI');
        $SaveRate->desc=$request->input('CompDescI');

        $rate=$SaveRate->save();

        return response()->json($SaveRate, 201);

    }
}
