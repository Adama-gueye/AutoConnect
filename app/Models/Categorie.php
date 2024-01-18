<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Categorie extends Model
{
    use HasFactory;
    use HasApiTokens;

    public function annonces() 
    {
        return $this -> hasMany(Annonce::class);
    }
}
