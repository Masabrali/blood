<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

/**
 * Create a cascading array of region, districts, wards and streets.
 * Convert the array to a json_string and store it in the database for retrieval
*/
Artisan::command('create_locations_array', function () {
    \App\Http\Controllers\LocationController::createArray();
})->describe('Map all locations i.e Regions, Districts, Wards, and Streets in a cascading array');
