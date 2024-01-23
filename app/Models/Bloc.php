<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloc extends Model
{
    use HasFactory;
    protected $fillable = ['image', 'titre', 'description'];

}
