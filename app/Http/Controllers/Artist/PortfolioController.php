<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
class PortfolioController extends Controller
{
    public function store(Request $request){
        $date = new DateTime();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
        ]);
        if($validator->fails()){
            return response()->json([
                    'error' =>true,
                    'message' => $validator->errors()->toJson(),
                    'data' => []
                ], 400);
        }
        
        $portfolio = new Portfolio();
        $portfolio->artist_id =  auth()->user()->id;
        $portfolio->name = $request->name;
        if ($request->file('picture')) {
            $imageName = $date->format('Y-m-dH:i:s.v')."{$request->file('picture')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('portfolio', $request->file('picture'), $imageName);
            $portfolio->picture = Storage::disk('public')->url("portfolio/$imageName");
        }
        $portfolio->save();
        return response()->json([
                'error' =>false,
                'message' => 'Portfolio successfully created',
                'data' => $portfolio
        ], 201);

       
    }
}
