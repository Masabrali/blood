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

use App\Distribution;
use App\Collection;
use App\Transfer;
use App\Group;

class DistributionController extends Controller
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
    * Get distribution Aggregates
    */
    public function aggregates($stock = null) {

        if (!Auth::check()) return;
        else {

            $distributions = $this->center_controller->centers();

            $groups = Group::all();

            $total = [ 'total'=>0 ];

            foreach ($distributions as $_distribution => $distribution) {

                $distribution = (Array) $distribution;

                $_total = 0;

                foreach ($groups as $group) {

                    $_distributions = Distribution::where('center', $distribution['_center'])->where('group', $group->id);

                    $_distributions = $this->filter_controller->time_filters($_distributions, 'distributions', $stock);

                    $distribution[$group->_name] = $_distributions->sum('units');

                    $_total += $distribution[$group->_name];

                    if (!isset($total[$group->_name])) $total[$group->_name] = 0;

                    $total[$group->_name] += $distribution[$group->_name];
                }

                $distribution['total'] = $_total;

                $total['total'] += $_total;

                $distributions[$_distribution] = (Object) $distribution;
            }

            $total = (Object) $total;

            return (Object) [ 'aggregates'=>$distributions, 'total'=>$total ];
        }
    }

    /**
    * Get Distributions
    */
    public function distributions($distribution = null) {

        if (!Auth::check()) return;
        else {

            $distributions = DB::table('distributions')
                              ->join('users', 'distributions.user', '=', 'users.id')
                              ->join('centers', 'distributions.center', '=', 'centers.id')
                              ->join('districts', 'centers.district', '=', 'districts.id')
                              ->join('regions', 'districts.region', '=', 'regions.id')
                              ->join('zones', 'regions.zone', '=', 'zones.id')
                              ->join('groups', 'distributions.group', '=', 'groups.id')
                              ->select(
                                    'distributions.id AS _distribution', 'users.id AS _user', 'users.firstname AS firstname', 'users.lastname AS lastname', 'zones.id AS _zone', 'zones.name AS zone', 'regions.id AS _region', 'regions.name AS region', 'districts.id AS _district', 'districts.name AS district', 'centers.id AS _center', 'centers.name AS center', 'groups.id AS _group', 'groups.name AS group', 'units', 'recepient', 'distributions.date AS date'
                              )
                              ->where('distributions.deleted_at', NULL)
                              ->where('centers.deleted_at', NULL)
                              ->where('districts.deleted_at', NULL)
                              ->where('regions.deleted_at', NULL)
                              ->where('zones.deleted_at', NULL);

            $distributions = $this->restriction_controller->restrictions($distributions);
            $distributions = $this->filter_controller->location_filters($distributions);
            $distributions = $this->filter_controller->time_filters($distributions, 'distributions');

            if (isset($distribution) && $distribution != null)
                return $distributions->where('distributions.id', $distribution)->first();
            else
                return $distributions->orderBy('date', 'DESC')
                                    ->orderBy('zone', 'ASC')
                                    ->orderBy('region', 'ASC')
                                    ->orderBy('district', 'ASC')
                                    ->orderBy('center', 'ASC')
                                    ->get();
        }
    }

    /**
    * Get Distribution
    */
    public function distribution($distribution) {

        if (!Auth::check()) return;
        else {
            if (empty($distribution)) return;
            else return $this->distributions($distribution);
        }
    }

    /**
     * Add a New Distribution.
     * Validate Distribution
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateDistribution($data, $units = null) {

        $group = Group::where('id', $data['group'])->first();

        $max = ((Array) $this->center_controller->stock($data['center'])->stock[0])[$group->_name];

        if (isset($units) && $units != null) $max += $units;

        $validator = Validator::make($data, [
            'zone' => 'required|exists:zones,id',
            'region' => 'required|exists:regions,id',
            'district' => 'required|exists:districts,id',
            'center' => 'required|exists:centers,id',
            'group' => 'required|exists:groups,id',
            'units' => 'required|numeric|min:1|max:'.$max,
            'recepient' => 'required|string',
            'date' => 'required|date:<='.date('Y-m-d H:i:s')
        ]);
        $validator->validate();

        return $validator;
    }

    /**
      * Show the Blood Distribution Add Form.
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

            return view('distribution.distribute', [
                'title'=>'Distributions', 'user'=>Auth::user(), 'handler'=>'addDistribution', 'previous'=>$previous
            ]);
        }
    }

    /**
     * Add a New Distribution
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $data = $request->all();

                $validator = $this->validateDistribution($data);

                if ($validator->fails())
                    return redirect()->back()->withErrors($validator)->withInputs();
                else {

                    $distribution = Distribution::create([
                        'center' => $data['center'],
                        'group' => $data['group'],
                        'units' => $data['units'],
                        'recepient' => $data['recepient'],
                        'date' => date('Y-m-d H:i:s', strtotime($data['date'].date(' H:i:s')))
                    ]);

                    return redirect()->back()->with('success', true);
                }
            }
        }
    }

    /**
     * Show the Blood Distribution Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($distribution, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($distribution)) return redirect()->back();
            else {

                $distribution = $this->distribution($distribution);

                if (empty($distribution)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $distribution->_zone,
                        'region' => $distribution->_region,
                        'district' => $distribution->_district,
                        'center' => $distribution->_center,
                        'group' => $distribution->_group,
                        'units' => $distribution->units,
                        'recepient' => $distribution->recepient,
                        'date' => date('Y-m-d', strtotime($distribution->date))
                    ];

                    $request->session()->put([ 'distribution'=>$distribution->_distribution ]);

                    return view('distribution.distribute', [
                        'title'=>'Distributions', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editDistribution', 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Edit Distribution
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $distribution = $request->session()->get('distribution');

                $distribution = Distribution::where('id', $distribution)->first();

                if (empty($distribution)) return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateDistribution($data, $distribution->units);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $_distribution = Distribution::find($distribution->id);

                        if ($distribution->center != $data['center'])
                            $_distribution->center = $data['center'];

                        if ($distribution->group != $data['group'])
                            $_distribution->group = $data['group'];

                        if ($distribution->units != $data['units'])
                            $_distribution->units = $data['units'];

                        if ($distribution->recepient != $data['recepient'])
                            $_distribution->recepient = $data['recepient'];

                        if (date('Y-m-d', strtotime($distribution->date)) != date('Y-m-d', strtotime($data['date'])))
                            $_distribution->date = date('Y-m-d', strtotime($data['date']));

                        $_distribution->save();

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Distribution Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($distribution, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($distribution)) return redirect()->back();
            else {

                $distribution = $this->distribution($distribution);

                if (empty($distribution)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $distribution->_zone,
                        'region' => $distribution->_region,
                        'district' => $distribution->_district,
                        'center' => $distribution->_center,
                        'group' => $distribution->_group,
                        'units' => $distribution->units,
                        'recepient' => $distribution->recepient,
                        'date' => date('Y-m-d', strtotime($distribution->date))
                    ];

                    $request->session()->put([ 'distribution'=>$distribution->_distribution ]);

                    return view('distribution.distribute', [
                        'title'=>'Distributions', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteDistribution', 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Delete Distribution.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $distribution = $request->session()->get('distribution');

                $distribution = Distribution::where('id', $distribution)->first();

                if (empty($distribution)) return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateDistribution($data, $distribution->units);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $_distribution = Distribution::find($distribution->id);

                        if ($distribution->center == $data['center'] && $distribution->group == $data['group'] && $distribution->units == $data['units'] && $distribution->recepient == $data['recepient'] && date('Y-m-d', strtotime($distribution->date)) == date('Y-m-d', strtotime($data['date'])))
                            $_distribution->delete();

                        return redirect('/distributions')->with('tab', 'individual');
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Distribution View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($distribution, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($distribution)) return redirect()->back();
            else {

                $distribution = $this->distribution($distribution);

                if (empty($distribution)) return redirect()->back();
                else {

                    $data = [
                        'zone' => $distribution->_zone,
                        'region' => $distribution->_region,
                        'district' => $distribution->_district,
                        'center' => $distribution->_center,
                        'group' => $distribution->_group,
                        'units' => $distribution->units,
                        'recepient' => $distribution->recepient,
                        'date' => date('Y-m-d', strtotime($distribution->date))
                    ];

                    return view('distribution.distribute', [
                        'title'=>'Distributions', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('distribution.distributions', [
                'title'=>'Distributions', 'user'=>Auth::user(), 'distributions'=>$this->distributions(), 'aggregates'=>$this->aggregates()
            ]);
    }

}
