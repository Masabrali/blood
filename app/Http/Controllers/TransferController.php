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

class TransferController extends Controller
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
    * Get transfer Aggregates
    */
    public function aggregates($stock = null) {

        if (!Auth::check()) return;
        else {

            $transfers = $this->center_controller->centers();

            $groups = Group::all();

            $total = [ 'total_in'=>0, 'total_out'=>0 ];

            foreach ($transfers as $_transfer => $transfer) {

                $transfer = (Array) $transfer;

                $_total_in = 0;

                $_total_out = 0;

                foreach ($groups as $group) {

                    $_transfers_in = transfer::where('to', $transfer['_center'])->where('group', $group->id);

                    $_transfers_out = transfer::where('from', $transfer['_center'])->where('group', $group->id);

                    $_transfers_in = $this->filter_controller->time_filters($_transfers_in, 'transfers', $stock);

                    $_transfers_out = $this->filter_controller->time_filters($_transfers_out, 'transfers', $stock);

                    $transfer[$group->_name.'_in'] = $_transfers_in->sum('units');

                    $_total_in += $transfer[$group->_name.'_in'];

                    $transfer[$group->_name.'_out'] = $_transfers_out->sum('units');

                    $_total_out += $transfer[$group->_name.'_out'];

                    if (!isset($total[$group->_name.'_in'])) $total[$group->_name.'_in'] = 0;

                    if (!isset($total[$group->_name.'_out'])) $total[$group->_name.'_out'] = 0;

                    $total[$group->_name.'_in'] += $transfer[$group->_name.'_in'];

                    $total[$group->_name.'_out'] += $transfer[$group->_name.'_out'];
                }

                $transfer['total_in'] = $_total_in;

                $transfer['total_out'] = $_total_out;

                $total['total_in'] += $_total_in;

                $total['total_out'] += $_total_out;

                $transfers[$_transfer] = (Object) $transfer;
            }

            $total = (Object) $total;

            return (Object) [ 'aggregates'=>$transfers, 'total'=>$total ];
        }
    }
    /**
    * Get Transfers
    */
    public function transfers($transfer = null) {
        if (!Auth::check()) return;
        else {

            $transfers = DB::table('transfers')
                              ->join('users', 'transfers.user', '=', 'users.id')
                              ->join('centers AS from_centers', 'transfers.from', '=', 'from_centers.id')
                              ->join('centers AS to_centers', 'transfers.to', '=', 'to_centers.id')
                              ->join('districts AS from_districts', 'from_centers.district', '=', 'from_districts.id')
                              ->join('districts AS to_districts', 'to_centers.district', '=', 'to_districts.id')
                              ->join('regions AS from_regions', 'from_districts.region', '=', 'from_regions.id')
                              ->join('regions AS to_regions', 'to_districts.region', '=', 'to_regions.id')
                              ->join('zones AS from_zones', 'from_regions.zone', '=', 'from_zones.id')
                              ->join('zones AS to_zones', 'to_regions.zone', '=', 'to_zones.id')
                              ->join('groups', 'transfers.group', '=', 'groups.id')
                              ->select(
                                    'transfers.id AS _transfer', 'users.id AS _user', 'users.firstname AS firstname', 'users.lastname AS lastname',
                                    'from_zones.id AS _from_zone', 'to_zones.id AS _to_zone', 'from_zones.name AS from_zone', 'to_zones.name AS to_zone',
                                    'from_regions.id AS _from_region', 'to_regions.id AS _to_region', 'from_regions.name AS from_region', 'to_regions.name AS to_region',
                                    'from_districts.id AS _from_district', 'to_districts.id AS _to_district', 'from_districts.name AS from_district', 'to_districts.name AS to_district',
                                    'from_centers.id AS _from_center', 'to_centers.id AS _to_center', 'from_centers.name AS from_center', 'to_centers.name AS to_center',
                                    'groups.id AS _group', 'groups.name AS group', 'units', 'transfers.date AS date'
                              )
                              ->where('transfers.deleted_at', NULL)
                              ->where('from_centers.deleted_at', NULL)
                              ->where('to_centers.deleted_at', NULL)
                              ->where('from_districts.deleted_at', NULL)
                              ->where('to_districts.deleted_at', NULL)
                              ->where('from_regions.deleted_at', NULL)
                              ->where('to_regions.deleted_at', NULL)
                              ->where('from_zones.deleted_at', NULL)
                              ->where('to_zones.deleted_at', NULL);

            $transfers = $this->restriction_controller->restrictions($transfers, true);
            $transfers = $this->filter_controller->location_filters($transfers, true);
            $transfers = $this->filter_controller->time_filters($transfers, 'transfers');

            if (isset($transfer) && $transfer != null)
                return $transfers->where('transfers.id', $transfer)->first();
            else
                return $transfers->orderBy('date', 'DESC')
                                  ->orderBy('_from_zone', 'ASC')
                                  ->orderBy('_from_region', 'ASC')
                                  ->orderBy('_from_district', 'ASC')
                                  ->orderBy('_from_center', 'ASC')
                                  ->get();
        }
    }
    /**
    * Get Transfer
    */
    public function transfer($transfer) {

        if (!Auth::check()) return;
        else {
            if (empty($transfer)) return;
            else return $this->transfers($transfer);
        }
    }

    /**
     * Add a New Transfer.
     * Validate Transfer
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateTransfer($data, $units = null) {

        $group = Group::where('id', $data['group'])->first();

        $max = ((Array) $this->center_controller->stock($data['from_center'])->stock[0])[$group->_name];

        $_max = ((Array) $this->center_controller->stock($data['to_center'])->storage)[$group->_name];

        if ($_max < $max) $max = $_max;

        $validator = Validator::make($data, [
            'from_zone' => 'required|exists:zones,id',
            'from_region' => 'required|exists:regions,id',
            'from_district' => 'required|exists:districts,id',
            'from_center' => 'required|exists:centers,id|different:to_center',
            'to_zone' => 'required|exists:zones,id',
            'to_region' => 'required|exists:regions,id',
            'to_district' => 'required|exists:districts,id',
            'to_center' => 'required|exists:centers,id|different:from_center',
            'group' => 'required|exists:groups,id',
            'units' => 'required|numeric|min:1|max:'.$max,
            'date' => 'required|date:<='.date('Y-m-d H:i:s')
        ]);
        $validator->validate();

        return $validator;
    }

    /**
     * Show the Blood Transfer Add Form.
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

            return view('transfer.transfer', [
                'title'=>'Transfers', 'user'=>Auth::user(), 'handler'=>'addTransfer', 'previous'=>$previous
            ]);
        }
    }

    /**
     * Add a New Transfer.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {

            $data = $request->all();

            $validator = $this->validateTransfer($data);

            if ($validator->fails())
                return redirect()->back()->withErrors($validator)->withInputs();
            else {

                $transfer = Transfer::create([
                    'from' => $data['from_center'],
                    'to' => $data['to_center'],
                    'group' => $data['group'],
                    'units' => $data['units'],
                    'date' => date('Y-m-d H:i:s', strtotime($data['date'].date(' H:i:s')))
                ]);

                return redirect()->back()->with('success', true);
            }
        }
    }

    /**
     * Show the Blood Transfer Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($transfer, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($transfer)) return redirect()->back();
            else {

                $transfer = $this->transfer($transfer);

                if (empty($transfer)) return redirect()->back();
                else {

                    $data = [
                        'from_zone' => $transfer->_from_zone,
                        'to_zone' => $transfer->_to_zone,
                        'from_region' => $transfer->_from_region,
                        'to_region' => $transfer->_to_region,
                        'from_district' => $transfer->_from_district,
                        'to_district' => $transfer->_to_district,
                        'from_center' => $transfer->_from_center,
                        'to_center' => $transfer->_to_center,
                        'group' => $transfer->_group,
                        'units' => $transfer->units,
                        'date' => date('Y-m-d', strtotime($transfer->date))
                    ];

                    $request->session()->put([ 'transfer'=>$transfer->_transfer ]);

                    return view('transfer.transfer', [
                        'title'=>'Transfers', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editTransfer', 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Edit Transfer
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $transfer = $request->session()->get('transfer');

                $transfer = Transfer::where('id', $transfer)->first();

                if (empty($transfer)) return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateTransfer($data, $transfer->units);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $_transfer = Transfer::find($transfer->id);

                        if ($transfer->from != $data['from_center'])
                            $_transfer->from = $data['from_center'];

                        if ($transfer->to != $data['to_center'])
                            $_transfer->to = $data['to_center'];

                        if ($transfer->group != $data['group'])
                            $_transfer->group = $data['group'];

                        if ($transfer->units != $data['units'])
                            $_transfer->units = $data['units'];

                        if (date('Y-m-d', strtotime($transfer->date)) != date('Y-m-d', strtotime($data['date'])))
                            $_transfer->date = date('Y-m-d', strtotime($data['date']));

                        $_transfer->save();

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Transfer Delete Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteForm($transfer, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($transfer)) return redirect()->back();
            else {

                $transfer = $this->transfer($transfer);

                if (empty($transfer)) return redirect()->back();
                else {

                    $data = [
                        'from_zone' => $transfer->_from_zone,
                        'to_zone' => $transfer->_to_zone,
                        'from_region' => $transfer->_from_region,
                        'to_region' => $transfer->_to_region,
                        'from_district' => $transfer->_from_district,
                        'to_district' => $transfer->_to_district,
                        'from_center' => $transfer->_from_center,
                        'to_center' => $transfer->_to_center,
                        'group' => $transfer->_group,
                        'units' => $transfer->units,
                        'date' => date('Y-m-d', strtotime($transfer->date))
                    ];

                    $request->session()->put([ 'transfer'=>$transfer->_transfer ]);

                    return view('transfer.transfer', [
                        'title'=>'Transfers', 'delete'=>true, 'user'=>Auth::user(), 'handler'=>'deleteTransfer', 'data'=>$data
                    ]);
                }
            }
        }
    }

    /**
     * Delete Transfer
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if ($request->method() != 'POST') return redirect()->back();
            else {

                $transfer = $request->session()->get('transfer');

                $transfer = Transfer::where('id', $transfer)->first();

                if (empty($transfer)) return redirect()->back();
                else {

                    $data = $request->all();

                    $validator = $this->validateTransfer($data, $transfer->units);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $_transfer = Transfer::find($transfer->id);

                        if ($transfer->from == $data['from_center'] && $transfer->to == $data['to_center'] && $transfer->group == $data['group'] && $transfer->units == $data['units'] && date('Y-m-d', strtotime($transfer->date)) == date('Y-m-d', strtotime($data['date'])))
                            $_transfer->delete();

                        return redirect('/transfers')->with('tab', 'individual');
                    }
                }
            }
        }
    }

    /**
     * Show the Blood Transfer View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($transfer, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($transfer)) return redirect()->back();
            else {

                $transfer = $this->transfer($transfer);

                if (empty($transfer)) return redirect()->back();
                else {

                    $data = [
                        'from_zone' => $transfer->_from_zone,
                        'to_zone' => $transfer->_to_zone,
                        'from_region' => $transfer->_from_region,
                        'to_region' => $transfer->_to_region,
                        'from_district' => $transfer->_from_district,
                        'to_district' => $transfer->_to_district,
                        'from_center' => $transfer->_from_center,
                        'to_center' => $transfer->_to_center,
                        'group' => $transfer->_group,
                        'units' => $transfer->units,
                        'date' => date('Y-m-d', strtotime($transfer->date))
                    ];

                    return view('transfer.transfer', [
                        'title'=>'Transfers', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('transfer.transfers', [
                'title'=>'Transfers', 'user'=>Auth::user(), 'transfers'=>$this->transfers(), 'aggregates'=>$this->aggregates()
            ]);
    }

}
