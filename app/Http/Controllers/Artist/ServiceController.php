<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Validator;

class ServiceController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'price' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json([
                    'error' =>true,
                    'message' => $validator->errors()->toJson(),
                    'data' => []
                ], 400);
        }
        $service = new Service();
        $service->name = $request->name;
        $service->price =  $request->price;
        $service->artist_id =  auth()->user()->id;
        $service->save();
        return response()->json([
                'error' =>false,
                'message' => 'Service successfully created',
                'data' => $service
        ], 201);

     
        // $start_time = \Carbon\Carbon::parse($request->input('start_time'));
        // $finish_time = \Carbon\Carbon::parse($request->input('finish_time'));

        // $result = $start_time->diffInDays($finish_time, false);
       
    }

    public function update(Request $request,$id){
     
        $service = Service::where('id',$id)->first();
        $service->name = $request->name;
        $service->price =  $request->price;
        $service->discount_per =  $request->discount_per;
        $service->start_data =  $request->start_date;
        $service->end_date =  $request->end_date;
        
        $service->save();
        return response()->json([
                'error' =>false,
                'message' => 'Service successfully updated',
                'data' => $service
        ], 201);
    }

    public function index(){
     
        $services = Service::all();
        return response()->json([
                'error' =>false,
                'message' => '',
                'data' => $services
        ], 201);
    }

    public function destroy($id){
        
        $service = Service::where('id',$id)->first();
        $service->delete();
        return response()->json([
                'error' =>false,
                'message' => 'Service deleted successfully',
                'data' => []
        ], 201);
    }
}
