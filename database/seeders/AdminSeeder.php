<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nom'=>'admin',
                'prenom'=>'admin User',
                'adresse'=>'Guediawaye',
                'email'=>'admin@gmail.com',
                'telephone'=>'772576767',
                'password'=> bcrypt('123456'),
                'role'=> 'admin',
            ],
            [
                'nom'=>'proprietaire',
                'prenom'=>'proprietaire User',
                'adresse'=>'Guediawaye',
                'email'=>'proprietaire@gmail.com',
                'telephone'=>'772576766',
                'password'=> bcrypt('123456'),
                'role'=> 'proprietaire',
            ],
            [
                'nom'=>'acheteur',
                'prenom'=>'acheteur User',
                'adresse'=>'Guediawaye',
                'email'=>'acheteur@gmail.com',
                'telephone'=>'772576765',
                'password'=> bcrypt('123456'),
                'role'=> 'acheteur',
            ],
        ];
    
        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
