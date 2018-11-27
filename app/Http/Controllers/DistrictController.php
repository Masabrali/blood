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

use App\District;
use App\Group;

class DistrictController extends Controller
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

        $this->groups = Group::orderBy('id', 'ASC')->get();

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

            $stock = $this->districts();

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
                                    ->where('districts.id', $_stock['_district'])
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
                                    ->where('districts.id', $_stock['_district'])
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
                                    ->where('districts.id', $_stock['_district'])
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
                                    ->where('districts.id', $_stock['_district'])
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
    * Get Districts
    */
    public function districts($district = null) {

        if (!Auth::check()) return;
        else {

            $districts = DB::table('districts')
                            ->join('regions', 'districts.region', '=', 'regions.id')
                            ->join('zones', 'regions.zone', '=', 'zones.id')
                            ->select(
                                  'districts.id AS _district', 'districts.name AS district', 'zones.id AS _zone', 'zones.name AS zone', 'regions.id AS _region', 'regions.name AS region'
                            )
                            ->where('districts.deleted_at', NULL)
                            ->where('regions.deleted_at', NULL)
                            ->where('zones.deleted_at', NULL);

            $districts = $this->restriction_controller->restrictions($districts, null, 'center');

            if (isset($district) && $district != null)
                $districts = $districts->where('districts.id', $district);
            else
                $districts = $this->filter_controller->location_filters($districts, null, 'center');

            $districts = $districts->orderBy('zone', 'ASC')
                                  ->orderBy('region', 'ASC')
                                  ->orderBy('district', 'ASC')
                                  ->get();

            foreach ($districts AS $__district => $_district) {

                $storages = $this->storages($_district->_district);

                $_district = (Array) $_district;

                foreach ($storages as $storage)
                    $_district[$storage->_name] = $storage->units;

                $districts[$__district] = (Object) $_district;

            }

            if (isset($district) && $district != null) return $districts->first();
            else return $districts;

        }
    }

    /**
    * Get District
    */
    public function district($district) {

        if (!Auth::check()) return;
        else {
            if (empty($district)) return;
            else return $this->districts($district);
        }
    }

    /**
    * Get Center Storage
    */
    public function storages($district = null) {

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
                        ->orderBy('district', 'ASC')
                        ->groupBy('group')
                        ->groupBy('district');

            $storages = $this->restriction_controller->restrictions($storages);
            $storages = $this->filter_controller->location_filters($storages);

            if (isset($district) && $district != null)
                $storages = $storages->where('districts.id', $district);

            $storages = $storages->get();

            $_storages = [];

            foreach ($this->groups as $_group => $group)
                $_storages[$group->_name] = (Object) [ 'group'=>$group->id, '_name'=>$group->_name, 'units'=>(isset($storages[$_group]->units))? $storages[$_group]->units:0 ];

            return (Object) $_storages;
        }
    }

    /**
     * Add a New District.
     * Validate District
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateDistrict($data, $edit = null) {

        $validation = [
            'zone' => 'required|exists:zones,id',
            'region' => 'required|exists:regions,id',
            'name' => [ 'required', 'string' ]
        ];

        if (!isset($edit))
            array_push($validation['name'],
                Rule::unique('districts', 'name')->where(function ($query) use ($data) {
                    return $query->where('zone', $data->zone)->where('region', $data['region']);
                })
            );

        $validator = Validator::make($data, $validation);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood District Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('district') || Auth::user()->role_id != 1)
                return redirect()->back();
            else
                return view('district.district', [
                    'title'=>'Districts', 'user'=>Auth::user(), 'handler'=>'addDistrict'
                ]);
        }
    }

    /**
     * Add a New District.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('district') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateDistrict($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $district = District::create([
                            'region' => $data['region'],
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
     * Show the Blood District Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($district, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('district') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($district)) return redirect()->back();
                else {

                    $district = $this->district($district);

                    if (empty($district)) return redirect()->back();
                    else {

                        $data = [
                            'zone' => $district->_zone,
                            'region' => $district->_region,
                            'name' => $district->district
                        ];

                        $request->session()->put([ 'district'=>$district->_district ]);

                        return view('district.district', [
                            'title'=>'Districts', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editDistrict', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Edit District.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('district') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $district = $request->session()->get('district');

                    $district = District::where('id', $district)->first();

                    if (empty($district)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateDistrict($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $edited = null;

                            $_district = District::find($district->id);

                            if ($district->region != $data['region']) {
                                $_district->region = $data['region'];

                                $edited = true;
                            }

                            if ($district->name != $data['name']) {

                                $validator = $this->validateDistrict($data);

                                if ($validator->fails())
                                    return redirect()->back()->withErrors($validator)->withInputs();
                                else
                                    $_district->name = $data['name'];
                            }

                            $_district->save();

                            LocationController::createArray();

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood District Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($district, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('district') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($district)) return redirect()->back();
                else {

                    $district = $this->district($district);

                    if (empty($district)) return redirect()->back();
                    else {

                        $data = [
                            'zone' => $district->_zone,
                            'region' => $district->_region,
                            'name' => $district->district
                        ];

                        $request->session()->put([ 'district'=>$district->_district ]);

                        return view('district.district', [
                            'title'=>'Districts', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteDistrict', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Delete District.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('district') || Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $district = $request->session()->get('district');

                    $district = District::where('id', $district)->first();

                    if (empty($district)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateDistrict($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_district = District::find($district->id);

                            if ($district->region == $data['region'] && $district->name == $data['name']) {
                                $_district->delete();

                                LocationController::createArray();
                            }

                            return redirect('/districts');
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood District View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($district, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($district)) return redirect()->back();
            else {

                $district = $this->district($district);

                if (empty($district)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $district->_zone,
                        'region' => $district->_region,
                        'name' => $district->district
                    ];

                    return view('district.district', [
                        'title'=>'Districts', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('district.districts', [
                'title'=>'Districts', 'user'=>Auth::user(), 'restriction'=>$this->restriction_controller, 'stock'=>$this->stock()
            ]);
    }
}
