<?php

namespace Tests\Feature;

use App\Http\Controllers\AnnonceController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class Annonce extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
//listes des annonces valide
    public function test_annonces(): void
    {
        $response = $this->get('/api/annonceValides');

        $response->assertStatus(200);
    }
//liste des annonces invalide
    public function test_annoncesInvalides(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->json('GET', 'api/annonceInvalides');

        $response->assertStatus(200);
    }
//valider ou invalider une annonce
    public function test_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);
    
        $annonce = User::factory()->create();
        $response = $this->json('PATCH', 'api/index' . $annonce->id);

        $response->assertStatus(201);
    }


//duplication d'un email
    public function test_duplicate_email()
    {
        $email = 'isseu@gmail.com';

        $user1 = User::factory()->acheteur()->create([
            'email' => $email,
        ]);

        $user2 = User::factory()->acheteur()->make([
            'email' => $email,
        ]);

        $response = $this->json('POST', 'api/register', $user2->toArray());

        $response->assertStatus(401);
    }
//connexion d'un utilisateur
    public function test_user_login(): void
    {
        $user = User::factory()->create([
            'email' => 'die@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', 'api/auth/login', [
            'email' => 'die@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }
//AJout une annonce
    public function test_ajout_annonce(): void
    {
        $prop = User::factory()->proprietaire()->create();
        $this->actingAs($prop);

        $annonce = User::factory()->create();

        $response = $this->json('POST', 'api/annonceStore', $annonce->toArray());

        $response->assertStatus(201);
    }

    //ajout une commentaire
    public function test_ajout_commentaire(): void
    {
        $acheteur = User::factory()->acheteur()->create();
    
            $annonce = User::factory()->create();

        $this->actingAs($acheteur);
        $commentaireData = [
            'commentaire' => 'cvbn',
            'user_id' => $acheteur->id,
        ];

        $response = $this->json('POST', 'api/commentaireStore' . $annonce->id, $commentaireData); 
        $this->assertEquals(201, $response->getStatusCode());
    }


    public function test_ajout_message(): void
    {
        $acheteur = User::factory()->acheteur()->create();

        $this->actingAs($acheteur);
        $messageData = [
            'message' => 'cvbn',
            'user_id' => $acheteur->id,
        ];

        $response = $this->json('POST', 'api/messageStoreAcheteur', $messageData);

        $this->assertEquals(201, $response->getStatusCode());

    }

    public function test_newsLetter(): void
    {
        $Data = [
            'email' => 'wawa@gmail.com',
        ];

        $response = $this->json('POST', 'api/newsLetterStore', $Data);

        $this->assertEquals(201, $response->getStatusCode());

    }
    public function test_signalement(): void
    {
        $acheteur = User::factory()->acheteur()->create();
    
        $annonce = User::factory()->create();

        $this->actingAs($acheteur);

        $signalement = [
            'description' => 'slm slm',
            'user_id' => $acheteur->id,
        ];

        $response = $this->json('POST', 'api/signalementStore'.$annonce->id, $signalement);

        $this->assertEquals(201, $response->getStatusCode());

    }
//Supprimer une bloc

    public function test_supprime_bloc() {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->json('DELETE', 'api/blocDestroy1');

        $response->assertStatus(200);

    }

    public function test_supprime_annonce() {
        $prop = User::factory()->proprietaire()->create();
    
        $annonce = User::factory()->create();
        $this->actingAs($prop);

        if($annonce->user_id === $prop->id){
            $response = $this->json('DELETE', 'api/annonceDestroy'.$annonce->id);

            $response->assertStatus(200);
         }else 
         echo "non";

    }

}
