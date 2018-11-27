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

use App\Center;
use App\Group;
use App\Storage;

class CenterController extends Controller
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
    public function stock($center = null) {

        if (!Auth::check()) return;
        else {

            $stock = $this->centers($center);

            if (!method_exists($stock, 'isEmpty')) $stock = [$stock];

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
                                    ->where('centers.id', $_stock['_center'])
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
                                    ->where('centers.id', $_stock['_center'])
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
                                    ->where('centers.id', $_stock['_center'])
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
                                    ->where('centers.id', $_stock['_center'])
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
    * Get Centers
    */
    public function centers($center = null) {

        if (!Auth::check()) return;
        else {

            $centers = DB::table('centers')
                            ->join('districts', 'centers.district', '=', 'districts.id')
                            ->join('regions', 'districts.region', '=', 'regions.id')
                            ->join('zones', 'regions.zone', '=', 'zones.id')
                            ->select(
                                'centers.id AS _center', 'centers.name AS center', 'zones.id AS _zone', 'zones.name AS zone', 'regions.id AS _region', 'regions.name AS region', 'districts.id AS _district', 'districts.name AS district'
                            )
                            ->where('centers.deleted_at', NULL)
                            ->where('districts.deleted_at', NULL)
                            ->where('regions.deleted_at', NULL)
                            ->where('zones.deleted_at', NULL);

            $centers = $this->restriction_controller->restrictions($centers);

            if (isset($center) && $center != null)
                $centers = $centers->where('centers.id', $center);
            else
                $centers = $this->filter_controller->location_filters($centers);

            $centers = $centers->orderBy('zone', 'ASC')
                              ->orderBy('region', 'ASC')
                              ->orderBy('district', 'ASC')
                              ->orderBy('center', 'ASC')
                              ->get();

            foreach ($centers AS $__center => $_center) {

                $storages = $this->storages($_center->_center);

                $_center = (Array) $_center;

                foreach ($storages as $storage)
                    $_center[$storage->_name] = $storage->units;

                $centers[$__center] = (Object) $_center;

            }

            if (isset($center) && $center != null) return $centers->first();
            else return $centers;

        }
    }

    /**
    * Get Center
    */
    public function center($center) {

        if (!Auth::check()) return;
        else {
            if (empty($center)) return;
            else return $this->centers($center);
        }
    }

    /**
    * Get Center Storage
    */
    public function storages($center = null) {

        if (!Auth::check()) return;
        else {

            $storages = DB::table('storages')
                        ->join('groups', 'storages.group', 'groups.id')
                        ->join('centers', 'storages.center', '=', 'centers.id')
                        ->join('districts', 'centers.district', '=', 'districts.id')
                        ->join('regions', 'districts.region', '=', 'regions.id')
                        ->join('zones', 'regions.zone', '=', 'zones.id')
                        ->select('storages.id AS id', 'center', 'group', '_name', 'units');

            $storages = $this->restriction_controller->restrictions($storages);
            $storages = $this->filter_controller->location_filters($storages);

            if (isset($center) && $center != null)
                $storages = $storages->where('center', $center);

            return $storages->orderBy('group')
                            ->orderBy('zone', 'ASC')
                            ->orderBy('region', 'ASC')
                            ->orderBy('district', 'ASC')
                            ->orderBy('center', 'ASC')
                            ->get();
        }
    }

    /**
     * Add a New Center.
     * Validate Center
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateCenter($data) {

        $validation = [
            'zone' => 'required|exists:zones,id',
            'region' => 'required|exists:regions,id',
            'district' => 'required|exists:districts,id',
            'name' => [
                'required', 'string',
                Rule::unique('centers', 'name')->where(function ($query) use ($data) {
                    return $query->where('zone', $data->zone)->where('region', $data['region'])->where('district', $data['distric']);
                })
            ]
        ];

        foreach ($this->groups as $group)
            $validation[$group->_name] = 'required|integer|min:2';

        if (!isset($edit))
            array_push($validation['name'],
                Rule::unique('centers', 'name')->where(function ($query) use ($data) {
                    return $query->where('zone', $data->zone)->where('region', $data['region'])->where('district', $data['distric']);
                })
            );

        $validator = Validator::make($data, $validation);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood Center Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('center') || Auth::user()->role_id != 1)
                return redirect()->back();
            else
                return view('center.center', [
                    'title'=>'Centers', 'user'=>Auth::user(), 'handler'=>'addCenter'
                ]);
        }
    }

    /**
     * Add a New Center.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('center') || Auth::user()->role_id != 1)
                return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateCenter($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $center = Center::create([
                            'district' => $data['district'],
                            'name' => $data['name'],
                        ]);

                        $_storage = [];

                        foreach ($this->groups as $group) {

                            array_push($_storage, [
                                'user' => Auth::id(),
                                'center' => $center->id,
                                'group' => $group->id,
                                'units' => $data[$group->_name]
                            ]);

                        }

                        $storage = Storage::insert($_storage);

                        LocationController::createArray();

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Center Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($center, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('center') || Auth::user()->role_id != 1)
                return redirect()->back();
            else {
                if (empty($center)) return redirect()->back();
                else {

                    $center = $this->center($center);

                    if (empty($center)) return redirect()->back();
                    else {

                        $data = [
                            'zone' => $center->_zone,
                            'region' => $center->_region,
                            'district' => $center->_district,
                            'name' => $center->center
                        ];

                        $center = (Array) $center;
                        foreach ($this->groups as $group)
                            $data[$group->_name] = (isset($center[$group->_name]))? $center[$group->_name]:0;

                        $request->session()->put([ 'center'=>$center['_center'] ]);

                        return view('center.center', [
                            'title'=>'Centers', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editCenter', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Edit Center.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('center') || Auth::user()->role_id != 1)
                return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $center = $request->session()->get('center');

                    $center = $this->center($center);

                    if (empty($center)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateCenter($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $edited = null;

                            $_center = Center::find($center->_center);

                            if ($center->_district != $data['district']) {
                                $_center->district = $data['district'];

                                $edited = true;
                            }

                            if ($center->center != $data['name']) {

                                $validator = $this->validateCenter($data);

                                if ($validator->fails())
                                    return redirect()->back()->withErrors($validator)->withInputs();
                                else
                                    $_center->name = $data['name'];
                            }

                            $storages = $this->storages($center->_center);

                            if (!$storages->isEmpty()) {

                                $center = (Array) $center;
                                foreach ($storages as $storage)
                                    if ($center[$storage->_name] != $data[$storage->_name]) {

                                        $_storage = Storage::find($storage->id);

                                        $_storage->units = $data[$storage->_name];

                                        $_storage->save();

                                    }

                            } else {

                                $_storage = [];

                                foreach ($this->groups as $group) {

                                    array_push($_storage, [
                                        'user' => Auth::id(),
                                        'center' => $center->_center,
                                        'group' => $group->id,
                                        'units' => $data[$group->_name]
                                    ]);

                                }

                                $storage = Storage::insert($_storage);
                            }

                            $_center->save();

                            LocationController::createArray();

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Center Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($center, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('center') || Auth::user()->role_id != 1)
                return redirect()->back();
            else {
                if (empty($center)) return redirect()->back();
                else {

                    $center = $this->center($center);

                    if (empty($center)) return redirect()->back();
                    else {

                        $data = [
                            'zone' => $center->_zone,
                            'region' => $center->_region,
                            'district' => $center->_district,
                            'name' => $center->center
                        ];

                        $center = (Array) $center;
                        foreach ($this->groups as $group)
                            $data[$group->_name] = (isset($center[$group->_name]))? $center[$group->_name]:0;

                        $request->session()->put([ 'center'=>$center['_center'] ]);

                        return view('center.center', [
                            'title'=>'Centers', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteCenter', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Delete Center.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($this->restriction_controller->restricted('center') || Auth::user()->role_id != 1)
                return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $center = $request->session()->get('center');

                    $center = $this->center($center);

                    if (empty($center)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateCenter($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_center = Center::find($center->id);

                            if ($center->_district == $data['district'] && $center->center == $data['name']) {

                              $center = (Array) $center;
                              foreach ($this->groups as $group)
                                  if ($data[$group->_name] != $center[$group->_name])
                                      return redirect()->back();

                                $_center->delete();

                                LocationController::createArray();
                            }

                            return redirect('/centers');
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Center View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($center, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($center)) return redirect()->back();
            else {

                $center = $this->center($center);

                if (empty($center)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $center->_zone,
                        'region' => $center->_region,
                        'district' => $center->_district,
                        'name' => $center->center
                    ];

                    $center = (Array) $center;
                    foreach ($this->groups as $group)
                        $data[$group->_name] = (isset($center[$group->_name]))? $center[$group->_name]:0;

                    return view('center.center', [
                        'title'=>'Centers', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('center.centers', [
                'title'=>'Centers', 'user'=>Auth::user(), 'restriction'=>$this->restriction_controller, 'stock'=>$this->stock()
            ]);
    }

}
