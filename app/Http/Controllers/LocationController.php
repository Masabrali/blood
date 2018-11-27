<?php

namespace App\Http\Controllers;

use App\Zone;
use App\Region;
use App\District;
use App\Center;
use App\Location;

class LocationController extends Controller
{
    /**
     * Map all the locations: Regions, Districts, Wards, streets
     * Store the new object in json_format in the database
     * @return void
     */
    static function createArray() {

        $locations = Array();

        $zones = Zone::orderBy('name', 'ASC')->get();
        foreach ($zones as $zone) {
            $zone = $zone->getAttributes();

            $regions = Array();
            foreach (Region::where('zone', $zone['id'])->orderBy('name', 'ASC')->get() as $region) {
                $region = $region->getAttributes();

                $districts = Array();
                foreach (District::where('region', $region['id'])->orderBy('name', 'ASC')->get() as $district) {
                    $district = $district->getAttributes();

                    $centers = Array();
                    foreach(Center::where('district', $district['id'])->orderBy('name', 'ASC')->get() as $center) {
                        array_push($centers, $center->getAttributes());
                    }
                    $district['centers'] = $centers;

                    $districts[$district['id']] = $district;
                }
                $region['districts'] = $districts;

                $regions[$region['id']] = $region;
            }
            $zone['regions'] = $regions;

            $locations[$zone['id']] = $zone;
        }

        $location = Location::find(1);

        if (!$location || !isset($location) || empty($location)) $location = new Location();

        $location->locations = json_encode($locations);

        $location->save();
    }
}
