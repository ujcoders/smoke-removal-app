<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmokeController extends Controller
{
    public function removeSmoke(Request $request)
    {
        // Get the direction of hand movement (left or right)
        $direction = $request->input('direction');

        // Return success if valid direction
        if ($direction === 'left' || $direction === 'right') {
            return response()->json(['success' => true, 'message' => 'Smoke is being removed']);
        }

        // Return error for invalid input
        return response()->json(['success' => false, 'message' => 'Invalid direction']);
    }
}
