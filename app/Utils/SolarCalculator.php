<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class SolarCalculator
{
    private $solarData;

    public function __construct()
    {
        // Load JSON data from the storage
        $json = Storage::get('public/installation_price.json');
        $this->solarData = json_decode($json, true);
        // dump($this->solarData);
    }

    public function performCalculation($solar_hours, $electricity, $panel_width, $panel_length, $parsed_power)
    {
        // Default values
        $panel_width = is_numeric($panel_width) ? $panel_width : 1.0;
        $panel_length = is_numeric($panel_length) ? $panel_length : 1.7;
        $parsed_power = in_array($parsed_power, [1300, 2200, 3500]) ? $parsed_power : 1300;

        $solar_array = $this->getSolarArray($solar_hours, $electricity);
        $num_panels = $this->getNumPanels($solar_array);
        $area_occupied = $this->getAreaOccupied($num_panels, $panel_width, $panel_length);
        $price = $this->getPrice($parsed_power);

        return [
            'solarArray' => $solar_array,
            'numPanels' => $num_panels,
            'areaOccupied' => $area_occupied,
            'price' => $price
        ];
    }

    private function getSolarArray($solar_hours, $electricity)
    {
        $array_output = $electricity / (365 * $solar_hours);
        $bill_offset = 0.08;
        $env_factor = 0.85;
        $array_size = $array_output * ($bill_offset / $env_factor) * 10;
        return number_format($array_size, 2);
    }

    private function getNumPanels($solar_array)
    {
        $panel_outputs = 300;
        $num_panels = $solar_array * 1000 / $panel_outputs;
        return number_format($num_panels, 0);
    }

    private function getAreaOccupied($num_panels, $panel_width, $panel_length)
    {
        $area_occupied = $num_panels * $panel_width * $panel_length;
        return number_format($area_occupied, 2);
    }

    private function getPrice($parsed_power)
    {
        $prices = array_filter($this->solarData['solar_panel'], function ($panel) use ($parsed_power) {
            return $panel['daya'] == $parsed_power;
        });

        if (empty($prices)) {
            throw new \Exception("Invalid power value");
        }

        return $prices;
    }
}
