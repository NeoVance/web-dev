<?php
namespace App\Http\Controllers;

use App\Game;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GameController extends Controller
{
    
    public function index()
    {
        return Game::all();
    }
    
    public function show($id)
    {
        try {
            return Game::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Game not found.'
                ]
            ], 404);
        }
    }
    
    private function pawnRow($row, $color) {
        $rowdata = [ 'color' => $color ];
        $pawns = [];
        for($i = 0; $i <= 7; $i++) {
            array_push(
                $pawns,
                array_merge([$i, $row], $rowdata)
            );
        }
        return $pawns;
    }
    
    private function generateNewGame(Game $game, $filename = false) {
        if (!$filename) {
            $info = [
                'rook' => [
                    [0, 0, 'color' => 'black'],
                    [7, 0, 'color' => 'black'],
                    [0, 7, 'color' => 'white'],
                    [7, 7, 'color' => 'white']
                ],
                'horse' => [
                    [1, 0, 'color' => 'black'],
                    [6, 0, 'color' => 'black'],
                    [1, 7, 'color' => 'white'],
                    [6, 7, 'color' => 'white']
                ],
                'bishop' => [
                    [2, 0, 'color' => 'black'],
                    [5, 0, 'color' => 'black'],
                    [2, 7, 'color' => 'white'],
                    [5, 7, 'color' => 'white']
                ],
                'queen' => [
                    [3, 0, 'color' => 'black'],
                    [3, 7, 'color' => 'white']
                ],
                'king' => [
                    [4, 0, 'color' => 'black'],
                    [4, 7, 'color' => 'white']
                ],
                'pawn' => array_merge(
                    $this->pawnRow(1, 'black'),
                    $this->pawnRow(6, 'white')
                ),
            ];
        } else {
            $info = json_decode(file_get_contents($filename), true);
        }
        
        foreach($info as $type => $states) {
            foreach($states as $state) {
                $this->createState($game, [
                    'type' => $type,
                    'color' => $state['color'],
                    'xposition' => $state[0],
                    'yposition' => $state[1]
                ]);
            }
        }
    }
    
    private function createState(Game $game, $stateData) {
        $stateData['game_id'] = $game->id;
        $state = $this->newState($stateData);
        $state->save();
    }
    
    public function store(Request $request)
    {
        $user = User::where(['auth_token' => $request->input('api_token')])
            ->get()
            ->first();
            
        try {
            $game = Game::create($request->all());
            $this->generateNewGame($game);
            $user->games()->attach($game->id, ['color' => 'black']);
        } catch (\Exception $e) {
            return response()->json([
                'created' => false,
                'exception' => get_class($e)
            ], 400);
        }
        
        return response()->json(
            ['created' => true],
            201,
            ['Location' => route('api.games.show', ['id' => $game->id])]
        );
    }
    
    public function update(Request $request, $id)
    {
        try {
            $game = Game::findOrFail($id);
            
            $this->allowUser($game, $request->input('api_token'));
            
            $game->fill($request->all());
            $game->save();
            return $game;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Game not found.'
                ]
            ], 404);
        } catch (\Exception $e) {
            return $this->putError($e);
        }
    }
    
    /**
     * DELETE
     */
    public function destroy(Request $request, $id) {
        try {
            $game = Game::findOrFail($id);
            
            $this->allowUser($game, $request->input('api_token'));
            
            $game->delete();
            
            return response(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Game not found.'
                ]
            ], 404);
        } catch (\Exception $e) {
            $this->putError($e);
        }
    }
    
}
