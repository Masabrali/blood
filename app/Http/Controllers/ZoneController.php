<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\LocationController;
use App\Http\Controllers\RestrictionController;
use App\Http\Controllers\FilterController;

use App\Zone;
use App\Group;

class ZoneController extends Controller
{
    //

    /**
    * Controller Instances
    */
    protected $restriction_controller;
    protected $filter_controller;
    protected $groups;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->restriction_controller = new RestrictionController();
        $this->filter_controller = new FilterController();

        $this->groups = Group::all();

        $this->middleware('auth');
        $this->middleware('user');
        $this->middleware('sessions');
    }

    /**
    * Get the current Sock of Blood
    */
    public function stock() {

        if (!Auth::check()) return;
        else {

            $stock = $this->zones();

            $total = [ 'total'=> 0 ];

            $storage = [];

            foreach ($stock as $__stock => $_stock) {

                $_stock = (Array) $_stock;

                $_total = 0;

                foreach ($this->groups as $group) {

                    if (!isset($storage[$group->_name])) $storage[$group->_name] = 0;

                    if (!isset($total[$group->_name])) $total[$group->_name] = 0;

                    $collections = DB::table('collections')
                                    ->join('centers', 'collections.center', '=', 'centers.id')
                                    ->join('districts', 'centers.district', '=', 'districts.id')
                                    ->join('regions', 'districts.region', '=', 'regions.id')
                                    ->join('zones', 'regions.zone', '=', 'zones.id')
                                    ->where('zones.id', $_stock['id'])
                                    ->where('group', $group->id)
                                    ->where('collections.deleted_at', NULL);
                    $collections = $this->restriction_controller->restrictions($collections);
                    $collections = $this->filter_controller->location_filters($collections);
                    $collections = $this->filter_controller->time_filters($collections, 'collections', true);
                    $collections = $collections->sum('units');

                    $transfers_in = DB::table('transfers')
                                    ->join('centers', 'transfers.to', '=', 'centers.id')
                                    ->join('districts', 'centers.district', '=', 'districts.id')
                                    ->join('regions', 'districts.region', '=', 'regions.id')
                                    ->join('zones', 'regions.zone', '=', 'zones.id')
                                    ->where('zones.id', $_stock['id'])
                                    ->where('group', $group->id)
                                    ->where('transfers.deleted_at', NULL);
                    $transfers_in = $this->restriction_controller->restrictions($transfers_in);
                    $transfers_in = $this->filter_controller->location_filters($transfers_in);
                    $transfers_in = $this->filter_controller->time_filters($transfers_in, 'transfers', true);
                    $transfers_in = $transfers_in->sum('units');

                    $transfers_out = DB::table('transfers')
                                    ->join('centers', 'transfers.from', '=', 'centers.id')
                                    ->join('districts', 'centers.district', '=', 'districts.id')
                                    ->join('regions', 'districts.region', '=', 'regions.id')
                                    ->join('zones', 'regions.zone', '=', 'zones.id')
                                    ->where('zones.id', $_stock['id'])
                                    ->where('group', $group->id)
                                    ->where('transfers.deleted_at', NULL);
                    $transfers_out = $this->restriction_controller->restrictions($transfers_out);
                    $transfers_out = $this->filter_controller->location_filters($transfers_out);
                    $transfers_out = $this->filter_controller->time_filters($transfers_out, 'transfers', true);
                    $transfers_out = $transfers_out->sum('units');

                    $distributions = DB::table('distributions')
                                    ->join('centers', 'distributions.center', '=', 'centers.id')
                                    ->join('districts', 'centers.district', '=', 'districts.id')
                                    ->join('regions', 'districts.region', '=', 'regions.id')
                                    ->join('zones', 'regions.zone', '=', 'zones.id')
                                    ->where('zones.id', $_stock['id'])
                                    ->where('group', $group->id)
                                    ->where('distributions.deleted_at', NULL);
                    $distributions = $this->restriction_controller->restrictions($distributions);
                    $distributions = $this->filter_controller->location_filters($distributions);
                    $distributions = $this->filter_controller->time_filters($distributions, 'distributions', true);
                    $distributions = $distributions->sum('units');

                    $storage[$group->_name] += (isset($_stock[$group->_name]))? $_stock[$group->_name]:0;

                    $_stock[$group->_name] = $collections + $transfers_in - $transfers_out - $distributions;

                    $_total += $_stock[$group->_name];

                    $total[$group->_name] += $_stock[$group->_name];
                }

                $_stock['total'] = $_total;

                $total['total'] += $_total;

                $stock[$__stock] = (Object) $_stock;
            }

            $storage = (Object) $storage;

            $total = (Object) $total;

            return (Object) [ 'stock'=>$stock, 'total'=>$total, 'storage'=>$storage ];
        }
    }

    /**
    * Get Zones
    */
    public function zones($zone = null) {

        if (!Auth::check()) return;
        else {

            $zones = Zone::where('zones.deleted_at', NULL);

            $zones = $this->restriction_controller->restrictions($zones, null, 'region');

            if (isset($zone) && $zone != null)
                $zones = $zones->where('zones.id', $zone);
            else
                $zones = $this->filter_controller->location_filters($zones, null, 'region');

            $zones = $zones->orderBy('name', 'ASC')->get();

            foreach ($zones AS $__zone => $_zone) {

                $_zone = $_zone->getAttributes();

                $storages = $this->storages($_zone['id']);

                foreach ($storages as $storage)
                    $_zone[$storage->_name] = $storage->units;

                $zones[$__zone] = (Object) $_zone;

            }

            if (isset($zone) && $zone != null) return $zones->first();
            else return $zones;
        }
    }

    /**
    * Get Zone
    */
    public function zone($zone) {

        if (!Auth::check()) return;
        else {
            if (empty($zone)) return;
            else return $this->zones($zone);
        }
    }

    /**
    * Get Center Storage
    */
    public function storages($zone = null) {

        if (!Auth::check()) return;
        else {

            $storages = DB::table('storages')
                        ->join('groups', 'storages.group', 'groups.id')
                        ->join('centers', 'storages.center', '=', 'centers.id')
                        ->join('districts', 'centers.district', '=', 'districts.id')
                        ->join('regions', 'districts.region', '=', 'regions.id')
                        ->join('zones', 'regions.zone', '=', 'zones.id')
                        ->select(DB::raw('SUM(units) AS units'))
                        ->orderBy('group', 'ASC')
                        ->orderBy('zone', 'ASC')
                        ->groupBy('group')
                        ->groupBy('zone');

            $storages = $this->restriction_controller->restrictions($storages);
            $storages = $this->filter_controller->location_filters($storages);

            if (isset($zone) && $zone != null)
                $storages = $storages->where('zones.id', $zone);

            $storages = $storages->get();

            $_storages = [];

            foreach ($this->groups as $_group => $group)
                $_storages[$group->_name] = (Object) [ 'group'=>$group->id, '_name'=>$group->_name, 'units'=>(isset($storages[$_group]->units))? $storages[$_group]->units:0 ];

            return (Object) $_storages;
        }
    }

    /**
     * Add a New Zone.
     * Validate Zone
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateZone($data, $edit = null) {

        $validation = [ 'name' => 'required|string' ];

        if (!isset($edit)) $validation['name'] .= '|unique:zones,name';

        $validator = Validator::make($data, $validation);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood Zone Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('zone') || Auth::user()->role_id != 1)
                return redirect()->back();
            else
                return view('zone.zone', [
                    'title'=>'Zones', 'user'=>Auth::user(), 'handler'=>'addZone'
                ]);
        }
    }

    /**
     * Add a New Zone.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('zone') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateZone($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $zone = Zone::create([ 'name' => $data['name'] ]);

                        LocationController::createArray();

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Zone Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($zone, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('zone') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($zone)) return redirect()->back();
                else {

                    $zone = $this->zone($zone);

                    if (empty($zone)) return redirect()->back();
                    else {

                        $data = [ 'name' => $zone->name ];

                        $request->session()->put([ 'zone'=>$zone->id ]);

                        return view('zone.zone', [
                            'title'=>'Zones', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editZone', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Edit Zone.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('zone') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $zone = $request->session()->get('zone');

                    $zone = Zone::where('id', $zone)->first();

                    if (empty($zone)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateZone($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_zone = Zone::find($zone->id);

                            if ($zone->name != $data['name']) {
                                $validator = $this->validateZone($data);

                                if ($validator->fails())
                                    return redirect()->back()->withErrors($validator)->withInputs();
                                else
                                    $_zone->name = $data['name'];
                            }

                            $_zone->save();

                            LocationController::createArray();

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Zone Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($zone, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('zone') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($zone)) return redirect()->back();
                else {

                    $zone = $this->zone($zone);

                    if (empty($zone)) return redirect()->back();
                    else {

                        $data = [ 'name' => $zone->name ];

                        $request->session()->put([ 'zone'=>$zone->id ]);

                        return view('zone.zone', [
                            'title'=>'Zones', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteZone', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Delete Zone.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('zone') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $zone = $request->session()->get('zone');

                    $zone = Zone::where('id', $zone)->first();

                    if (empty($zone)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateZone($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_zone = Zone::find($zone->id);

                            if ($zone->name == $data['name']) {
                                $_zone->delete();

                                LocationController::createArray();
                            }

                            return redirect('/zones');
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Zone View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($zone, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($zone)) return redirect()->back();
            else {

                $zone = $this->zone($zone);

                if (empty($zone)) return redirect()->back();
                else {

                    $data = [ 'name' => $zone->name ];

                    return view('zone.zone', [
                        'title'=>'Zones', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function load(Request $request) {

        if (!Auth::check()) return redirect('/');
        else
            return view('zone.zones', [
                'title'=>'Zones', 'user'=>Auth::user(), 'restriction'=>$this->restriction_controller, 'stock'=>$this->stock()
            ]);
    }
}
