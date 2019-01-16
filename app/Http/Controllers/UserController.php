<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\User;
use App\Promocode;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function create_event(Request $request)
    {
        // Validate Input
        $this->validate($request, [
            // User detaiils for authentication
            'email' => 'required',
            'password' => 'required',
            // Promo code details
            'promo_code_expiry' => 'required|date',
            'promo_code' => 'required',
            'promo_code_radius' => 'required|numeric',
            'promo_code_value' => 'required|numeric',
            // Event details
            'event_name' => 'required|unique:events,name',
            'event_description' => 'required',
            'event_date' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);

        // Authenticate user
        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            // Create event
            $event = new Event();
            $event->name = $request->event_name;
            $event->description = $request->event_description;
            $event->lat = $request->lat;
            $event->long = $request->long;
            $event->starts_at = Carbon::parse($request->event_date);//'2016-12-20 10:26'
            $event->save();

            // Create promo code for event
            $promo_code = new Promocode();
            $promo_code->event_id = $event->id;
            $promo_code->code = $request->promo_code;
            $promo_code->radius = $request->promo_code_radius;
            $promo_code->value = $request->promo_code_value;
            $promo_code->expires_at = Carbon::parse($request->promo_code_expiry);
            $promo_code->save();
            
            return response()->json(['status' => 'Event succesfully created']);
        } else {
            return response()->json(['status' => 'Invalid credentials'], 401);
        }

    }
}
