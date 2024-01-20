<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class User extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // public function test_login()
    // {
    //     $response = $this->json('POST', 'api/auth/login', [
    //         'email' => 'adamagu99@gmail.com',
    //         'password' => 'Ada20865',
    //     ]);
    
    //     $response->assertStatus(201);

    //  //  return $this->respondWithToken($token);
    // }
    // public function test_login()
    // {
    //     $user = User::factory()->create([
    //         'email' => 'adamagu99@gmail.com',
    //         'password' => Hash::make('Ada20865'),
    //     ]);

    //     $response = $this->json('POST', 'api/auth/login', [
    //         'email' => 'adamagu99@gmail.com',
    //         'password' => 'Ada20865',
    //     ]);

    //     $response->assertStatus(200);
    // }


    

}
