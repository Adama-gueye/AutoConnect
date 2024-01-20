<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
        
    public function definition(): array
    {
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
            'transmission' => $this->faker->word,
            'etat' => $this->faker->randomElement(['accepter', 'refuser']),
            'user_id' => User::factory()->proprietaire(),
            'categorie_id' => Categorie::factory(),
        ];
    }
    

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'nom' => $this->faker->lastName,
                'prenom' => $this->faker->firstName,
                'email' => $this->faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => 'admin',
            ];
        });
    }

    public function acheteur()
    {
        return $this->state(function (array $attributes) {
            return [
                'nom' => $this->faker->lastName,
                'prenom' => $this->faker->firstName,
                'email' => $this->faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'telephone' => $this->faker->phoneNumber,
                'adresse' => $this->faker->address,
                'role' => 'acheteur',
            ];
        });
    }

    public function proprietaire()
    {
        return $this->state(function (array $attributes) {
            return [
                'nom' => $this->faker->lastName,
                'prenom' => $this->faker->firstName,
                'email' => $this->faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'telephone' => $this->faker->phoneNumber,
                'adresse' => $this->faker->address,
                'adresse' => $this->faker->sentence,
                'role' => 'proprietaire',
            ];
        });
    }

    public function annonce()
    {
        return $this->state(function (array $attributes) {
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
                'transmission' =>$this->faker->word,
                'etat' => $this->faker->randomElement('refuser'),
                'categorie_id' => 1,
            ];
        });
    }

    
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
