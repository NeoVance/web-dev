<?php
namespace App\Http\Controllers;

use App\Game;
use App\State;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StateController extends Controller
{
    function index() {
        return State::all();
    }
    
    function show($id) {
        return $this->getModel(State::class, $id);
    }
    
    function showGame($id) {
        // id = game_id
        $game = $this->getModel(Game::class, $id);
        
        if (!($game instanceof Game)) {
            return $game;
        }
        
        try {
            return State::where([
                'game_id' => $game->id
            ])->get();
        } catch (\Exception $e) {
            return $this->putError($e);
        }
    }
    
    function store(Request $request, $id) {
        try {
            $game = Game::findOrFail($id);
            
            $this->allowUserColor(
                $game,
                $request->input('api_token'),
                $request->input('color')
            );
            
            $state = new State;
            $state->type = $request->input('type');
            $state->color = $request->input('color');
            $state->xposition = $request->input('xposition');
            $state->yposition = $request->input('yposition');
            $state->game_id = $game->id;
            $state->save();
            
            return response()->json(
                ['created' => true],
                201,
                ['Location' => route('api.state.show', ['id' => $state->id])]
            );
        } catch (\Exception $e) {
            return response()->json([
                'created' => false,
                'exception' => get_class($e)
            ], 400);
        }
    }
    
    function update(Request $request, $id, $stateid) {
        // id = game_id
        try {
            $game = $this->getModel(Game::class, $id);
            $state = $this->getModel(State::class, $stateid);
            
            $this->allowUserColor(
                $game,
                $request->input('api_token'),
                $state->color
            );
            
            if ($state instanceof State) {
                if ($game instanceof Game) {
                    $game->turn = $game->turn + 1;
                    $game->save();
                } else {
                    return $game;
                }
                
                $state->fill($request->all());
                $state->save();
            }
            
            return $state;
        } catch (\Exception $e) {
            $this->putError($e);
        }
    }
    
    function destroy($id, $stateid) {
        // id = game_id
        $state = $this->getModel(State::class, $stateid);
        $game = $this->getModel(Game::class, $id);
        
        $taking = State::where([
            'xposition' => $state->xposition,
            'yposition' => $state->yposition,
            'game_id' => $game->id
        ])
            ->where('color', '!=', $state->color)
            ->get()
            ->first();
            
        if (!($taking instanceof State)) {
            return response(null, 406);
        }
        
        if ($game instanceof Game) {
            if ($state instanceof State) {
                $state->delete();
                return response(null, 204);
            }
            
            return $state;
        } else {
            return $game;
        }
    }
}