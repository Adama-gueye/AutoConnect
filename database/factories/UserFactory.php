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
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'telephone' => $this->faker->phoneNumber,
            'adresse' => $this->faker->address,
           // 'role' => 'admin', // Assurez-vous d'ajuster le rôle en conséquence
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

    
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
