<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index()
    {
        $tracks = Track::orderBy('title')->get();

        return response()->json($tracks);
    }

    public function show($id)
    {
        $track = Track::findOrFail($id);

        return response()->json($track);
    }
}
