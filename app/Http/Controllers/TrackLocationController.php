<?php

namespace App\Http\Controllers;

use App\Events\SendLocationEvent;
use Illuminate\Http\Request;

class TrackLocationController extends Controller
{
    public function index()
    {
        return view('track');
    }

    public function liveLocation()
    {
        return view('live-location');
    }
    public function mapPlot()
    {
        return view('map-plot');
    }

    public function updateLocation(Request $request)
    {
        broadcast(new SendLocationEvent($request->latitude, $request->longitude));
        return response()->json([
            'message' => "Send Location ok",
        ]);
    }
}
