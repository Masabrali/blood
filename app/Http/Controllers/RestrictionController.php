<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Restriction;
use App\Zone;
use App\Region;
use App\District;
use App\Center;

class RestrictionController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user');
    }

    /**
    * Check user Restrictions
    */
    public function restricted($_restriction) {

        if (!Auth::check()) return;
        else {
            if (empty($_restriction)) return;
            else {

                $restriction = Restriction::where('user', Auth::id())->first();

                if (empty($restriction)) return;
                else {

                    $restricion = (Array) $restriction;

                    if (empty($restriction[$_restriction])) return;
                    else return !!$restriction[$_restriction];
                }
            }
        }
    }

    /** Get User Restrictions */
    public function getRestriction($user = null) {

        if (!Auth::check()) return;
        else {
            if (!isset($user)) return;
            else {

                $restriction = Restriction::where('user', $user)->first();

                if (!empty($restriction)) {
                    $zone = Zone::where('id', $restriction->zone)->first();
                    $region = Region::where('id', $restriction->region)->first();
                    $district = District::where('id', $restriction->district)->first();
                    $center = Center::where('id', $restriction->center)->first();

                    if (!isset($zone->id)) $restriction->zone = NULL;
                    if (!isset($region->id)) $restriction->region = NULL;
                    if (!isset($district)) $restriction->district = NULL;
                    if (!isset($center)) $restriction->center = NULL;
                }

                return $restriction;
            }
        }
    }

    /**
    * Add Restrictions to the collections
    */
    public function restrictions($query, $not_aggregates = null, $avoid = null) {

        if (!Auth::check()) return;
        else {
            if (!empty($query)) {

                $restriction = $this->getRestriction(Auth::id());

                if (!empty($restriction)) {

                    if (isset($not_aggregates)) {
                        $from = 'from_';
                        $to = 'to_';
                    } else {
                        $from = '';
                        $to = '';
                    }

                    if (isset($restriction->zone))
                        $query = $query->where($from.'zones.id', $restriction->zone)->where($to.'zones.id', $restriction->zone);

                    if ($avoid != 'region') {

                        if (isset($restriction->region))
                            $query = $query->where($from.'regions.id', $restriction->region)->where($to.'regions.id', $restriction->region);

                        if ($avoid != 'district') {

                            if (isset($restriction->district))
                                $query = $query->where($from.'districts.id', $restriction->district)->where($to.'districts.id', $restriction->district);

                            if ($avoid != 'center') {

                                if (isset($restriction->center)) {
                                    if (isset($not_aggregates))
                                        $query = $query->where($from.'centers.id', $restriction->center)->orWhere($to.'centers.id', $restriction->center);
                                    else
                                        $query = $query->where($from.'centers.id', $restriction->center)->where($to.'centers.id', $restriction->center);
                                }
                            }
                        }
                    }
                }
            }

            return $query;
        }
    }

}
