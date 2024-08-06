<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\SolarCalculator;

class ApiController extends Controller
{
    protected $solarCalculator;

    public function __construct(SolarCalculator $solarCalculator)
    {
        $this->solarCalculator = $solarCalculator;
    }

    public function calculate(Request $request)
    {
        
        $request->validate([
            'solar_hours' => 'required|numeric',
            'electricity' => 'required|numeric',
            'panel_width' => 'nullable|numeric',
            'panel_length' => 'nullable|numeric',
            'power' => 'required|integer|in:1300,2200,3500'
        ]);

        try {
            $solar_hours = (float) $request->input('solar_hours');
            $electricity = (float) $request->input('electricity');
            $panel_width = (float) $request->input('panel_width', 1.0);
            $panel_length = (float) $request->input('panel_length', 1.7);
            $power = (int) $request->input('power');

            $result = $this->solarCalculator->performCalculation($solar_hours, $electricity, $panel_width, $panel_length, $power);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bad Request', 'message' => $e->getMessage()], 400);
        }
    }

    public function calculateCost(Request $request)
    {
        
        $request->validate([
            'monthly_cost' => 'required|numeric'
        ]);

        try {
            $monthly_cost = (float) $request->input('monthly_cost');
            $annual_cost = $monthly_cost * 12;

            return response()->json(['annual_cost' => $annual_cost]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bad Request', 'message' => $e->getMessage()], 400);
        }
    }

    public function welcome()
    {
        // dump('This is a debug message');
        return response()->json(['message' => 'Welcome to the API!']);
    }

    public function user()
    {
        $user = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ];

        return response()->json($user);
    }
}
