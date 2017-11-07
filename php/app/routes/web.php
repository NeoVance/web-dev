<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;
use App\User;

$router->get('/', [ 'as' => 'login', function () use ($router) {
    return view('head', [
        'content' => view('index')
    ]);
}]);

$router->get('dashboard', function () {
    return view('head', [
        'content' => view('dashboard')
    ]);
});

$router->get('chessboard/{id:[\d]+}', function ($id) {
    return view('head', [
        'content' => view('chessboard', [ 'game_id' => $id ])
    ]);
});

$router->post('register', function (Request $request) use ($router) {
    try {
        $this->validate($request, [
          'name' => 'required|unique:users',
          'email' => 'required|email|unique:users',
          'password' => 'required'
        ]);
        $user = new User();// User::create($request->all());
        $info = $request->all();
        $user->name = $info['name'];
        $user->email = $info['email'];
        $user->password = $info['password'];
        $user->save();
        return redirect()->to('/', 301, $request->header(), true);
    } catch(\Illuminate\Validation\ValidationException $validation) {
        return $validation->response;
    } catch (\Exception $e) {
        return response()->json([
            'error' => [
                'message' => $e->getMessage()
            ]
        ]);
    }
});

$router->get('register', function () {
    return view('head', [
        'content' => view('register')
    ]);
});

$router->post('api/login', function (Request $request) {
    try {
        $user = User::where('name', $request->all()['name'])->get();
        if (!$user->isEmpty()) {
            $user = $user->first();
            $password = $request->all()['password'];
            if ($password == $user->password) {
                $token = 'token'.rand();
                $user->auth_token = $token;
                $user->save();
                return response()->json([
                    'token' => $token,
                ]);
            }
            
            throw new \Exception('Unauthorized.');
        }
        
        throw new \Exception('Not a valid user.');
    } catch(\Exception $e) {
        return response()->json([
            'error' => [
                'message' => $e->getMessage()
            ]
        ], 401);
    }
});

$router->group([
    'prefix' => 'api/state',
    'middleware' => 'auth'
], function () use ($router) {
    $router->get('/', 'StateController@index');
    $router->get('/{id:[\d]+}', [
        'as' => 'api.state.show',
        'uses' => 'StateController@show'
    ]);
});

$router->group([
    'prefix' => 'api/games',
    'middleware' => 'auth'
], function () use ($router) {
    $router->get('/', 'GameController@index');
    $router->get('/{id:[\d]+}/state', 'StateController@showGame');
    $router->get('/{id:[\d]+}', [
        'as' => 'api.games.show',
        'uses' => 'GameController@show'
    ]);
    $router->post('/', 'GameController@store');
    $router->post('/{id:[\d]+}/state', 'StateController@store');
    $router->put('/{id:[\d]+}', 'GameController@update');
    $router->put(
        '/{id:[\d]+}/state/{stateid:[\d]+}',
        'StateController@update'
    );
    $router->delete('/{id:[\d]+}', 'GameController@destroy');
    $router->delete(
        '/{id:[\d]+}/state/{stateid:[\d]+}',
        'StateController@destroy'
    );
});
