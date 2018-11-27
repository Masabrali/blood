<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\RestrictionController;
use App\Http\Controllers\FilterController;

use App\Group;

class GroupController extends Controller
{
    //

    /**
    * Controller Instances
    */
    protected $restriction_controller;
    protected $filter_controller;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->restriction_controller = new RestrictionController();
        $this->filter_controller = new FilterController();

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

            $stock = $this->groups();

            $total = [
                'collections'=>0, 'transfers_in'=>0, 'transfers_out'=>0, 'distributions'=>0, 'total'=>0
            ];

            $storage = 0;

            foreach ($stock as $__stock => $_stock) {

                $_stock = (Array) $_stock;

                $collections = DB::table('collections')
                                ->join('groups', 'collections.group', 'groups.id')
                                ->join('centers', 'collections.center', '=', 'centers.id')
                                ->join('districts', 'centers.district', '=', 'districts.id')
                                ->join('regions', 'districts.region', '=', 'regions.id')
                                ->join('zones', 'regions.zone', '=', 'zones.id')
                                ->where('groups.id', $_stock['id'])
                                ->where('collections.deleted_at', NULL);
                $collections = $this->restriction_controller->restrictions($collections);
                $collections = $this->filter_controller->location_filters($collections);
                $collections = $this->filter_controller->time_filters($collections, 'collections', true);
                $collections = $collections->sum('units');

                $_stock['collections'] = $collections;

                $total['collections'] += $collections;

                $transfers_in = DB::table('transfers')
                                ->join('groups', 'transfers.group', 'groups.id')
                                ->join('centers', 'transfers.to', '=', 'centers.id')
                                ->join('districts', 'centers.district', '=', 'districts.id')
                                ->join('regions', 'districts.region', '=', 'regions.id')
                                ->join('zones', 'regions.zone', '=', 'zones.id')
                                ->where('groups.id', $_stock['id'])
                                ->where('transfers.deleted_at', NULL);
                $transfers_in = $this->restriction_controller->restrictions($transfers_in);
                $transfers_in = $this->filter_controller->location_filters($transfers_in);
                $transfers_in = $this->filter_controller->time_filters($transfers_in, 'transfers', true);
                $transfers_in = $transfers_in->sum('units');

                $_stock['transfers_in'] = $transfers_in;

                $total['transfers_in'] += $transfers_in;

                $transfers_out = DB::table('transfers')
                                ->join('groups', 'transfers.group', 'groups.id')
                                ->join('centers', 'transfers.from', '=', 'centers.id')
                                ->join('districts', 'centers.district', '=', 'districts.id')
                                ->join('regions', 'districts.region', '=', 'regions.id')
                                ->join('zones', 'regions.zone', '=', 'zones.id')
                                ->where('groups.id', $_stock['id'])
                                ->where('transfers.deleted_at', NULL);
                $transfers_out = $this->restriction_controller->restrictions($transfers_out);
                $transfers_out = $this->filter_controller->location_filters($transfers_out);
                $transfers_out = $this->filter_controller->time_filters($transfers_out, 'transfers', true);
                $transfers_out = $transfers_out->sum('units');

                $_stock['transfers_out'] = $transfers_out;

                $total['transfers_out'] += $transfers_out;

                $distributions = DB::table('distributions')
                                ->join('groups', 'distributions.group', 'groups.id')
                                ->join('centers', 'distributions.center', '=', 'centers.id')
                                ->join('districts', 'centers.district', '=', 'districts.id')
                                ->join('regions', 'districts.region', '=', 'regions.id')
                                ->join('zones', 'regions.zone', '=', 'zones.id')
                                ->where('groups.id', $_stock['id'])
                                ->where('distributions.deleted_at', NULL);
                $distributions = $this->restriction_controller->restrictions($distributions);
                $distributions = $this->filter_controller->location_filters($distributions);
                $distributions = $this->filter_controller->time_filters($distributions, 'distributions', true);
                $distributions = $distributions->sum('units');

                $_stock['distributions'] = $distributions;

                $total['distributions'] += $distributions;

                $storage += (isset($_stock['storage']))? $_stock['storage']:0;

                $_stock['units'] = $collections + $transfers_in - $transfers_out - $distributions;

                if ($_stock['storage'] == 0) $_stock['percent'] = 0;
                else
                    $_stock['percent'] = round(($_stock['units'] / $_stock['storage']) * 100, 2);

                $total['total'] += $_stock['units'];

                $stock[$__stock] = (Object) $_stock;
            }

            $total = (Object) $total;

            if ($storage == 0) $percent = 0;
            else $percent = round(($total->total /  $storage) * 100, 2);

            return (Object) [ 'stock'=>$stock, 'total'=>$total, 'storage'=>$storage, 'percent'=>$percent ];
        }
    }

    /**
    * Get Groups
    */
    public function groups($group = null) {

        if (!Auth::check()) return;
        else {

            $groups = Group::where('groups.deleted_at', NULL);

            if (isset($group) && $group != null)
                $groups = $groups->where('groups.id', $group);

            $groups = $groups->orderBy('name', 'ASC')->get();

            foreach ($groups AS $__group => $_group) {

                $_group = $_group->getAttributes();

                $storage = $this->storages($_group['id']);

                $_group['storage'] = (isset($storage->units))? $storage->units:0;

                $groups[$__group] = (Object) $_group;

            }

            if (isset($group) && $group != null) return $groups->first();
            else return $groups;
        }
    }

    /**
    * Get Group
    */
    public function group($group) {

        if (!Auth::check()) return;
        else {
            if (empty($group)) return;
            else return $this->groups($group);
        }
    }

    /**
    * Get Center Storage
    */
    public function storages($group = null) {

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
                        ->groupBy('group');

            $storages = $this->restriction_controller->restrictions($storages);
            $storages = $this->filter_controller->location_filters($storages);

            if (isset($group) && $group != null)
                return $storages->where('groups.id', $group)->first();
            else return $storages->get();
        }
    }

    /**
     * Add a New Group.
     * Validate Group
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateGroup($data, $edit = null) {

        $validation = [ 'name' => 'required|string' ];

        if (!isset($edit)) $validation['name'] .= '|unique:groups,name';

        $validator = Validator::make($data, $validation);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood Group Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else
                return view('group.group', [
                    'title'=>'Groups', 'user'=>Auth::user(), 'handler'=>'addGroup'
                ]);
        }
    }

    /**
     * Add a New Group.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateGroup($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        if (substr_count($data['name'], '+'))
                            $_name = str_replace('+', '_plus', $data['name']);
                        else if (substr_count($data['name'], '-'))
                            $_name = str_replace('-', '_minus', $data['name']);

                        $_name = strtolower($_name);

                        $group = Group::create([
                            'name' => strtoupper($data['name']),
                            '_name' => $_name
                        ]);

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Group Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($group, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($group)) return redirect()->back();
                else {

                    $group = $this->group($group);

                    if (empty($group)) return redirect()->back();
                    else {

                        $data = [ 'name' => $group->name ];

                        $request->session()->put([ 'group'=>$group->id ]);

                        return view('group.group', [
                            'title'=>'Groups', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editGroup', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Edit Group.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $group = $request->session()->get('group');

                    $group = Group::where('id', $group)->first();

                    if (empty($group)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateGroup($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_group = Group::find($group->id);

                            if ($group->name != $data['name']) {

                                $validator = $this->validateGroup($data);

                                if ($validator->fails())
                                    return redirect()->back()->withErrors($validator)->withInputs();
                                else {

                                    $_group->name = strtoupper($data['name']);

                                    if (substr_count($data['name'], '+'))
                                        $_name = str_replace('+', '_plus', $data['name']);
                                    else if (substr_count($data['name'], '-'))
                                        $_name = str_replace('-', '_minus', $data['name']);

                                    $_group->_name = strtolower($_name);

                                }

                            }

                            $_group->save();

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Group Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($group, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($group)) return redirect()->back();
                else {

                    $group = $this->group($group);

                    if (empty($group)) return redirect()->back();
                    else {

                        $data = [ 'name' => $group->name ];

                        $request->session()->put([ 'group'=>$group->id ]);

                        return view('group.group', [
                            'title'=>'Groups', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteGroup', 'data'=>$data
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Delete Group.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $group = $request->session()->get('group');

                    $group = Group::where('id', $group)->first();

                    if (empty($group)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateGroup($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_group = Group::find($group->id);

                            if (substr_count($data['name'], '+'))
                                $_name = str_replace('+', '_plus', $data['name']);
                            else if (substr_count($data['name'], '-'))
                                $_name = str_replace('-', '_minus', $data['name']);

                            $_name = strtolower($_name);

                            if ($group->name == $data['name'] && $group->_name == $_name)
                                $_group->delete();

                            return redirect('/groups');
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Group View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($group, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($group)) return redirect()->back();
            else {

                $group = $this->group($group);

                if (empty($group)) return redirect()->back();
                else {

                    $data = [ 'name' => $group->name ];

                    return view('group.group', [
                        'title'=>'Groups', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('group.groups', [
                'title'=>'Groups', 'user'=>Auth::user(), 'restriction'=>$this->restriction_controller, 'stock'=>$this->stock()
            ]);
    }
}
