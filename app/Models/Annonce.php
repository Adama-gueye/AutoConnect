<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    public function categorie() 
    {
        return $this -> belongsTo(Categorie::class);
    }
    public function user() 
    {
        return $this -> belongsTo(User::class);
    }
    public function commentaire() 
    {
        return $this -> hasMany(Commentaire::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    protected $fillable = [
        'nom', 'marque', 'couleur', 'image', 'prix', 'description', 
        'nbrePlace', 'localisation', 'moteur', 'annee', 'carburant', 
        'carosserie', 'transmission', 'etat', 'user_id', 'categorie_id'
    ];


    public static function create(array $attributes = [])
    {
        return parent::query()->create($attributes);
    }

}
