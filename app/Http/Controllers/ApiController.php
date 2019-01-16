<?php

namespace App\Http\Controllers;

use App\Libraries\Polyline;
use App\Promocode;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // If auth is needed update constructor method
    }

    public function active_promo_codes()
    {
        return response()->json(
            [
                'message' => 'Promo code fetched succesfully',
                'data' => Promocode::join('status', 'status.id', 'promo_codes.status_id')
                    ->join('events', 'events.id', 'promo_codes.event_id')
                    ->select('promo_codes.id', 'events.name as event', 'promo_codes.code', 'status.name', 'promo_codes.value', 'promo_codes.radius', 'promo_codes.expires_at', 'promo_codes.created_at', 'promo_codes.updated_at')
                    ->where('status_id', 1)
                    ->get(),
            ]
        );

    }
    public function all_promo_codes()
    {
        return response()->json(
            [
                'message' => 'Promo code fetched succesfully',
                'data' => Promocode::join('status', 'status.id', 'promo_codes.status_id')
                    ->join('events', 'events.id', 'promo_codes.event_id')
                    ->select('promo_codes.id', 'events.name as event', 'promo_codes.code', 'status.name', 'promo_codes.value', 'promo_codes.radius', 'promo_codes.expires_at', 'promo_codes.created_at', 'promo_codes.updated_at')
                    ->get(),
            ]
        );

    }

    public function redeem_promo_code(Request $request)
    {
        $this->validate($request, [
            // User details for authentication
            'origin_lat' => 'required|numeric',
            'origin_long' => 'required|numeric',
            'destination_lat' => 'required|numeric',
            'destination_long' => 'required|numeric',
            // Promo code details
            'promo_code' => 'required',
        ]);
        $promo_code = Promocode::join('status', 'status.id', 'promo_codes.status_id')
            ->join('events', 'events.id', 'promo_codes.event_id')
            ->select(
                'promo_codes.id',
                'events.name as event',
                'promo_codes.code',
                'status.name',
                'promo_codes.value',
                'promo_codes.radius',
                'promo_codes.expires_at',
                'promo_codes.created_at',
                'promo_codes.updated_at')
            ->where('code', $request->promo_code)
            ->first();
        $distance = $this->distance($request->origin_lat, $request->origin_long, $request->destination_lat, $request->destination_long);

        $points = [
            [$request->origin_lat, $request->origin_lon],
            [$request->destination_lat, $request->destination_long],
        ];

        $poly_line = Polyline::Encode($points);

        if ($distance <= $promo_code->radius) {
            return response()->json(
                [
                    'message' => 'Promo code is valid',
                    'promo_code' => $promo_code,
                    'polyline' => $poly_line,
                    'distance' => $distance,
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => 'Promo code is invalid',
                ]
            );
        }

    }

    // Helper function to find the distance between two points given lat and long
    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1609.344; //Return distance in meters
    }

}
