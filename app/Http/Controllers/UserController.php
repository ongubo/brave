<?php

namespace App\Http\Controllers;

use App\Event;
use App\Promocode;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;

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
            'promo_code' => 'required|unique:promo_codes,code',
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
            // Use transaction so that iff a promocode is not created an event is also not created
            DB::transaction(function () use ($request){
                // Create event
                $event = new Event();
                $event->name = $request->event_name;
                $event->description = $request->event_description;
                $event->lat = $request->lat;
                $event->long = $request->long;
                $event->starts_at = Carbon::parse($request->event_date); //'2016-12-20 10:26'
                $event->save();

                // Create promo code for event
                $promo_code = new Promocode();
                $promo_code->event_id = $event->id;
                $promo_code->status_id = 1;
                $promo_code->code = $request->promo_code;
                $promo_code->radius = $request->promo_code_radius;
                $promo_code->value = $request->promo_code_value;
                $promo_code->expires_at = Carbon::parse($request->promo_code_expiry);
                $promo_code->save();
                //
            });

            return response()->json(['message' => 'Event succesfully created']);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

    }

    public function deactivate_promo_code(Request $request)
    {
        $this->validate($request, [
            // User detaiils for authentication
            'email' => 'required',
            'password' => 'required',
            // Promo code details
            'promo_code_id' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // deactivate promocode
            $promo_code = Promocode::find($request->promo_code_id);
            if ($promo_code) {
                $promo_code->status_id = 4;
                $promo_code->save();
                return response()->json(['message' => 'Promo code succesfully deactivated']);
            } else {
                return response()->json(['message' => 'Promocode not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

    }
        public function update_promo_code_radius(Request $request)
    {
        $this->validate($request, [
            // User detaiils for authentication
            'email' => 'required',
            'password' => 'required',
            // Promo code details
            'promo_code_id' => 'required',
            'promo_code_radius' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // update promo code radius
            $promo_code = Promocode::find($request->promo_code_id);
            if ($promo_code) {
                $promo_code->radius = $request->promo_code_radius;
                $promo_code->save();
                return response()->json(['message' => 'Promo code radius succesfully updated']);
            } else {
                return response()->json(['message' => 'Promocode not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

    }
}
