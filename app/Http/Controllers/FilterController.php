<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\RestrictionController;

use App\Filter;
use App\Zone;
use App\Region;
use App\District;
use App\Center;

class FilterController extends Controller
{
    //

    /**
    * Controller Instances
    */
    protected $restriction_controller;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->restriction_controller = new RestrictionController();

        $this->middleware('auth');
        $this->middleware('user');
    }

    /**
    * Filter Validation
    */
    protected function validateFilter($data) {

        $validator = Validator::make($data, [
            'zone'=>'sometimes|exists:zones,id',
            'region'=>'sometimes|exists:regions,id',
            'district'=>'sometimes|exists:districts,id',
            'center'=>'sometimes|exists:centers,id',
            'from'=>'required|date',
            'to'=>'required|date'
        ]);

        return $validator;

    }
    /**
     * Show the Blood Collections.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $data = $request->all();

                $validator = $this->validateFilter($data);

                $filter = Filter::where('user', Auth::id())->first();

                if (isset($filter->id) && !empty($filter->id))
                    $_filter = Filter::find($filter->id);
                else $_filter = new Filter();

                if (!empty($data['zone']) && !$validator->errors()->has('zone')) {

                    $_filter->zone = $data['zone'];

                    if (!empty($data['region']) && !$validator->errors()->has('region')) {

                        $_filter->region = $data['region'];

                        if (!empty($data['district']) && !$validator->errors()->has('district')) {

                            $_filter->district = $data['district'];

                            if (!empty($data['center']) && !$validator->errors()->has('center'))
                                $_filter->center = $data['center'];
                            else
                                $_filter->center = NULL;
                        } else {

                            $_filter->district = NULL;

                            $_filter->center = NULL;

                        }
                    } else {

                        $_filter->region = NULL;

                        $_filter->district = NULL;

                        $_filter->center = NULL;

                    }
                } else {

                    $_filter->zone = NULL;

                    $_filter->region = NULL;

                    $_filter->district = NULL;

                    $_filter->center = NULL;

                }

                if (!empty($data['from']) && !$validator->errors()->has('from') && !empty($data['to']) && !$validator->errors()->has('to')) {

                    $_filter->from = date('Y-m-d', strtotime($data['from']));

                    $_filter->to = date('Y-m-d', strtotime($data['to']));

                    $_filter->save();
                }

                return redirect()->back();
            }
        }
    }
    /**
    * Reset Filter
    */
    public function reset(Request $request)
    {
        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'GET') return redirect()->back();
            else {

                $filter = Filter::where('user', Auth::id())->first();

                if (empty($filter)) return redirect()->back();
                else {

                    $_filter = Filter::find($filter->id);

                    $restriction = $this->restriction_controller->getRestriction(Auth::id());

                    if (isset($restriction->zone))
                        $_filter->zone = $restriction->zone;
                    else
                        $_filter->zone = NULL;

                    if (isset($restriction->region))
                        $_filter->region = $restriction->region;
                    else
                        $_filter->region = NULL;

                    if (isset($restriction->district))
                        $_filter->district = $restriction->district;
                    else
                        $_filter->district = NULL;

                    if (isset($restriction->center))
                        $_filter->center = $restriction->center;
                    else
                        $_filter->center = NULL;

                    $_filter->from = date('Y-m-01');
                    $_filter->to = date('Y-m-t');

                    $_filter->save();

                    return redirect()->back();

                }
            }
        }
    }

    /** Get User Filters */
    public function getFilter($user = null) {

        if (!Auth::check()) return;
        else {
            if (!isset($user)) return;
            else {

                $filter = Filter::where('user', $user)->first();

                if (!empty($filter)) {
                    $zone = Zone::where('id', $filter->zone)->first();
                    $region = Region::where('id', $filter->region)->first();
                    $district = District::where('id', $filter->district)->first();
                    $center = Center::where('id', $filter->center)->first();

                    if (!isset($zone->id)) $filter->zone = NULL;
                    if (!isset($region->id)) $filter->region = NULL;
                    if (!isset($district)) $filter->district = NULL;
                    if (!isset($center)) $filter->center = NULL;
                }

                return $filter;
            }
        }
    }

    /**
    * Location Filter
    */
    public function location_filters($query, $not_aggregates = null, $avoid = null) {

        if (!Auth::check()) return;
        else {
            if (!empty($query)) {

                $filter = $this->getFilter(Auth::id());

                if (!empty($filter)) {

                    if (isset($not_aggregates)) {
                        $from = 'from_';
                        $to = 'to_';
                    } else {
                        $from = '';
                        $to = '';
                    }

                    if (isset($filter->zone))
                        $query = $query->where($from.'zones.id', $filter->zone)->where($to.'zones.id', $filter->zone);

                    if ($avoid != 'region') {

                        if (isset($filter->region))
                            $query = $query->where($from.'regions.id', $filter->region)->where($to.'regions.id', $filter->region);

                        if ($avoid != 'district') {

                            if (isset($filter->district))
                                $query = $query->where($from.'districts.id', $filter->district)->where($to.'districts.id', $filter->district);

                            if ($avoid != 'center') {

                                if (isset($filter->center) && isset($not_aggregates))
                                    $query = $query->where($from.'centers.id', $filter->center)->orWhere($to.'centers.id', $filter->center);

                                else if (isset($filter->center))
                                    $query = $query->where('centers.id', $filter->center);
                            }
                        }
                    }
                }
            }

            return $query;
        }
    }

    /**
    * Time Filter function
    */
    public function time_filters($query, $table, $stock = null) {

        if (!Auth::check()) return;
        else {
            if (!empty($query)) {

                $filter = $this->getFilter(Auth::id());

                if (!empty($filter)) {

                    if (isset($filter->from) && !isset($stock))
                        $query = $query->where($table.'.date', '>=', $filter->from);

                    if (isset($filter->to))
                        $query = $query->where($table.'.date', '<=', $filter->to);
                }
            }

            return $query;
        }
    }

}
