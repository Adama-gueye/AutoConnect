<?php

namespace Tests\Feature;

use App\Http\Controllers\AnnonceController;
use App\Mail\AnnonceAccepter;
use App\Mail\AnnonceRejeter;
use App\Models\Annonce;
use App\Models\Bloc;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TestUser extends TestCase
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
        $response = $this->json('GET','/api/annonceValides');

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

    //AjoutAnnonce
    public function test_add_annonce(): void
    {
        $prop = User::factory()->proprietaire()->create();

        $this->actingAs($prop);

        $annonce = Annonce::factory()->make(); // Utilisez make() pour obtenir une instance non persistante

        $response = $this->json('POST', 'api/annonceStore', $annonce->toArray());

        $response->assertStatus(201);

    }
//valider ou invalider une annonce
    public function test_index(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $annonce = Annonce::factory()->make();

        $response = $this->json('PATCH', 'api/updateEtataAnnonce' . $annonce->id);

        $response->assertStatus(201);
    }



//duplication d'un email
    public function test_duplicate_email()
    {
        $email = 'd@gmail.com';

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
            'email' => 'ngom@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', 'api/auth/login', [
            'email' => 'ngom@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }
    // Autres méthodes et relations...



    public function test_ajout_message(): void
    {
        $acheteur = User::factory()->acheteur()->create();

        $this->actingAs($acheteur);
        $messageData = [
            'message' => 'cvbn',
            'email' => 'bb@gmail.com',
            'nomComplet' => 'Adama Gueye',
            'user_id' => $acheteur->id,
        ];

        $response = $this->json('POST', 'api/messageStore', $messageData);

        $this->assertEquals(201, $response->getStatusCode());

    }
    

    public function test_newsLetter(): void
    {
        $Data = [
            'email' => 'n@gmail.com',
        ];

        $response = $this->json('POST', 'api/newsLetterStore', $Data);

        $this->assertEquals(201, $response->getStatusCode());

    }
        public function test_signalement(): void
    {
        $acheteur = User::factory()->acheteur()->create();
        $this->actingAs($acheteur);

        // Créez une catégorie
      //  $categorie = Categorie::factory()->create();

        // Créez une annonce avec la catégorie créée
        $annonce = Annonce::factory()->create();

        $signalement = [
            'description' => 'slm slm',
            'user_id' => $acheteur->id,
        ];

        $response = $this->json('POST', 'api/signalementStore' . $annonce->id, $signalement);

        $this->assertEquals(201, $response->getStatusCode());
    }

//ajout annonce

//Supprimer une bloc

    public function test_suppression_bloc()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $bloc = Bloc::factory()->create();

        $responseSuppression = $this->json('DELETE', "api/blocDestroy{$bloc->id}");

        $responseSuppression->assertStatus(200);

        $this->assertDatabaseMissing('blocs', ['id' => $bloc]);
    }


        public function test_supprime_annonce()
    {
        $prop = User::factory()->proprietaire()->create();
        $annonce = Annonce::factory()->create(['user_id' => $prop->id]);

        $this->actingAs($prop);

        if($annonce->user_id === $prop->id){
            $response = $this->json('DELETE', 'api/annonceDestroy' . $annonce->id);

            $response->assertStatus(200);

            $this->assertDatabaseMissing('annonces', ['id' => $annonce->id]);
        }

        
    }
//ajout commentaire

//listeBloc

//véhiculeParCategorie

}
