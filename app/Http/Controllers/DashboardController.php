<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\RestrictionController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CenterController;

class DashboardController extends Controller
{

    /**
    * Controller Instances
    */
    protected $restriction_controller;
    protected $filter_controller;
    protected $zone_controller;
    protected $region_controller;
    protected $district_controller;
    protected $center_controller;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->restriction_controller = new RestrictionController();
        $this->filter_controller = new FilterController();
        $this->zone_controller = new ZoneController();
        $this->region_controller = new RegionController();
        $this->district_controller = new DistrictController();
        $this->center_controller = new CenterController();

        $this->middleware('auth');
        $this->middleware('user');
        $this->middleware('sessions');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function load(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {

            $filter = $this->filter_controller->getFilter(Auth::id());
            $restriction = $this->restriction_controller->getRestriction(Auth::id());

            if (!empty($filter->center) || !empty($restriction->center) || !empty($filter->district) || !empty($restriction->district)) {

                $stock = $this->center_controller->stock();

                $stock_title = 'Center';

            } else if (!empty($filter->region) || !empty($restriction->region)) {

                $stock = $this->district_controller->stock();

                $stock_title = 'District';

            } else if (!empty($filter->zone) || !empty($restriction->zone)) {

                $stock = $this->region_controller->stock();

                $stock_title = 'Region';

            } else {
                $stock = $this->zone_controller->stock();

                $stock_title = 'Zone';
            }

            return view('dashboard', [ 'title'=>'Dashboard', 'user'=>Auth::user(), 'stock'=>$stock, 'stock_title'=>$stock_title ]);
        }
    }
}
