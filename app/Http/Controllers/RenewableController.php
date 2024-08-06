<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RenewableController extends Controller
{
    protected $weatherData;

    public function __construct()
    {
        // Load weather data from JSON file
        $this->weatherData = json_decode(Storage::get('public/weather_by_province.json'), true);
    }

    public function calculateRenewables(Request $request)
    {
        try {
            $id = $request->input('id');

            $weather = $this->getWeather($id);

            if (!$weather) {
                return response()->json(['error' => 'Weather data not found for the given ID'], 404);
            }

            $solar_hours_per_day = $weather['solar_hours_per_day'];
            $wind_strength = $weather['wind_strength'];
            $rain_power = $weather['rain_power'];

            $high_list = [];
            $moderate_list = [];
            $low_list = [];

            if ($solar_hours_per_day >= 6) {
                $high_list[] = "Panel Surya";
            } elseif ($solar_hours_per_day >= 4) {
                $moderate_list[] = "Panel Surya";
            } else {
                $low_list[] = "Panel Surya";
            }

            if ($wind_strength >= 6) {
                $high_list[] = "Turbin Angin";
            } elseif ($wind_strength >= 3) {
                $moderate_list[] = "Turbin Angin";
            } else {
                $low_list[] = "Turbin Angin";
            }

            if ($rain_power === "High") {
                $high_list[] = "Hydroelectric";
            } elseif ($rain_power === "Moderate") {
                $moderate_list[] = "Hydroelectric";
            } else {
                $low_list[] = "Hydroelectric";
            }

            $renewables = [
                'high_list' => $high_list,
                'moderate_list' => $moderate_list,
                'low_list' => $low_list,
            ];

            $result = [
                'solarHours' => $solar_hours_per_day,
                'windStrength' => $wind_strength,
                'rainPower' => $rain_power,
                'renewable_sources' => $renewables,
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bad Request'], 400);
        }
    }

    protected function getWeather($id)
    {
        foreach ($this->weatherData as $province) {
            if ($province['id'] === $id) {
                return $province;
            }
        }

        return null;
    }
}

