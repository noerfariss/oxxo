<?php

namespace App\Http\Controllers\Api;

use App\Class\ResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MapBoxController extends Controller
{
    public function getAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $token = env('MAPBOX_TOKEN');

        try {
            $req = Http::get('https://api.mapbox.com/search/geocode/v6/reverse?', [
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'access_token' => $token
            ])
                ->object();

            $features = $req->features[0];
            $address = $features->properties->full_address;
            return ResponseClass::success(data: [
                'address' => $address
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return ResponseClass::error();
        }
    }
}
