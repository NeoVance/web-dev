<?php
namespace Tests\App\Http\Controllers;

use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class GameControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     * @test
     */
    public function test_index_should_return_status_200()
    {
        $token = $this->user->auth_token;
        
        $this->get(
            '/api/games?api_token='.$token
        )->seeStatusCode(200);
    }
    
    public function test_index_should_return_collection()
    {
        $token = $this->user->auth_token;
        
        $this->get('/api/games?api_token='.$token);
        
        foreach($this->games as $game) {
            $this->seeJson([
                'title' => $game->title
            ]);
        }
    }
    
    public function test_show_should_return_a_valid_game()
    {
        $token = $this->user->auth_token;
        $game = $this->games->first();
        
        $this->get(
            "/api/games/{$game->id}?api_token={$token}"
        )
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $game->id,
                'title' => $game->title,
                'turn' => $game->turn
            ]);
            
        $data = json_decode($this->response->getContent(), true);
        
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }
    
    public function test_show_should_fail_when_game_does_not_exist()
    {  
        $token = $this->user->auth_token;
        $this->get('/api/games/999999?api_token='.$token)
            ->seeStatusCode(404)
            ->seeJson([
                'error' => [
                    'message' => 'Game not found.'
                ]
            ]);
    }
    
    public function test_show_route_should_not_match_invalid_route()
    {
        $token = $this->user->auth_token;
        $this->get(
            '/api/games/invalid-route?api_token='.$token
        );
        
        $this->assertNotRegExp(
            '/Game not found./',
            $this->response->getContent(),
            'GameController@show route is matching when it should not.'
        );
    }
    
    public function test_store_should_save_new_game_in_database()
    {
        $this->post('/api/games', [
            'title' => 'My special new game.',
            'api_token' => $this->user->auth_token
        ]);
        
        $this->seeJson([ 'created' => true ])
            ->seeInDatabase('games', ['title' => 'My special new game.']);
    }
    
    public function test_store_should_respond_with_201_and_location()
    {
        $this->post('/api/games', [
            'title' => 'My next special game.',
            'api_token' => $this->user->auth_token
        ]);
        
        $this->seeStatusCode(201)
            ->seeHeaderWithRegExp('Location', '#/api/games/[\d]+$#');
    }
    
    public function test_update_should_change_database_values()
    {
        $this->notSeeInDatabase('games', [
            'title' => 'My extra awesome game.'
        ]);
        
        $this->put('/api/games/1', [
            'title' => 'My extra awesome game.',
            'api_token' => $this->user->auth_token
        ]);
        
        $this->seeInDatabase('games', [
            'title' => 'My extra awesome game.'
        ]);
    }
    
    public function test_update_should_only_change_fillable_fields()
    {
        $game = $this->games->first();
        
        $this->seeInDatabase('games', [
            'title' => $game->title
        ]);
        
        $this->put("/api/games/{$game->id}", [
            'title' => 'Updated awesome game.',
            'turn' => 1,
            'api_token' => $this->user->auth_token
        ]);
        
        $this->seeStatusCode(200)
            ->seeJson([
                'id' => $game->id,
                'title' => 'Updated awesome game.',
                'turn' => $game->turn,
            ]);
    }
    
    public function test_update_should_fail_with_invalid_id()
    {
        $token = $this->user->auth_token;
        $this->put('/api/games/999999', [
            "api_token" => $token
        ])
            ->seeStatusCode(404)
            ->seeJsonEquals([
                'error' => [
                    'message' => 'Game not found.'
                ]
            ]);
    }
    
    public function test_update_should_not_match_an_invalid_route()
    {
        $this->put('/api/games/invalid-route', [
            'api_token' => $this->user->auth_token
        ])
            ->seeStatusCode(404);
    }
    
    public function test_destroy_should_remove_a_valid_game()
    {
        $game = $this->games->first();
        
        $this->delete("/api/games/{$game->id}", [
            'api_token' => $this->user->auth_token
        ])
            ->seeStatusCode(204)
            ->isEmpty();
            
        $this->notSeeInDatabase('games', ['id' => $game->id]);
    }
    
    public function test_destroy_should_fail_with_invalid_id()
    {
        $this->delete('/api/games/999999', [
            'api_token' => $this->user->auth_token
        ])
            ->seeStatusCode(404)
            ->seeJsonEquals([
                'error' => [
                    'message' => 'Game not found.'
                ]
            ]);
    }
    
    public function test_destroy_should_fail_with_invalid_route()
    {
        $this->delete('/api/games/invalid-route', [
            'api_token' => $this->user->auth_token
        ])
            ->seeStatusCode(404);
    }
}
