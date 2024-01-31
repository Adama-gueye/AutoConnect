<?php
namespace Database\Factories;

use App\Models\Annonce;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnonceFactory extends Factory
{
    protected $model = Annonce::class;

    
        public function definition(): array
    {
        // Créez une catégorie
        $categorie = Categorie::factory()->create();

        return [
            'nom' => $this->faker->word,
            'marque' => $this->faker->word,
            'couleur' => $this->faker->word,
            'image' => $this->faker->imageUrl(),
            'prix' => $this->faker->numberBetween(1000, 5000),
            'description' => $this->faker->paragraph,
            'carburant' => $this->faker->word,
            'nbrePlace' => $this->faker->numberBetween(1, 7),
            'localisation' => $this->faker->address,
            'moteur' => $this->faker->word,
            'annee' => 2024,
            'kilometrage' => $this->faker->word,
            'transmission' => $this->faker->word,
            'climatisation' => "Oui",
            'etat' => 'accepter', // Changer l'état initial à 'accepter'
            'categorie_id' => $categorie->id,
            'user_id' => User::factory()->proprietaire()->create()->id,
        ];
    }
}
