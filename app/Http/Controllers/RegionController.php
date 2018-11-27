<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\LocationController;
use App\Http\Controllers\RestrictionController;
use App\Http\Controllers\FilterController;

use App\Region;
use App\Group;

class RegionController extends Controller
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

            $stock = $this->regions();

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
                                    ->where('regions.id', $_stock['_region'])
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
                                    ->where('regions.id', $_stock['_region'])
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
                                    ->where('regions.id', $_stock['_region'])
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
                                    ->where('regions.id', $_stock['_region'])
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
    * Get Regions
    */
    public function regions($region = null) {

        if (!Auth::check()) return;
        else {

            $regions = DB::table('regions')
                            ->join('zones', 'regions.zone', '=', 'zones.id')
                            ->select(
                                'regions.id AS _region', 'regions.name AS region', 'zones.id AS _zone', 'zones.name AS zone'
                            )
                            ->where('regions.deleted_at', NULL)
                            ->where('zones.deleted_at', NULL);

            $regions = $this->restriction_controller->restrictions($regions, null, 'district');

            if (isset($region) && $region != null)
                $regions = $regions->where('regions.id', $region);
            else
                $regions = $this->filter_controller->location_filters($regions, null, 'district');

            $regions = $regions->orderBy('zone', 'ASC')->orderBy('region', 'ASC')->get();

            foreach ($regions AS $__region => $_region) {

                $storages = $this->storages($_region->_region);

                $_region = (Array) $_region;

                foreach ($storages as $storage)
                    $_region[$storage->_name] = $storage->units;

                $regions[$__region] = (Object) $_region;

            }

            if (isset($region) && $region != null) return $regions->first();
            else return $regions;
        }
    }

    /**
    * Get Region
    */
    public function region($region) {

        if (!Auth::check()) return;
        else {
            if (empty($region)) return;
            else return $this->regions($region);
        }
    }

    /**
    * Get Center Storage
    */
    public function storages($region = null) {

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
                        ->orderBy('region', 'ASC')
                        ->groupBy('group')
                        ->groupBy('region');

            $storages = $this->restriction_controller->restrictions($storages);
            $storages = $this->filter_controller->location_filters($storages);

            if (isset($region) && $region != null)
                $storages = $storages->where('regions.id', $region);

            $storages = $storages->get();

            $_storages = [];

            foreach ($this->groups as $_group => $group)
                $_storages[$group->_name] = (Object) [ 'group'=>$group->id, '_name'=>$group->_name, 'units'=>(isset($storages[$_group]->units))? $storages[$_group]->units:0 ];

            return (Object) $_storages;
        }
    }

    /**
     * Add a New Region.
     * Validate Region
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateRegion($data, $edit = null) {

        $validation = [
            'zone' => 'required|exists:zones,id',
            'name' => [ 'required', 'string' ]
        ];

        if (!isset($edit))
            array_push($validation['name'],
                Rule::unique('regions', 'name')->where(function($query) use ($data) {
                    return $query->where('zone', $data['zone']);
                })
            );

        $validator = Validator::make($data, $validation);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood Region Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('region') || Auth::user()->role_id != 1)
                return redirect()->back();
            else
                return view('region.region', [
                    'title'=>'Regions', 'user'=>Auth::user(), 'handler'=>'addRegion'
                ]);
        }
    }

    /**
     * Add a New Region.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('region') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateRegion($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $region = Region::create([
                            'zone' => $data['zone'],
                            'name' => $data['name']
                        ]);

                        LocationController::createArray();

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Region Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($region, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('region') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($region)) return redirect()->back();
                else {

                    $region = $this->region($region);

                    if (empty($region)) return redirect()->back();
                    else {

                        $data = [
                            'zone' => $region->_zone,
                            'name' => $region->region
                        ];

                        $request->session()->put([ 'region'=>$region->_region ]);

                        return view('region.region', [
                            'title'=>'Regions', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editRegion', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Edit Region.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('region') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $region = $request->session()->get('region');

                    $region = Region::where('id', $region)->first();

                    if (empty($region)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateRegion($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $edited = null;

                            $_region = Region::find($region->id);

                            if ($region->zone != $data['zone']) {
                                $_region->zone = $data['zone'];

                                $edited = true;
                            }

                            if ($region->name != $data['name']) {

                                $validator = $this->validateRegion($data);

                                if ($validator->fails())
                                    return redirect()->back()->withErrors($validator)->withInputs();
                                else
                                    $_region->name = $data['name'];
                            }

                            $_region->save();

                            LocationController::createArray();

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Region Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($region, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('region') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($region)) return redirect()->back();
                else {

                    $region = $this->region($region);

                    if (empty($region)) return redirect()->back();
                    else {

                        $data = [
                            'zone' => $region->_zone,
                            'name' => $region->region
                        ];

                        $request->session()->put([ 'region'=>$region->_region ]);

                        return view('region.region', [
                            'title'=>'Regions', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteRegion', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Delete Region.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('region') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $region = $request->session()->get('region');

                    $region = Region::where('id', $region)->first();

                    if (empty($region)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateRegion($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_region = Region::find($region->id);

                            if ($region->zone == $data['zone'] && $region->name == $data['name']) {
                                $_region->delete();

                                LocationController::createArray();
                            }

                            return redirect('/regions');
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Region View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($region, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($region)) return redirect()->back();
            else {

                $region = $this->region($region);

                if (empty($region)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $region->_zone,
                        'name' => $region->region
                    ];

                    return view('region.region', [
                        'title'=>'Regions', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('region.regions', [
                'title'=>'Regions', 'user'=>Auth::user(), 'restriction'=>$this->restriction_controller, 'stock'=>$this->stock()
            ]);
    }
}
