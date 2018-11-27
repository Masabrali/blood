<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\SMSController;
use App\Http\Controllers\ImageController;

use App\Mail\UserPassword;

use App\User;
use App\Restriction;
use App\Filter;

class UserController extends Controller
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
    * Get the current Sock Linked to the User
    */
    public function stock() {

        if (!Auth::check()) return;
        else {

            $stock = $this->users();

            $total = [
                'collections'=>0, 'transfers_in'=>0, 'transfers_out'=>0, 'distributions'=>0, 'total'=>0
            ];

            foreach ($stock as $__stock => $_stock) {

                $_stock = (Array) $_stock;

                $collections = DB::table('collections')
                                ->join('groups', 'collections.group', 'groups.id')
                                ->join('centers', 'collections.center', '=', 'centers.id')
                                ->join('districts', 'centers.district', '=', 'districts.id')
                                ->join('regions', 'districts.region', '=', 'regions.id')
                                ->join('zones', 'regions.zone', '=', 'zones.id')
                                ->where('collections.user', $_stock['_user'])
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
                                ->where('transfers.user', $_stock['_user'])
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
                                ->where('transfers.user', $_stock['_user'])
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
                                ->where('distributions.user', $_stock['_user'])
                                ->where('distributions.deleted_at', NULL);
                $distributions = $this->restriction_controller->restrictions($distributions);
                $distributions = $this->filter_controller->location_filters($distributions);
                $distributions = $this->filter_controller->time_filters($distributions, 'distributions', true);
                $distributions = $distributions->sum('units');

                $_stock['distributions'] = $distributions;

                $total['distributions'] += $distributions;

                $_stock['units'] = $collections + $transfers_in - $transfers_out - $distributions;

                $total['total'] += $_stock['units'];

                $stock[$__stock] = (Object) $_stock;
            }

            $total = (Object) $total;

            return (Object) [ 'stock'=>$stock, 'total'=>$total ];

        }
    }

    /**
    * Get Users
    */
    public function users($user = null) {

        if (!Auth::check()) return;
        else {

            $users = DB::table('users')
                      ->join('roles', 'users.role_id', '=', 'roles.id')
                      ->join('restrictions', 'restrictions.user', '=', 'users.id')
                      ->leftjoin('centers', 'restrictions.center', '=', 'centers.id')
                      ->leftjoin('districts', 'restrictions.district', '=', 'districts.id')
                      ->leftjoin('regions', 'restrictions.region', '=', 'regions.id')
                      ->leftjoin('zones', 'restrictions.zone', '=', 'zones.id')
                      ->select(
                          'users.id AS _user', 'roles.id AS _role', 'roles.display_name AS role', 'firstname', 'middlename', 'lastname', 'email', 'phone', 'avatar', 'zones.id AS _zone', 'zones.name AS zone', 'regions.id AS _region', 'regions.name AS region', 'districts.id AS _district', 'districts.name AS district', 'centers.id AS _center', 'centers.name AS center', 'users.deleted_at AS deleted_at'
                      )
                      ->orderBy('role')
                      ->orderBy('firstname')
                      ->orderBy('zone')
                      ->orderBy('region')
                      ->orderBy('district')
                      ->orderBy('center');

            if (!isset($user) || $user == null) {
                $users = $this->restriction_controller->restrictions($users);
                $users = $this->filter_controller->location_filters($users);
            }

            if (isset($user) && $user != null)
                return $users->where('users.id', $user)->first();
            else return $users->get();

        }
    }

    /**
    * Get User
    */
    public function user($user) {

        if (!Auth::check()) return;
        else {
            if (empty($user)) return;
            else return $this->users($user);
        }
    }

    /**
    * Generate Password Function
    */
    protected function generatePassword($data) {

        if (isset($data) && !empty($data)) {

            while(true) {

                $choice = random_int(0, 1);
                $_choice = random_int(0, 1);
                $_password = bin2hex(random_bytes(6));

                $password = "";

                if ($_choice == 0)
                    while ($choice < 12) {
                        $password .= $_password[$choice];
                        $choice += 2;
                    }
                else if ($_choice == 1) {
                    $choice = 11 - $choice;
                    while ($choice >= 0) {
                        $password .= $_password[$choice];
                        $choice -= 2;
                    }
                }

                $validator = Validator::make([
                    'password'=>$password, '_password'=>bcrypt($password)
                ],[
                    'password'=>'required|string|size:6',
                    '_password'=>'required|string|unique:users,password'
                ]);

                if (!$validator->fails()) break;

            }

            return $password;

        } else return false;
    }

    /**
    * Send Password Function
    */
    protected function sendPassword($user, $password, Request $request) {

        if (!$user || !isset($user) || empty($user)) return redirect()->back();
        else {

            if (!isset($user->email) && !isset($user->phone)) return redirect()->back();
            else {

                if (empty($user->email) && empty($user->phone))
                    return redirect()->back();
                else {

                    if (!empty($user->email))
                        Mail::to($user->email)
                              ->send(new UserPassword($user, $user->email, $password));
                    else if (!empty($user->phone))
                        SMSController::send($user->phone, "Your NBTP Database username is $user->phone and password is $password");

                    return true;
                }
            }
        }
    }

    /**
     * Phone regular expression.
     *
     * @var string
     */
    protected $phone_regex = "/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/";

    /**
     * Add a New User.
     * Validate User
     *
     * @return \Illuminate\Http\Response
     */
    protected function validateUser($data, $edit = null) {

        if (isset($data['email']) && $data['email'] != NULL)
            $data['email'] = strtolower($data['email']);

        $validation = [
            'firstname' => 'required|string|max:255',
            'middlename' => 'sometimes|string|nullable|max:255',
            'lastname' => 'required|string|max:255',
            'role' => 'required|exists:roles,id',
            'zone' => 'sometimes',
            'region' => 'sometimes',
            'district' => 'sometimes',
            'center' => 'sometimes'
        ];

        $email = 'required_without:phone';
        if (!isset($edit)) $email .= '|unique:users,email|email';

        $phone = 'required_without:email';
        if (!isset($edit)) $phone .= '|min:10|unique:users,phone|regex:'.$this->phone_regex;

        if ((!isset($data['email']) && !isset($data['phone'])) || (empty($data['email']) && empty($data['phone']))) {

            $validation['email'] = $email;

            $validation['phone'] = $phone;

        } else {

            if (isset($data['email']) && !empty($data['email']) && $data['email'] != NULL)
                $validation['email'] = $email;

            if (isset($data['phone']) && !empty($data['phone']) && $data['phone'] != NULL)
                $validation['phone'] = $phone;

        }

        if (isset($data['zone']) && !empty($data['zone']))
            $validation['zone'] .= '|exists:zones,id';

        if (isset($data['region']) && !empty($data['region']))
            $validation['region'] .= '|exists:regions,id';

        if (isset($data['district']) && !empty($data['district']))
            $validation['district'] .= '|exists:districts,id';

        if (isset($data['center']) && !empty($data['center']))
            $validation['center'] .= '|exists:centers,id';

        if (!isset($edit)) $validation['password'] = 'required|string|min:6';

        $validator = Validator::make($data, $validation);

        $validator->validate();

        return $validator;
    }

    /**
     * Show the User Add Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else
                return view('user.user', [
                    'title'=>'Users', 'user'=>Auth::user(), 'handler'=>'addUser'
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
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $data = $request->all();

                    $data['password'] = $this->generatePassword($data);

                    $validator = $this->validateUser($data);

                    if ($validator->fails())
                        return redirect()->back()->withErrors($validator)->withInputs();
                    else {

                        $user = User::create([
                            'registrar' => Auth::id(),
                            'role_id' => $data['role'],
                            'firstname' => ucfirst(strtolower($data['firstname'])),
                            'middlename' => ucfirst(strtolower($data['middlename'])),
                            'lastname' => ucfirst(strtolower($data['lastname'])),
                            'email' => strtolower($data['email']),
                            'phone' => $data['phone'],
                            'password' => bcrypt($data['password'])
                        ]);

                        if (!isset($data['zone']) || empty($data['zone'])) $data['zone'] = NULL;

                        if (!isset($data['zone']) || empty($data['zone']) || !isset($data['region']) || empty($data['region']))
                            $data['region'] = NULL;

                        if (!isset($data['zone']) || empty($data['zone']) || !isset($data['region']) || empty($data['region']) || !isset($data['district']) || empty($data['district']))
                            $data['district'] = NULL;

                        if (!isset($data['zone']) || empty($data['zone']) || !isset($data['region']) || empty($data['region']) || !isset($data['district']) || empty($data['district']) || !isset($data['center']) || empty($data['center']))
                            $data['center'] = NULL;

                        $restriction = Restriction::create([
                            'user' => $user->id,
                            'zone' => $data['zone'],
                            'region' => $data['region'],
                            'district' => $data['district'],
                            'center' => $data['center']
                        ]);

                        $filter = Filter::create([
                            'user' => $user->id,
                            'zone' => $data['zone'],
                            'region' => $data['region'],
                            'district' => $data['district'],
                            'center' => $data['center'],
                            'from' => date('Y-m-01'),
                            'to' => date('Y-m-t')
                        ]);

                        $this->sendPassword($user, $data['password'], $request);

                        return redirect()->back()->with('success', true);
                    }
                }
            }
        }
    }

    /**
     * Show the User Edit Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm($user, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($user)) return redirect()->back();
                else {

                    $user = $this->user($user);

                    if (empty($user)) return redirect()->back();
                    else {

                        $data = [
                            'role' => $user->_role,
                            'firstname' => $user->firstname,
                            'middlename' => $user->middlename,
                            'lastname' => $user->lastname,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'zone' => $user->_zone,
                            'region' => $user->_region,
                            'district' => $user->_district,
                            'center' => $user->_center
                        ];

                        $request->session()->put([ 'user'=>$user->_user ]);

                        return view('user.user', [
                            'title'=>'Users', 'edit'=>true, 'user'=>Auth::user(), 'handler'=>'editUser', 'data'=>$data
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
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $user = $request->session()->get('user');

                    $user = User::where('id', $user)->first();

                    if (empty($user)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateUser($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_user = User::find($user->id);

                            if ($user->firstname != $data['firstname'])
                                $_user->firstname = $data['firstname'];

                            if ($user->middlename != $data['middlename'])
                                $_user->middlename = $data['middlename'];

                            if ($user->lastname != $data['lastname'])
                                $_user->lastname = $data['lastname'];

                            if ($user->role_id != $data['role'])
                                $_user->role_id = $data['role'];

                            if ($user->email != $data['email']) {
                                $_user->email = $data['email'];
                                $_user->email_verification = NULL;
                            }

                            if ($user->phone != $data['phone']) {
                                $_user->phone = $data['phone'];
                                $_user->phone_verification = NULL;
                            }

                            $_user->save();

                            $restriction = Restriction::where('user', $user->id)->first();

                            if (!empty($restriction)) {

                                $_restriction = Restriction::find($restriction->id);

                                if (!isset($data['zone']) || empty($data['zone']))
                                    $data['zone'] = NULL;

                                if (!isset($data['zone']) || empty($data['zone']) || !isset($data['region']) || empty($data['region']))
                                    $data['region'] = NULL;

                                if (!isset($data['zone']) || empty($data['zone']) || !isset($data['region']) || empty($data['region']) || !isset($data['district']) || empty($data['district']))
                                    $data['district'] = NULL;

                                if (!isset($data['zone']) || empty($data['zone']) || !isset($data['region']) || empty($data['region']) || !isset($data['district']) || empty($data['district']) || !isset($data['center']) || empty($data['center']))
                                    $data['center'] = NULL;

                                if ($restriction->zone != $data['zone'])
                                    $_restriction->zone = $data['zone'];

                                if ($restriction->region != $data['region'])
                                    $_restriction->region = $data['region'];

                                if ($restriction->district != $data['district'])
                                    $_restriction->district = $data['district'];

                                if ($restriction->center != $data['center'])
                                    $_restriction->center = $data['center'];

                                $_restriction->save();

                                $filter = Filter::where('user', $user->id)->first();

                                if (!empty($filter)) {

                                    $_filter = Filter::find($filter->id);

                                    if ($filter->zone != $data['zone'])
                                        $_filter->zone = $data['zone'];

                                    if ($filter->region != $data['region'])
                                        $_filter->region = $data['region'];

                                    if ($filter->district != $data['district'])
                                        $_filter->district = $data['district'];

                                    if ($filter->center != $data['center'])
                                        $_filter->center = $data['center'];

                                    $_filter->save();
                                }
                            }

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the User Deactivate Form.
     *
     * @return \Illuminate\Http\Response
     */
    public function activationForm($user, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if (empty($user)) return redirect()->back();
                else {

                    $user = $this->user($user);

                    if (empty($user)) return redirect()->back();
                    else {

                        $data = [
                            'role' => $user->_role,
                            'firstname' => $user->firstname,
                            'middlename' => $user->middlename,
                            'lastname' => $user->lastname,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'zone' => $user->_zone,
                            'region' => $user->_region,
                            'district' => $user->_district,
                            'center' => $user->_center
                        ];

                        $request->session()->put([ 'user'=>$user->_user ]);

                        $activate = NULL;
                        $deactivate = NULL;
                        if (isset($user->deleted_at)) {
                            $activate = true;

                            $handler = 'activateUser';
                        } else {
                            $deactivate = true;

                            $handler = 'deactivateUser';
                        }

                        return view('user.user', [
                            'title'=>'Users', 'activate'=>$activate, 'deactivate'=>$deactivate, 'user'=>Auth::user(), 'handler'=>$handler, 'data'=>$data
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
    public function deactivate(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $user = $request->session()->get('user');

                    $user = $this->user($user);

                    if (empty($user)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateUser($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_user = User::find($user->_user);

                            if ($user->_role == $data['role'] && $user->firstname == $data['firstname'] && $user->middlename == $data['middlename'] && $user->lastname == $data['lastname'] && $user->email == $data['email'] && $user->phone == $data['phone'] && $user->_zone == $data['zone'] && $user->_region == $data['region'] && $user->_district == $data['district'] && $user->_center == $data['center'])
                                $_user->delete();

                            return redirect()->back()->with('success', true);
                        }
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
    public function activate(Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (Auth::user()->role_id != 1) return redirect()->back();
            else {
                if ($request->method() != 'POST') return redirect()->back();
                else {

                    $user = $request->session()->get('user');

                    $user = $this->user($user);

                    if (empty($user)) return redirect()->back();
                    else {

                        $data = $request->all();

                        $validator = $this->validateUser($data, true);

                        if ($validator->fails())
                            return redirect()->back()->withErrors($validator)->withInputs();
                        else {

                            $_user = User::where('id', $user->_user)->withTrashed()->first();

                            if ($user->_role == $data['role'] && $user->firstname == $data['firstname'] && $user->middlename == $data['middlename'] && $user->lastname == $data['lastname'] && $user->email == $data['email'] && $user->phone == $data['phone'] && $user->_zone == $data['zone'] && $user->_region == $data['region'] && $user->_district == $data['district'] && $user->_center == $data['center'])
                                $_user->restore();

                            return redirect()->back()->with('success', true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the User View.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($user, Request $request) {

        if (!Auth::check()) return redirect('/');
        else {
            if (empty($user)) return redirect()->back();
            else {

                $user = $this->user($user);

                if (empty($user)) return redirect()->back();
                else {

                    $data = [
                        'role' => $user->_role,
                        'firstname' => $user->firstname,
                        'middlename' => $user->middlename,
                        'lastname' => $user->lastname,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'zone' => $user->_zone,
                        'region' => $user->_region,
                        'district' => $user->_district,
                        'center' => $user->_center
                    ];

                    return view('user.user', [
                        'title'=>'Users', 'view'=>true, 'user'=>Auth::user(), 'data'=>$data
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
            return view('user.users', [
                'title'=>'Users', 'user'=>Auth::user(), 'restriction'=>$this->restriction_controller, 'stock'=>$this->stock()
            ]);
    }
}
