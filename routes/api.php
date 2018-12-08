<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

use Illuminate\Support\Facades\Validator;
use App\Game;

Route::get('/games', function (Request $request) {
    return json_encode( Game::orderBy('created_at', 'DESC')->get() );
});

Route::get('/games/{game}', function (Request $request, $game) {
    return json_encode( Game::where('id', $game)->first() );
});

function validateGame($data, $update = null) {

    $validation = [
        'title' => 'required|string',
        'cover' => 'required|string'
    ];

    if (isset($update)) $validation['id'] = 'required|numeric|exists:games,id';

    $validator = Validator::make($data, $validation);

    return $validator;
}

Route::post('/games/save', function (Request $request) {

    $data = $request->all();

    $validator = validateGame($data);

    if ($validator->fails()) {

        $errors = (Array) $validator->errors()->getMessages();

        $errors['global'] = [
            'type'=>'Validation Error',
            'message'=>'Some validation errors detected'
        ];

        return json_encode( (Object) [ 'errors' =>  (Object) $errors ] );

    } else {

        $game = Game::create([
            'title' => $data['title'],
            'cover' => $data['cover']
        ]);

        if(!$game) Game::abort(500, 'Error: Failed to save Game');
        else return json_encode((Object) $game->getAttributes());
    }
});

Route::put('/games/update', function (Request $request) {

    $data = $request->all();

    $validator = validateGame($data, true);

    if ($validator->fails()) {

        $errors = (Array) $validator->errors()->getMessages();

        $errors['global'] = [
            'type'=>'Validation Error',
            'message'=>'Some validation errors detected'
        ];

        return json_encode( (Object) [ 'errors' =>  (Object) $errors ] );

    } else {

        $game = Game::where('id', $data['id'])->first();

        if (empty($game)) {

            $errors = [
                'global' => [
                    'type'=>'Validation Error',
                    'message'=>'Some validation errors detected'
                ]
            ];

            return json_encode( (Object) [ 'errors' =>  (Object) $errors ] );

        } else {

            $_game = Game::find($data['id']);

            if ($game->title != $data['title']) $_game->title = $data['title'];

            if ($game->cover != $data['cover']) $_game->cover = $data['cover'];

            $_game->save();

            if(!$_game) Game::abort(500, 'Error: Failed to save Game');
            else return json_encode((Object) $_game->getAttributes());

        }
    }
});

Route::delete('/games/{game}', function (Request $request, $id) {

    $game = Game::where('id', $id)->first();

    if (empty($game)) {

        $errors = [
            'global' => [
                'type'=>'Validation Error',
                'message'=>'Some validation errors detected'
            ]
        ];

        return json_encode( (Object) [ 'errors' =>  (Object) $errors ] );

    } else {

        $_game = Game::find($id);

        $_game->delete();

        if(!$_game) Game::abort(500, 'Error: Failed to save Game');
        else return json_encode((Object) $game->getAttributes());
    }
});
