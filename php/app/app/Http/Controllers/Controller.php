<?php

namespace App\Http\Controllers;

use App\Game;
use App\State;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Controller extends BaseController
{
    //
    function putError(\Exception $e) {
        return response()->json([
            'error' => [
                'message' => 'Something went wrong.',
                'exception' => get_class($e)
            ]
        ], 400);
    }
    
    function getModel($model, $id) {
        try {
            $item = $model::findOrFail($id);
            return $item;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => "{$model} not found."
                ]
            ], 404);
        }
    }
    
    function newState($stateData = []) {
        // [
        //    color => value,
        //    type,
        //    ...
        // ]
        
        $newState = new State();
        foreach($stateData as $column => $value) {
            $newState->{$column} = $value;
        }
        
        return $newState;
    }
    
    function allowUser(Game $userGame, $authToken) {
        $allowed = false;
            
        foreach($userGame->users as $user) {
            if ($user->auth_token == $authToken) {
                $allowed = true;
            }
        }
        
        if (!$allowed) {
            throw new \Exception('This is not your game.');
        }
    }
    
    function allowUserColor(Game $userGame, $authToken, $color) {
        $this->allowUser($userGame, $authToken);
        
        $user = $userGame->users()
            ->where('auth_token', $authToken)
            ->get()
            ->first();
            
        if ($color != $user->pivot->color) {
            throw new \Exception('That is not your piece');
        }
    }
}
