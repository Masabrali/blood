<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// use Closure;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/react', function () {
    return view('react');
});

Route::get('/redux', function () {
    return view('redux');
});

Route::get('/user', function () {
    return view('redux');
});

Route::get('/games', function () {
    return view('redux');
});

Route::get('/game/{game}', function () {
    return view('redux');
});




Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();
Route::get('/register', function () {
    return redirect('/');
});

/** Dashboard */
Route::get('/dashboard', 'DashboardController@load')->name('dashboard');
Route::get('/home', function () {
    return redirect('/dashboard');
});

/** Filter */
Route::get('/filter', function () { return redirect()->back(); });
Route::any('/filter', 'FilterController@filter')->name('filter');
Route::get('/filter/reset', 'FilterController@reset')->name('resetFilter');

/** View */
Route::get('/collections', 'CollectionController@load')->name('collections');
Route::get('/collections/tabs/{tab}', function ($tab) {
    return redirect('/collections')->with('tab', $tab);
});
Route::get('/collections/collection/{collection}', 'CollectionController@view')->name('collection');
/** Add */
Route::get('/collections/add', 'CollectionController@addForm')->name('addCollectionForm');
Route::post('/collections/add', 'CollectionController@add')->name('addCollection');
/** Edit */
Route::get('/collections/edit/{collection}', 'CollectionController@editForm')->name('editCollectionForm');
Route::get('/collections/edit', function () {
    return redirect()->back();
});
Route::post('/collections/edit', 'CollectionController@edit')->name('editCollection');
/** Delete */
Route::get('/collections/delete/{collection}', 'CollectionController@deleteForm')->name('deleteCollectionForm');
Route::get('/collections/delete', function () {
    return redirect()->back();
});
Route::post('/collections/delete', 'CollectionController@delete')->name('deleteCollection');

/** View */
Route::get('/distributions', 'DistributionController@load')->name('distributions');
Route::get('/distributions/tabs/{tab}', function ($tab) {
    return redirect('/distributions')->with('tab', $tab);
});
Route::get('/distributions/distribution/{distribution}', 'DistributionController@view')->name('distribution');
/** Add */
Route::get('/distributions/add', 'DistributionController@addForm')->name('addDistributionForm');
Route::post('/distributions/add', 'DistributionController@add')->name('addDistribution');
/** Edit */
Route::get('/distributions/edit/{distribution}', 'DistributionController@editForm')->name('editDistributionForm');
Route::get('/distributions/edit', function () {
    return redirect()->back();
});
Route::post('/distributions/edit', 'DistributionController@edit')->name('editDistribution');
/** Delete */
Route::get('/distributions/delete/{distribution}', 'DistributionController@deleteForm')->name('deleteDistributionForm');
Route::get('/distributions/delete', function () {
    return redirect()->back();
});
Route::post('/distributions/delete', 'DistributionController@delete')->name('deleteDistribution');

/** View */
Route::get('/transfers', 'TransferController@load')->name('transfers');
Route::get('/transfers/tabs/{tab}', function ($tab) {
    return redirect('/transfers')->with('tab', $tab);
});
Route::get('/transfers/transfer/{transfer}', 'TransferController@view')->name('transfer');
/** Add */
Route::get('/transfers/add', 'TransferController@addForm')->name('addTransferForm');
Route::post('/transfers/add', 'TransferController@add')->name('addTransfer');
/** Edit */
Route::get('/transfers/edit/{transfer}', 'TransferController@editForm')->name('editTransferForm');
Route::get('/transfers/edit', function () {
    return redirect()->back();
});
Route::post('/transfers/edit', 'TransferController@edit')->name('editTransfer');
/** Delete */
Route::get('/transfers/delete/{transfer}', 'TransferController@deleteForm')->name('deleteTransferForm');
Route::get('/transfers/delete', function () {
    return redirect()->back();
});
Route::post('/transfers/delete', 'TransferController@delete')->name('deleteTransfer');

/** Zones */
Route::get('/zones', 'ZoneController@load')->name('zones');
Route::get('/zones/zone/{zone}', 'ZoneController@view')->name('zone');
/** Add */
Route::get('/zones/add', 'ZoneController@addForm')->name('addZoneForm');
Route::post('/zones/add', 'ZoneController@add')->name('addZone');
/** Edit */
Route::get('/zones/edit/{zone}', 'ZoneController@editForm')->name('editZoneForm');
Route::get('/zones/edit', function () {
    return redirect()->back();
});
Route::post('/zones/edit', 'ZoneController@edit')->name('editZone');
/** Delete */
Route::get('/zones/delete/{zone}', 'ZoneController@deleteForm')->name('deleteZoneForm');
Route::get('/zones/delete', function () {
    return redirect()->back();
});
Route::post('/zones/delete', 'ZoneController@delete')->name('deleteZone');

/** Regions */
Route::get('/regions', 'RegionController@load')->name('regions');
Route::get('/regions/region/{region}', 'RegionController@view')->name('region');
/** Add */
Route::get('/regions/add', 'RegionController@addForm')->name('addRegionForm');
Route::post('/regions/add', 'RegionController@add')->name('addRegion');
/** Edit */
Route::get('/regions/edit/{region}', 'RegionController@editForm')->name('editRegionForm');
Route::get('/regions/edit', function () {
    return redirect()->back();
});
Route::post('/regions/edit', 'RegionController@edit')->name('editRegion');
/** Delete */
Route::get('/regions/delete/{region}', 'RegionController@deleteForm')->name('deleteRegionForm');
Route::get('/regions/delete', function () {
    return redirect()->back();
});
Route::post('/regions/delete', 'RegionController@delete')->name('deleteRegion');

/** Districts */
Route::get('/districts', 'DistrictController@load')->name('districts');
Route::get('/districts/district/{district}', 'DistrictController@view')->name('district');
/** Add */
Route::get('/districts/add', 'DistrictController@addForm')->name('addDistrictForm');
Route::post('/districts/add', 'DistrictController@add')->name('addDistrict');
/** Edit */
Route::get('/districts/edit/{district}', 'DistrictController@editForm')->name('editDistrictForm');
Route::get('/districts/edit', function () {
    return redirect()->back();
});
Route::post('/districts/edit', 'DistrictController@edit')->name('editDistrict');
/** Delete */
Route::get('/districts/delete/{district}', 'DistrictController@deleteForm')->name('deleteDistrictForm');
Route::get('/districts/delete', function () {
    return redirect()->back();
});
Route::post('/districts/delete', 'DistrictController@delete')->name('deleteDistrict');

/** Centers */
Route::get('/centers', 'CenterController@load')->name('centers');
Route::get('/centers/center/{center}', 'CenterController@view')->name('center');
/** Add */
Route::get('/centers/add', 'CenterController@addForm')->name('addCenterForm');
Route::post('/centers/add', 'CenterController@add')->name('addCenter');
/** Edit */
Route::get('/centers/edit/{center}', 'CenterController@editForm')->name('editCenterForm');
Route::get('/centers/edit', function () {
    return redirect()->back();
});
Route::post('/centers/edit', 'CenterController@edit')->name('editCenter');
/** Delete */
Route::get('/centers/delete/{center}', 'CenterController@deleteForm')->name('deleteCenterForm');
Route::get('/centers/delete', function () {
    return redirect()->back();
});
Route::post('/centers/delete', 'CenterController@delete')->name('deleteCenter');

/** Users */
Route::get('/users', 'UserController@load')->name('users');
Route::get('/users/user/{user}', 'UserController@view')->name('user');
/** Add */
Route::get('/users/add', 'UserController@addForm')->name('addUserForm');
Route::post('/users/add', 'UserController@add')->name('addUser');
/** Edit */
Route::get('/users/edit/{user}', 'UserController@editForm')->name('editUserForm');
Route::get('/users/edit', function () {
    return redirect()->back();
});
Route::post('/users/edit', 'UserController@edit')->name('editUser');
/** Deactivat */
Route::get('/users/deactivate/{user}', 'UserController@activationForm')->name('deactivateUserForm');
Route::get('/users/deactivate', function () {
    return redirect()->back();
});
Route::post('/users/deactivate', 'UserController@deactivate')->name('deactivateUser');
/** Activate */
Route::get('/users/activate/{user}', 'UserController@activationForm')->name('activateUserForm');
Route::get('/users/activate', function () {
    return redirect()->back();
});
Route::post('/users/activate', 'UserController@activate')->name('activateUser');

/** Groups */
Route::get('/groups', 'GroupController@load')->name('groups');
Route::get('/groups/group/{group}', 'GroupController@view')->name('group');
/** Add */
Route::get('/groups/add', 'GroupController@addForm')->name('addGroupForm');
Route::post('/groups/add', 'GroupController@add')->name('addGroup');
/** Edit */
Route::get('/groups/edit/{group}', 'GroupController@editForm')->name('editGroupForm');
Route::get('/groups/edit', function () {
    return redirect()->back();
});
Route::post('/groups/edit', 'GroupController@edit')->name('editGroup');
/** Delete */
Route::get('/groups/delete/{group}', 'GroupController@deleteForm')->name('deleteGroupForm');
Route::get('/groups/delete', function () {
    return redirect()->back();
});
Route::post('/groups/delete', 'GroupController@delete')->name('deleteGroup');

/** Settings */
Route::get('/settings/{setting}', 'SettingsController@load')->name('settings');
/** Edit Avatar */
Route::post('/settings/avatar/edit', 'SettingsController@editAvatar')->name('editAvatar');
/** Edit Info */
Route::post('/settings/info/edit', 'SettingsController@editInfo')->name('editInfo');
/** Edit Phone */
Route::post('/settings/phone/edit', 'SettingsController@editPhone')->name('editPhone');
/** Edit Email */
Route::post('/settings/email/edit', 'SettingsController@editEmail')->name('editEmail');
/** Resend Code */
Route::get('/settings/{settings}/verify/resend', 'SettingsController@resendCode')->name('resendCode');
/** Verify Code */
Route::post('/settings/{settings}/verify', 'SettingsController@verifyCode')->name('verifyCode');
Route::get('/settings/{settings}/verify/cancel', 'SettingsController@cancelVerification')->name('cancelVerification');
/** Edit Password */
Route::post('/settings/password/edit', 'SettingsController@editPassword')->name('editPassword');
