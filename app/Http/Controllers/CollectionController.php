<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use App\Http\Controllers\CenterController;
use App\Http\Controllers\RestrictionController;
use App\Http\Controllers\FilterController;

use App\Collection;
use App\Group;

class CollectionController extends Controller
{
    //

    /**
    * Controller Instances
    */
    protected $center_controller;
    protected $restriction_controller;
    protected $filter_controller;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->center_controller = new CenterController();
        $this->restriction_controller = new RestrictionController();
        $this->filter_controller = new FilterController();

        $this->middleware('auth');
        $this->middleware('user');
        $this->middleware('sessions');
    }

    /**
    * Get Collection Aggregates
    */
    public function aggregates($stock = null) {

        if (!Auth::check()) return;
        else {

            $collections = $this->center_controller->centers();

            $groups = Group::all();

            $total = [ 'total' => 0 ];

            foreach ($collections as $_collection => $collection) {

                $collection = (Array) $collection;

                $_total = 0;

                foreach ($groups as $group) {

                    $_collections = Collection::where('center', $collection['_center'])->where('group', $group->id);

                    $_collections = $this->filter_controller->time_filters($_collections, 'collections', $stock);

                    $collection[$group->_name] = $_collections->sum('units');

                    $_total += $collection[$group->_name];

                    if (!isset($total[$group->_name])) $total[$group->_name] = 0;

                    $total[$group->_name] += $collection[$group->_name];
                }

                $collection['total'] = $_total;

                $total['total'] += $_total;

                $collections[$_collection] = (Object) $collection;
            }

            $total = (Object) $total;

            return (Object) [ 'aggregates'=>$collections, 'total'=>$total ];
        }
    }

    /**
    * Get Collections
    */
    public function collections($collection = null) {

        if (!Auth::check()) return;
        else {

            $collections = DB::table('collections')
                              ->join('users', 'collections.user', '=', 'users.id')
                              ->join('centers', 'collections.center', '=', 'centers.id')
                              ->join('districts', 'centers.district', '=', 'districts.id')
                              ->join('regions', 'districts.region', '=', 'regions.id')
                              ->join('zones', 'regions.zone', '=', 'zones.id')
                              ->join('groups', 'collections.group', '=', 'groups.id')
                              ->select(
                                    'collections.id AS _collection', 'users.id AS _user', 'users.firstname AS firstname', 'users.lastname AS lastname', 'zones.id AS _zone', 'zones.name AS zone', 'regions.id AS _region', 'regions.name AS region', 'districts.id AS _district', 'districts.name AS district', 'centers.id AS _center', 'centers.name AS center', 'groups.id AS _group', 'groups.name AS group', 'units',
                                    'collections.date AS date'
                              )
                              ->where('collections.deleted_at', NULL)
                              ->where('centers.deleted_at', NULL)
                              ->where('districts.deleted_at', NULL)
                              ->where('regions.deleted_at', NULL)
                              ->where('zones.deleted_at', NULL);

            $collections = $this->restriction_controller->restrictions($collections);
            $collections = $this->filter_controller->location_filters($collections);
            $collections = $this->filter_controller->time_filters($collections, 'collections');

            if (isset($collection) && $collection != null)
                return $collections->where('collections.id', $collection)->first();
            else
                return $collections->orderBy('date', 'DESC')
                                  ->orderBy('zone', 'ASC')
                                  ->orderBy('region', 'ASC')
                                  ->orderBy('district', 'ASC')
                                  ->orderBy('center', 'ASC')
                                  ->get();
        }
    }

    /**
    * Get Collection
    */
    public function collection($collection) {

        if (!Auth::check()) return;
        else {
            if (empty($collection)) return;
            else return $this->collections($collection);
        }
    }

    /**
     * Add a New Collection.
     * Validate Collection
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateCollection($data) {

        $group = Group::where('id', $data['group'])->first();
        $stock = $this->center_controller->stock($data['center']);

        $storage = ((Array) $stock->storage)[$group->_name];
        $stock = ((Array) $stock->stock[0])[$group->_name];

        $max = $storage - $stock;

        $validator = Validator::make($data, [
            'zone' => 'required|exists:zones,id',
            'region' => 'required|exists:regions,id',
            'district' => 'required|exists:districts,id',
            'center' => 'required|exists:centers,id',
            'group' => 'required|exists:groups,id',
            'units' => 'required|integer|min:1|max:'.$max,
            'date' => 'required|date:<='.date('Y-m-d H:i:s')
        ]);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood Collection Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {

            $previous = $request->session()->get('previous');

            if (empty($previous)) {
                $previous = URL::previous();

                $request->session()->put([ 'previous'=>$previous ]);
            }

            return view('collection.collect', [
                'title'=>'Collections', 'user'=>Auth::user(), 'handler'=>'addCollection', 'previous'=>$previous
            ]);
        }
    }

    /**
     * Add a New Collection.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $data = $request->all();

                $validator = $this->validateCollection($data);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {

                    $collection = Collection::create([
                        'center' => $data['center'],
                        'group' => $data['group'],
                        'units' => $data['units'],
                        'date' => date('Y-m-d H:i:s', strtotime($data['date'].date(' H:i:s')))
                    ]);

                    return redirect()->back()->with('success', true);
                }
            }
        }
    }

    /**
     * Show the Blood Collection Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($collection, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($collection)) return redirect()->back();
            else {

                $collection = $this->collection($collection);

                if (empty($collection)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $collection->_zone,
                        'region' => $collection->_region,
                        'district' => $collection->_district,
                        'center' => $collection->_center,
                        'group' => $collection->_group,
                        'units' => $collection->units,
                        'date' => date('Y-m-d', strtotime($collection->date))
                    ];

                    $request->session()->put([ 'collection'=>$collection->_collection ]);

                    return view('collection.collect', [
                        'title'=>'Collections', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editCollection', 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Edit Collection.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $collection = $request->session()->get('collection');

                $collection = Collection::where('id', $collection)->first();

                if (empty($collection)) return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateCollection($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $_collection = Collection::find($collection->id);

                        if ($collection->center != $data['center'])
                            $_collection->center = $data['center'];

                        if ($collection->group != $data['group'])
                            $_collection->group = $data['group'];

                        if ($collection->units != $data['units'])
                            $_collection->units = $data['units'];

                        if (date('Y-m-d', strtotime($collection->date)) != date('Y-m-d', strtotime($data['date'])))
                            $_collection->date = date('Y-m-d', strtotime($data['date']));

                        $_collection->save();

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Collection Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($collection, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($collection)) return redirect()->back();
            else {

                $collection = $this->collection($collection);

                if (empty($collection)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $collection->_zone,
                        'region' => $collection->_region,
                        'district' => $collection->_district,
                        'center' => $collection->_center,
                        'group' => $collection->_group,
                        'units' => $collection->units,
                        'date' => date('Y-m-d', strtotime($collection->date))
                    ];

                    $request->session()->put([ 'collection'=>$collection->_collection ]);

                    return view('collection.collect', [
                        'title'=>'Collections', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteCollection', 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Delete Collection.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $collection = $request->session()->get('collection');

                $collection = Collection::where('id', $collection)->first();

                if (empty($collection)) return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateCollection($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $_collection = Collection::find($collection->id);

                        if ($collection->center == $data['center'] && $collection->group == $data['group'] && $collection->units == $data['units'] && date('Y-m-d', strtotime($collection->date)) == date('Y-m-d', strtotime($data['date'])))
                            $_collection->delete();

                        return redirect('/collections')->with('tab', 'individual');
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Collection View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($collection, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($collection)) return redirect()->back();
            else {

                $collection = $this->collection($collection);

                if (empty($collection)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $collection->_zone,
                        'region' => $collection->_region,
                        'district' => $collection->_district,
                        'center' => $collection->_center,
                        'group' => $collection->_group,
                        'units' => $collection->units,
                        'date' => date('Y-m-d', strtotime($collection->date))
                    ];

                    return view('collection.collect', [
                        'title'=>'Collections', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Show the Blood Collections.
     *
     * @return \Illuminate\Http\Response
     */
    public function load(Request $request) {

        if (!Auth::check()) return redirect('/');
        else
            return view('collection.collections', [
                'title'=>'Collections', 'user'=>Auth::user(), 'collections'=>$this->collections(), 'aggregates'=>$this->aggregates()
            ]);
    }

}
